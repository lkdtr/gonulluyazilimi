<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsentEvent extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = ['contact_id', 'channel', 'granted', 'source', 'user_id', 'ip', 'user_agent'];

    protected $casts = [
        'granted' => 'boolean',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    public function actor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
