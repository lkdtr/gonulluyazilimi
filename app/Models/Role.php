<?php

namespace App\Models;

use App\Models\Concerns\Auditable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\DB;

/**
 * A named set of permissions. Accounts get roles directly (role_user) or
 * through the affiliations of their contact (affiliation_type_role).
 */
class Role extends Model
{
    use Auditable;

    public const OWNER = 'owner';

    public const MANAGER = 'manager';

    protected $fillable = ['key', 'name', 'description'];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    public static function findByKey(string $key): self
    {
        return static::where('key', $key)->firstOrFail();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function affiliationTypes(): BelongsToMany
    {
        return $this->belongsToMany(AffiliationType::class);
    }

    public function isOwner(): bool
    {
        return $this->key === self::OWNER;
    }

    /**
     * @return string[]
     */
    public function permissions(): array
    {
        return DB::table('permission_role')->where('role_id', $this->id)->orderBy('permission')->pluck('permission')->all();
    }

    /**
     * @param  string[]  $permissions
     */
    public function syncPermissions(array $permissions): void
    {
        $old = DB::table('permission_role')->where('role_id', $this->id)->orderBy('permission')->pluck('permission')->all();
        $new = array_values(array_unique($permissions));
        sort($new);

        DB::transaction(function () use ($permissions) {
            DB::table('permission_role')->where('role_id', $this->id)->delete();
            DB::table('permission_role')->insert(array_map(
                fn (string $permission) => ['role_id' => $this->id, 'permission' => $permission],
                array_values(array_unique($permissions))
            ));
        });

        if ($old !== $new) {
            \App\Support\Audit::record('change', $this, ['permissions' => [implode(', ', $old), implode(', ', $new)]], $this->auditDescription('yetkileri değişti'));
        }
    }

    public function auditLabel(): string
    {
        return (string) $this->name;
    }
}
