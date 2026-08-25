<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeminarRequests extends Model
{
    use HasFactory;

    protected $table = 'seminar_requests';
    protected $primaryKey = 'id';

    protected $casts = [
        'seminar_date' => 'date',
        'seminar_start_date' => 'date',
        'seminar_end_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function seminarSubject()
    {
        return $this->belongsTo(SeminarSubjects::class);
    }

    public function organizationRecord()
    {
        return $this->belongsTo(Organizations::class, 'organization_id');
    }
}
