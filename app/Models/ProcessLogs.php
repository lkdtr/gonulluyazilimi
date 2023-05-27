<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProcessLogs extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'process_logs';
    protected $primaryKey = 'id';
}
