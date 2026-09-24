<?php

namespace App\Models;

use App\Models\Concerns\Auditable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A kind of relationship a contact can have with the association (volunteer,
 * member, board member...). Types are data: the association adds its own;
 * only the system ones are referenced by code.
 */
class AffiliationType extends Model
{
    use Auditable;

    public const VOLUNTEER = 'volunteer';

    public const MEMBER = 'member';

    protected $fillable = ['key', 'name', 'description', 'has_term', 'sort'];

    protected $casts = [
        'is_system' => 'boolean',
        'has_term' => 'boolean',
    ];

    public static function findByKey(string $key): self
    {
        return static::where('key', $key)->firstOrFail();
    }

    /**
     * Role template: accounts whose contact holds this affiliation get
     * these roles while it lasts.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function affiliations(): HasMany
    {
        return $this->hasMany(ContactAffiliation::class);
    }

    public function auditLabel(): string
    {
        return (string) $this->name;
    }
}
