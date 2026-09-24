<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Proof that a person accepted a given version of an agreement.
 */
class AgreementAcceptance extends Model
{
    public const UPDATED_AT = null;

    public const CREATED_AT = null;

    protected $fillable = ['agreement_version_id', 'user_id', 'contact_id', 'context', 'ip', 'user_agent', 'accepted_at'];

    protected $casts = [
        'accepted_at' => 'datetime',
    ];

    public function version(): BelongsTo
    {
        return $this->belongsTo(AgreementVersion::class, 'agreement_version_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class)->withTrashed();
    }
}
