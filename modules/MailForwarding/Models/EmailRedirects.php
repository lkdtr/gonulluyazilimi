<?php

namespace Modules\MailForwarding\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailRedirects extends Model
{
    use HasFactory;

    protected $table = 'email_redirects';
    protected $primaryKey = 'id';

    protected static function booted(): void
    {
        // The domain follows the alias (name.surname@domain).
        static::saving(function (EmailRedirects $redirect) {
            if ($redirect->isDirty('email_alias') && ($at = strrpos((string) $redirect->email_alias, '@')) !== false) {
                $redirect->domain = strtolower(substr($redirect->email_alias, $at + 1));
            }
        });
    }

    protected $fillable = [
        'user_id',
        'email_alias',
        'domain',
        'email_forwarding',
        'status',
    ];
}
