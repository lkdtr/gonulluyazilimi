<?php

namespace Modules\LkdYoung\Support;

use Illuminate\Support\Facades\Mail;
use Modules\LkdYoung\Models\LkdYoungApplication;
use Modules\MailForwarding\Models\EmailRedirects;

trait InteractsWithRepresentatives
{
 protected function sendAnnouncement($a,$rep){try{Mail::to($rep->mailing_list_address)->send(new \App\Mail\AnnouncementMailing((object)['subject'=>$a->subject,'content'=>$a->detail]));$a->update(['status'=>'sent','sent_at'=>now(),'recipient_count'=>LkdYoungApplication::where('university_id',$a->university_id)->where('contact_consent',true)->count()+1]);}catch(\Throwable $e){$a->update(['status'=>'failed','failure_reason'=>$e->getMessage()]);}}
 protected function activeAlias($u){return EmailRedirects::where('user_id',$u->id)->where('status',1)->first();}
}
