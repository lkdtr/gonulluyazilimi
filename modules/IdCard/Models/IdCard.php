<?php

namespace Modules\IdCard\Models;

use App\Models\Concerns\Auditable;

use App\Models\Contact;
use App\Models\ContactAffiliation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A virtual ID card issued for one affiliation of a contact.
 */
class IdCard extends Model
{
    use Auditable;

    public const VALID = 'valid';

    public const REVOKED = 'revoked';

    /** The affiliation (membership, volunteering, term) has ended. */
    public const ENDED = 'ended';

    /** Cards of this type are switched off. */
    public const INACTIVE = 'inactive';

    protected $fillable = [
        'id_card_template_id',
        'contact_id',
        'contact_affiliation_id',
        'serial',
        'number',
        'verify_token',
    ];

    protected $casts = [
        'revoked_at' => 'datetime',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(IdCardTemplate::class, 'id_card_template_id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class)->withTrashed();
    }

    public function affiliation(): BelongsTo
    {
        return $this->belongsTo(ContactAffiliation::class, 'contact_affiliation_id');
    }

    public static function newVerifyToken(): string
    {
        return bin2hex(random_bytes(20));
    }

    public function status(): string
    {
        return match (true) {
            $this->revoked_at !== null => self::REVOKED,
            ! $this->template->is_active => self::INACTIVE,
            ! $this->affiliation->isActive() || $this->contact->trashed() => self::ENDED,
            default => self::VALID,
        };
    }

    public function isValid(): bool
    {
        return $this->status() === self::VALID;
    }

    public function renewVerifyToken(): void
    {
        $this->forceFill(['verify_token' => self::newVerifyToken()])->save();
    }

    public function auditLabel(): string
    {
        return (string) $this->number;
    }
}
