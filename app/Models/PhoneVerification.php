<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Phone number verification codes sent by SMS or WhatsApp (formerly the
 * contact_permissions table; it never held communication consents).
 */
class PhoneVerification extends Model
{
    use HasFactory;

    protected $table = 'phone_verifications';
    protected $primaryKey = 'id';

    protected $fillable = [
        'value',
        'value_type',
        'verification_code',
        'verification_code_expires_at',
        'verification_attempts',
        'verified',
        'verified_at',
        'status',
    ];

    protected $casts = [
        'verified' => 'boolean',
        'verified_at' => 'datetime',
        'verification_code_expires_at' => 'datetime',
    ];

}
