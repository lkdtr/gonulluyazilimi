<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organizations extends Model
{
    use HasFactory;

    protected $table = 'organizations';

    protected $fillable = [
        'name',
        'normalized_name',
    ];

    public function seminarRequests()
    {
        return $this->hasMany(SeminarRequests::class, 'organization_id');
    }
}
