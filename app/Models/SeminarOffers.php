<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SeminarOffers extends Model
{
    protected $fillable = ['user_id', 'seminar_subject_id', 'seminar_subject_proposal_id', 'summary', 'target_audience', 'seminar_type', 'duration', 'availability_start_date', 'availability_end_date', 'cities', 'technical_requirements', 'biography', 'reference_links', 'notes', 'status'];
    protected $casts = ['availability_start_date' => 'date', 'availability_end_date' => 'date'];

    public function user() { return $this->belongsTo(User::class); }
    public function seminarSubject() { return $this->belongsTo(SeminarSubjects::class); }
    public function seminarSubjectProposal() { return $this->belongsTo(SeminarSubjectProposals::class); }
}
