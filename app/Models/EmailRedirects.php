<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailRedirects extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'email_redirects';
    protected $primaryKey = 'id';
}
