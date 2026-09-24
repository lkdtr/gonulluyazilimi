<?php

namespace Modules\Membership\Models;

use App\Models\Concerns\Auditable;
use App\Models\Contact;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Membership extends Model
{
    use Auditable;

    public const APPLICANT = 'applicant';

    public const ACTIVE = 'active';

    public const SUSPENDED = 'suspended';

    public const LEFT = 'left';

    public const REJECTED = 'rejected';

    public const STATUSES = [
        self::APPLICANT => 'Başvuru',
        self::ACTIVE => 'Üye',
        self::SUSPENDED => 'Askıda',
        self::LEFT => 'Ayrıldı',
        self::REJECTED => 'Başvurusu reddedildi',
    ];

    public const STATUS_COLORS = [
        self::APPLICANT => 'yellow',
        self::ACTIVE => 'green',
        self::SUSPENDED => 'orange',
        self::LEFT => 'secondary',
        self::REJECTED => 'red',
    ];

    protected $fillable = ['contact_id', 'number', 'status', 'applied_at', 'joined_at', 'left_at', 'derbis_registered', 'notes'];

    protected $attributes = [
        'status' => self::ACTIVE,
    ];

    protected $casts = [
        'applied_at' => 'date',
        'joined_at' => 'date',
        'left_at' => 'date',
        'derbis_registered' => 'boolean',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class)->withTrashed();
    }

    public function events(): HasMany
    {
        return $this->hasMany(MembershipEvent::class)->orderByDesc('occurred_on')->orderByDesc('id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::ACTIVE);
    }

    public function isActive(): bool
    {
        return $this->status === self::ACTIVE;
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function auditLabel(): string
    {
        return trim(($this->number ? $this->number.' — ' : '').($this->contact?->display_name ?? ''));
    }
}
