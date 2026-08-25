<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeminarSubjectProposals extends Model
{
    protected $fillable = ['subject', 'normalized_subject', 'status', 'accepted_by', 'accepted_at'];
    protected $casts = ['accepted_at' => 'datetime'];
}
