<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserEvents extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'user_events';
    protected $primaryKey = 'id';

}
