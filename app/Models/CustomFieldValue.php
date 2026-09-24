<?php

namespace App\Models;

use App\Models\Concerns\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomFieldValue extends Model
{
    use Auditable;

    protected $fillable = ['contact_id', 'custom_field_id', 'value'];

    public function field(): BelongsTo
    {
        return $this->belongsTo(CustomField::class, 'custom_field_id');
    }

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function auditLabel(): string
    {
        return ($this->field?->label ?? '').' — '.($this->contact?->display_name ?? '');
    }
}
