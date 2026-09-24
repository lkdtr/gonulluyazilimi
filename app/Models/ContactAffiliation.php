<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One period in which a contact held an affiliation type. A new period is a
 * new row, so history (e.g. successive board terms) is kept.
 */
class ContactAffiliation extends Model
{
    protected $fillable = ['contact_id', 'affiliation_type_id', 'title', 'started_at', 'ended_at', 'note'];

    protected $casts = [
        'started_at' => 'date',
        'ended_at' => 'date',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(AffiliationType::class, 'affiliation_type_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where(fn (Builder $query) => $query->whereNull('ended_at')->orWhereDate('ended_at', '>', today()));
    }

    public function scopeOfType(Builder $query, string $key): Builder
    {
        return $query->whereHas('type', fn (Builder $query) => $query->where('key', $key));
    }

    public function isActive(): bool
    {
        return $this->ended_at === null || $this->ended_at->gt(today());
    }
}
