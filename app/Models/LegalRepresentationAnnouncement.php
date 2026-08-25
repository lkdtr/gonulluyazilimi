<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class LegalRepresentationAnnouncement extends Model {protected $fillable=['legal_representation_id','created_by','subject','detail','status','approved_by','sent_at','failure_reason'];public function representation(){return $this->belongsTo(LegalRepresentation::class,'legal_representation_id');}}
