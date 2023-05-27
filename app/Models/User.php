<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'surname',
        'national_id',
        'email',
        'phone_number',
        'password',
        'phone_number_verified_at',
        'agreement_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'phone_number_verified_at' => 'datetime',
        'agreement_at' => 'datetime',
    ];

    public function getEmailRedirects() {
        $emailRedirect = $this->hasOne('App\Models\EmailRedirects', 'id', 'user_id')->first();
        if($emailRedirect==null) {
            $res = ["email_alias"=>""];
            return (object) $res;
        }
        else {
            return $emailRedirect;
        }
    }
}
