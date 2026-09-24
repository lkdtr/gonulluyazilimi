<?php

namespace Modules\MailForwarding\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Modules\MailForwarding\Mail\ForwardingWelcome;
use Modules\MailForwarding\Models\EmailRedirects;

class ForwardingController extends Controller
{
    public function sendWelcome($user_id) {

        $user = User::where("id", $user_id)->first();
        if($user==null) {
            $this->set_log("other", "Kullanıcı yok");
        }
        else {
            $active = EmailRedirects::where("user_id", $user_id)->where('status', 1)->get();
            foreach ($active as $email_redirects) {
                $user->alias = $email_redirects->email_alias;
                Mail::to($email_redirects->email_alias)->send(new ForwardingWelcome($user));
                $this->set_log("other", $email_redirects->email_alias." adresine yönlendirme başarılı e-postası gönderildi");
            }
            if ($active->isNotEmpty()) {
                return Redirect::route('admin.users')->with("success-status", trans("panel.send_penguen_welcome_success"));
            }
        }
        return Redirect::route('admin.users')->with("danger-status", trans("panel.send_penguen_welcome_failed"));
    }

    public function remove($user_id) {

        $user = User::where("id", $user_id)->first();
        if($user==null) {
            $this->set_log("other", "Kullanıcı yok");
        }
        else {
            $active = EmailRedirects::where("user_id", $user_id)->where('status', 1)->get();
            foreach ($active as $email_redirect) {
                $email_redirect->status = 0;
                $email_redirect->email_forwarding = $user->email;
                $email_redirect->save();
                $this->set_log("other", $email_redirect->email_alias." adresi devre dışı bırakıldı");
            }
            if ($active->isNotEmpty()) {
                return Redirect::route('admin.users')->with("success-status", trans("panel.remove_penguen_success"));
            }
            else {
                return Redirect::route('admin.users')->with("danger-status", trans("panel.remove_penguen_failed"));
            }
        }

        return Redirect::route('admin.users')->with("danger-status", trans("panel.remove_penguen_failed"));
    }
}
