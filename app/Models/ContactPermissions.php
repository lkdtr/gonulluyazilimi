<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactPermissions extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'contact_permissions';
    protected $primaryKey = 'id';

}
