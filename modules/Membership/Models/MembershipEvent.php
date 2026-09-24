<?php

namespace Modules\Membership\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MembershipEvent extends Model
{
    public const TYPES = [
        'applied' => 'Başvuru',
        'joined' => 'Üyelik başladı',
        'suspended' => 'Üyelik askıya alındı',
        'reactivated' => 'Üyelik yeniden başladı',
        'left' => 'Üyelikten ayrıldı',
        'rejected' => 'Başvuru reddedildi',
        'number_changed' => 'Üye no değişti',
        'note' => 'Not',
    ];

    protected $fillable = ['membership_id', 'type', 'occurred_on', 'extra_months', 'note', 'user_id'];

    protected $casts = [
        'occurred_on' => 'date',
    ];

    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function label(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }
}
