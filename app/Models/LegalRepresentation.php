<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class LegalRepresentation extends Model {protected $fillable=['city','representative_name','email','address','user_id','can_send_announcements','mailing_list_address','status'];protected $casts=['status'=>'boolean','can_send_announcements'=>'boolean'];public function user(){return $this->belongsTo(User::class);}}
