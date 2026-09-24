<?php

namespace App\Models;

use App\Models\Concerns\Auditable;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AgreementVersion extends Model
{
    use Auditable;

    protected $fillable = ['agreement_id', 'version', 'content'];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public function agreement(): BelongsTo
    {
        return $this->belongsTo(Agreement::class);
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function acceptances(): HasMany
    {
        return $this->hasMany(AgreementAcceptance::class);
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null;
    }

    public function auditLabel(): string
    {
        return ($this->agreement?->title ?? '').' sürüm '.$this->version;
    }
}
