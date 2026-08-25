<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class LegalRepresentationVolunteer extends Model {protected $fillable=['legal_representation_id','user_id','contact_consent','notified_at'];protected $casts=['contact_consent'=>'boolean','notified_at'=>'datetime'];public function representation(){return $this->belongsTo(LegalRepresentation::class,'legal_representation_id');}public function user(){return $this->belongsTo(User::class);}}
