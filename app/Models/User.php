<?php

namespace App\Models;

use App\Models\Concerns\Auditable;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use Auditable, HasApiTokens, HasFactory, Notifiable;

    private ?Collection $effectiveRoles = null;

    private ?array $permissions = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'surname',
        'national_id',
        'email',
        'phone_number',
        'password',
        'phone_number_verified_at',
        'agreement_at',
        'birthday',
        'city_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_number_verified_at' => 'datetime',
        'agreement_at' => 'datetime',
        'birthday' => 'date',
    ];

    protected static function booted()
    {
        // Until the profile fields move to contacts, the account stays the
        // source of truth and its contact mirrors it.
        static::saved(function (User $user) {
            $legacyRoleChanged = $user->wasRecentlyCreated || $user->wasChanged('role');

            $user->syncContact();

            if ($legacyRoleChanged) {
                $user->syncLegacyRole();
            }
        });
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Roles given to the account directly.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Direct roles plus the roles templated on the active affiliations of
     * the account's contact.
     */
    public function effectiveRoles(): Collection
    {
        return $this->effectiveRoles ??= Role::query()
            ->whereIn('id', DB::table('role_user')->where('user_id', $this->id)->select('role_id'))
            ->orWhereIn('id', DB::table('affiliation_type_role')
                ->join('contact_affiliations', 'contact_affiliations.affiliation_type_id', '=', 'affiliation_type_role.affiliation_type_id')
                ->where('contact_affiliations.contact_id', $this->contact_id ?? 0)
                ->where(fn ($query) => $query->whereNull('contact_affiliations.ended_at')->orWhereDate('contact_affiliations.ended_at', '>', today()))
                ->select('affiliation_type_role.role_id'))
            ->get();
    }

    public function hasRole(string $key): bool
    {
        return $this->effectiveRoles()->contains('key', $key);
    }

    public function isOwner(): bool
    {
        return $this->hasRole(Role::OWNER);
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isOwner()) {
            return true;
        }

        $this->permissions ??= DB::table('permission_role')->whereIn('role_id', $this->effectiveRoles()->pluck('id'))->pluck('permission')->unique()->all();

        return in_array($permission, $this->permissions, true);
    }

    /**
     * Legacy access level the older screens check: 1 owner, 2 manager,
     * 3 anyone else.
     */
    public function accessLevel(): int
    {
        return match (true) {
            $this->isOwner() => 1,
            $this->hasRole(Role::MANAGER) => 2,
            default => 3,
        };
    }

    /**
     * Allowed when the legacy level or any permission in the list matches;
     * an empty list allows every signed-in user.
     *
     * @param  array<int|string>  $access  legacy levels (1, 2) and permission keys
     */
    public function canAccess(array $access): bool
    {
        if ($access === []) {
            return true;
        }

        foreach ($access as $entry) {
            if (is_int($entry) ? $entry === $this->accessLevel() : $this->hasPermission($entry)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Set the account's direct level: 1 owner, 2 manager, 3 none.
     */
    public function setAccessLevel(int $level): void
    {
        $this->role = $level;
        $this->save();
    }

    /**
     * The legacy users.role column still decides the direct owner/manager
     * role, so older code and the column stay in step until it is dropped.
     * Other roles of the account are left alone.
     */
    private function syncLegacyRole(): void
    {
        $system = Role::whereIn('key', [Role::OWNER, Role::MANAGER])->pluck('id', 'key');

        $this->roles()->detach($system->values());
        if (in_array((int) $this->role, [1, 2], true)) {
            $this->roles()->attach($system[(int) $this->role === 1 ? Role::OWNER : Role::MANAGER]);
        }

        $this->flushAccess();
    }

    public function flushAccess(): void
    {
        $this->effectiveRoles = null;
        $this->permissions = null;
    }

    public function syncContact(): Contact
    {
        $contact = $this->contact ?? new Contact(['type' => Contact::TYPE_PERSON]);
        // Read before saveQuietly() below resets the change set.
        $membershipChanged = $this->wasRecentlyCreated || $this->wasChanged('lkd_user_id');

        $contact->fill([
            'first_name' => $this->name,
            'last_name' => $this->surname,
            'identity_number' => $this->national_id,
            'email' => $this->email,
            'phone' => $this->phone_number,
            'birthday' => $this->birthday,
            'city_id' => $this->city_id ?: null,
        ]);

        if (! $contact->exists || $contact->isDirty()) {
            $contact->save();
        }

        if ($this->contact_id !== $contact->id) {
            $this->contact()->associate($contact);
            $this->saveQuietly();
        }

        // The LKD member number still decides membership until the
        // membership module takes over.
        if ($membershipChanged) {
            $this->lkd_user_id > 0
                ? $contact->affiliate(AffiliationType::MEMBER)
                : $contact->endAffiliation(AffiliationType::MEMBER);
        }

        return $contact;
    }

    public function getValidation() {
        $contactPermission = $this->hasOne('App\Models\ContactPermissions', 'value', 'phone_number')
                        ->where('value_type', 'phone_number')->first();
        if($contactPermission==null) {
            $res = ["verified"=>"", "verification_code" => ""];
            return (object) $res;
        }
        else {
            return $contactPermission;
        }
    }

    public function getCity() {
        if($this->city_id==0) {
            $res = ["city_name"=>""];
            return (object) $res;
        }
        return $this->hasOne('App\Models\Cities', 'id', 'city_id')->first();
    }

    public function auditLabel(): string
    {
        return (string) $this->email;
    }
}
