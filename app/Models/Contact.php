<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * A person or organization known to the association. It exists on its own:
 * a contact may be a member, donor or volunteer without ever having an
 * account, and an account (User) always points to its contact.
 */
class Contact extends Model
{
    use SoftDeletes;

    public const TYPE_PERSON = 'person';

    public const TYPE_ORGANIZATION = 'organization';

    protected $fillable = [
        'type',
        'first_name',
        'last_name',
        'organization_name',
        'identity_number',
        'email',
        'phone',
        'birthday',
        'city_id',
    ];

    protected $attributes = [
        'type' => self::TYPE_PERSON,
    ];

    protected $casts = [
        'birthday' => 'date',
    ];

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }

    /**
     * A contact can hold several affiliations at once (member, volunteer,
     * board member...); ended ones stay as history.
     */
    public function affiliations(): HasMany
    {
        return $this->hasMany(ContactAffiliation::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(ContactPhoto::class);
    }

    /**
     * The photo shown for the contact: the approved one, if any.
     */
    public function approvedPhoto(): HasOne
    {
        return $this->hasOne(ContactPhoto::class)->ofMany(['id' => 'max'], fn ($query) => $query->approved());
    }

    /**
     * The latest upload waiting for review, or the latest rejection.
     */
    public function latestPhotoUpload(): HasOne
    {
        return $this->hasOne(ContactPhoto::class)->ofMany(['id' => 'max'], fn ($query) => $query->where('status', '!=', ContactPhoto::APPROVED));
    }

    public function hasAffiliation(string $key): bool
    {
        return $this->affiliations()->active()->ofType($key)->exists();
    }

    /**
     * Start an affiliation unless the contact already holds it.
     */
    public function affiliate(string $key, array $attributes = []): ContactAffiliation
    {
        $active = $this->affiliations()->active()->ofType($key)->first();

        return $active ?? $this->affiliations()->create($attributes + [
            'affiliation_type_id' => AffiliationType::findByKey($key)->id,
            'started_at' => today(),
        ]);
    }

    /**
     * End the contact's active affiliations of the given type.
     */
    public function endAffiliation(string $key): void
    {
        $this->affiliations()->active()->ofType($key)->get()
            ->each(fn (ContactAffiliation $affiliation) => $affiliation->update(['ended_at' => today()]));
    }

    public function isOrganization(): bool
    {
        return $this->type === self::TYPE_ORGANIZATION;
    }

    public function getDisplayNameAttribute(): string
    {
        if ($this->isOrganization()) {
            return (string) $this->organization_name;
        }

        return trim($this->first_name.' '.$this->last_name);
    }
}
