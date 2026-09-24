<?php

namespace Modules\IdCard\Models;

use App\Models\Concerns\Auditable;

use App\Models\AffiliationType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * Card design and rules for one affiliation type.
 */
class IdCardTemplate extends Model
{
    use Auditable;

    /** Fields a card has room for besides the name. */
    public const MAX_FIELDS = 4;

    public const LOGO_DIRECTORY = 'id-card-logos';

    protected $fillable = [
        'affiliation_type_id',
        'name',
        'organization_name',
        'is_active',
        'requires_photo',
        'background_color',
        'text_color',
        'accent_color',
        'number_prefix',
        'number_digits',
        'fields',
        'footer_text',
    ];

    protected $attributes = [
        'is_active' => true,
        'requires_photo' => true,
        'background_color' => '#ffffff',
        'text_color' => '#1d273b',
        'accent_color' => '#206bc4',
        'number_prefix' => '',
        'number_digits' => 6,
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'requires_photo' => 'boolean',
        'number_digits' => 'integer',
        'fields' => 'array',
    ];

    protected static function booted(): void
    {
        static::deleted(fn (IdCardTemplate $template) => $template->logo_path && Storage::disk('local')->delete($template->logo_path));
    }

    public function affiliationType(): BelongsTo
    {
        return $this->belongsTo(AffiliationType::class);
    }

    public function cards(): HasMany
    {
        return $this->hasMany(IdCard::class);
    }

    public function formatNumber(int $serial): string
    {
        return $this->number_prefix.str_pad((string) $serial, $this->number_digits, '0', STR_PAD_LEFT);
    }

    public function auditLabel(): string
    {
        return (string) $this->name;
    }
}
