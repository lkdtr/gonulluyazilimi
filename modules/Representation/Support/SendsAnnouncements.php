<?php

namespace Modules\Representation\Support;

use Illuminate\Support\Facades\Mail;

trait SendsAnnouncements
{
 protected function send($a){try{Mail::to($a->representation->mailing_list_address)->send(new \App\Mail\AnnouncementMailing((object)['subject'=>$a->subject,'content'=>$a->detail]));$a->update(['status'=>'sent','sent_at'=>now()]);}catch(\Throwable $e){$a->update(['status'=>'failed','failure_reason'=>$e->getMessage()]);}}
}
