<?php

namespace Modules\MailForwarding\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Modules\MailForwarding\Mail\ForwardingWelcome;
use Modules\MailForwarding\Models\EmailRedirects;

class AdminForwardingController extends Controller
{
    public function sendWelcome($user_id) {

        $user = User::where("id", $user_id)->first();
        if($user==null) {
            $this->set_log("other", "Kullanıcı yok");
        }
        else {
            $email_redirects = EmailRedirects::where("user_id", $user_id)->first();
            if($email_redirects!=null) {
                $user->alias = $email_redirects->email_alias;
                Mail::to($email_redirects->email_alias)->send(new ForwardingWelcome($user));
                $this->set_log("other", $email_redirects->email_alias." adresine yönlendirme başarılı e-postası gönderildi");
                return Redirect::to(secure_url('/users'))->with("success-status", trans("panel.send_penguen_welcome_success"));
            }
        }
        return Redirect::to(secure_url('/users'))->with("danger-status", trans("panel.send_penguen_welcome_failed"));
    }

    public function remove($user_id) {

        $user = User::where("id", $user_id)->first();
        if($user==null) {
            $this->set_log("other", "Kullanıcı yok");
        }
        else {
            $email_redirect = EmailRedirects::where("user_id", $user_id)->first();
            if($email_redirect!=null) {
                $email_redirect->status = 0;
                $email_redirect->email_forwarding = $user->email;
                $email_redirect->save();
                $this->set_log("other", $email_redirect->email_alias." adresi devre dışı bırakıldı");
                return Redirect::to(secure_url('/users'))->with("success-status", trans("panel.remove_penguen_success"));
            }
            else {
                return Redirect::to(secure_url('/users'))->with("danger-status", trans("panel.remove_penguen_failed"));
            }
        }

        return Redirect::to(secure_url('/users'))->with("danger-status", trans("panel.remove_penguen_failed"));
    }
}
