<?php

namespace App\Models;

use App\Models\Concerns\Auditable;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * A profile photo of a contact, shown only once a manager approves it.
 */
class ContactPhoto extends Model
{
    use Auditable;

    public const PENDING = 'pending';

    public const APPROVED = 'approved';

    public const REJECTED = 'rejected';

    /** Directory on the private "local" disk. */
    public const DIRECTORY = 'contact-photos';

    protected $fillable = ['contact_id', 'path', 'status'];

    protected $attributes = [
        'status' => self::PENDING,
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::deleted(fn (ContactPhoto $photo) => Storage::disk('local')->delete($photo->path));
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::PENDING);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', self::APPROVED);
    }

    public function isPending(): bool
    {
        return $this->status === self::PENDING;
    }

    /**
     * Approve the photo and drop the contact's other photos, so a contact has
     * at most one approved photo and no stale files.
     */
    public function approve(User $reviewer): void
    {
        $this->forceFill(['status' => self::APPROVED, 'reviewed_by' => $reviewer->id, 'reviewed_at' => now(), 'rejection_reason' => null])->save();

        $this->contact->photos()->whereKeyNot($this->id)->get()->each->delete();
    }

    /**
     * Reject the photo; the file is removed, the record stays so the person
     * sees why.
     */
    public function reject(User $reviewer, ?string $reason = null): void
    {
        Storage::disk('local')->delete($this->path);

        $this->forceFill(['status' => self::REJECTED, 'reviewed_by' => $reviewer->id, 'reviewed_at' => now(), 'rejection_reason' => $reason])->save();
    }

    public function auditLabel(): string
    {
        return (string) $this->contact?->display_name;
    }
}
