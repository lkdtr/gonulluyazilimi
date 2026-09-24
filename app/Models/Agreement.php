<?php

namespace App\Models;

use App\Models\Concerns\Auditable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * An agreement people accept, e.g. the privacy policy. Its text lives in
 * versions; the latest published one is in force.
 */
class Agreement extends Model
{
    use Auditable;

    /** Privacy policy (KVKK), accepted on registration. */
    public const PRIVACY = 'kvkk';

    /** Terms of the association email address (forwarding). */
    public const EMAIL_USAGE = 'email-usage';

    protected $fillable = ['key', 'title', 'description'];

    public function versions(): HasMany
    {
        return $this->hasMany(AgreementVersion::class)->orderByDesc('version');
    }

    public function currentVersion(): HasOne
    {
        return $this->hasOne(AgreementVersion::class)->ofMany(['version' => 'max'], fn ($query) => $query->whereNotNull('published_at'));
    }

    public function draft(): HasOne
    {
        return $this->hasOne(AgreementVersion::class)->whereNull('published_at');
    }

    public static function findByKey(string $key): ?self
    {
        return static::where('key', $key)->first();
    }

    public function auditLabel(): string
    {
        return (string) $this->title;
    }
}
