<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * A field an association defines for its contacts, shown on the contact page
 * and, depending on member_access, on the person's profile.
 */
class CustomField extends Model
{
    use Auditable;

    public const TYPES = [
        'text' => 'Kısa metin',
        'textarea' => 'Uzun metin',
        'number' => 'Sayı',
        'date' => 'Tarih',
        'select' => 'Seçenek listesi',
        'checkbox' => 'Evet / hayır',
        'email' => 'E-posta',
        'url' => 'Web adresi',
        'phone' => 'Telefon',
    ];

    public const ACCESS = [
        'hidden' => 'Yalnız yönetim görür',
        'visible' => 'Kişi görür, değiştiremez',
        'editable' => 'Kişi görür ve değiştirir',
    ];

    public const APPLIES_TO = [
        'person' => 'Kişi',
        'organization' => 'Kurum',
        'both' => 'Kişi ve kurum',
    ];

    protected $fillable = ['key', 'label', 'group', 'type', 'options', 'help', 'is_required', 'member_access', 'applies_to', 'is_active', 'sort'];

    protected $attributes = [
        'group' => 'personal',
        'type' => 'text',
        'member_access' => 'hidden',
        'applies_to' => 'person',
        'is_active' => true,
        'sort' => 100,
    ];

    protected $casts = [
        'options' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
        'sort' => 'integer',
    ];

    protected static function booted(): void
    {
        $refresh = fn () => app(\App\Modules\ContactFields::class)->refresh();
        static::saved($refresh);
        static::deleted($refresh);
    }

    public function values(): HasMany
    {
        return $this->hasMany(CustomFieldValue::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeFor(Builder $query, Contact $contact): Builder
    {
        return $query->whereIn('applies_to', ['both', $contact->isOrganization() ? 'organization' : 'person']);
    }

    /**
     * Value as shown to people (checkbox and date formatted).
     */
    public function display(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return match ($this->type) {
            'checkbox' => $value === '1' ? 'Evet' : 'Hayır',
            'date' => \Illuminate\Support\Carbon::parse($value)->format('d.m.Y'),
            default => $value,
        };
    }

    public function auditLabel(): string
    {
        return (string) $this->label;
    }
}
