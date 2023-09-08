<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Mail;

use App\Mail\PenguenWelcome;

use App\Models\User;
use App\Models\EmailRedirects;

class AdminController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(function ($request, $next) {

            $this->middleware('auth');

            if(!Auth::check() ) {
                return redirect('/login')->with('redirect', URL::full() );
            }

            if( (Auth::user()->role!=1 ) && (Auth::user()->role!=2 ) ) {
                return redirect('/login')->with('redirect', URL::full() );
            }

            return $next($request);
        });
    }

    public function users() {

        $users = User::where("status", 1)->get();

        return view('admin.users', ["users" => $users]);
    }

    public function setManagerRole($user_id) {

        if (Auth::user()->role!=1 ) {
            return Redirect::to(secure_url('/users'))->with("danger-status", trans("panel.unauthorized_process"));
        }

        $user = User::where("id", $user_id)->first();
        if($user==null) {
            $this->set_log("other", "Kullanıcı yok");
        }
        else {
            $user->role = 2;
            if($user->save()) {
                $this->set_log("change", $user->name." ".$user->surname. " kullanıcısının rolü yönetici yapıldı");
                return Redirect::to(secure_url('/users'))->with("success-status", trans("panel.set_manager_role_success"));
            }

            return Redirect::to(secure_url('/users'))->with("danger-status", trans("panel.set_manager_role_failed"));
        }

    }

    public function setOwnerRole($user_id) {

        if (Auth::user()->role!=1 ) {
            return Redirect::to(secure_url('/users'))->with("danger-status", trans("panel.unauthorized_process"));
        }

        $user = User::where("id", $user_id)->first();
        if($user==null) {
            $this->set_log("other", "Kullanıcı yok");
        }
        else {
            $user->role = 1;
            if($user->save()) {
                $this->set_log("change", $user->name." ".$user->surname. " kullanıcısının rolü sahip yapıldı");
                return Redirect::to(secure_url('/users'))->with("success-status", trans("panel.set_owner_role_success"));
            }

            return Redirect::to(secure_url('/users'))->with("danger-status", trans("panel.set_owner_role_failed"));
        }

    }

    public function setUserRole($user_id) {

        if (Auth::user()->role!=1 ) {
            return Redirect::to(secure_url('/users'))->with("danger-status", trans("panel.unauthorized_process"));
        }

        $user = User::where("id", $user_id)->first();
        if($user==null) {
            $this->set_log("other", "Kullanıcı yok");
        }
        else {
            $user->role = 3;
            if($user->save()) {
                $this->set_log("change", $user->name." ".$user->surname. " kullanıcısının rolü kullanıcı yapıldı");
                return Redirect::to(secure_url('/users'))->with("success-status", trans("panel.set_user_role_success"));
            }

            return Redirect::to(secure_url('/users'))->with("danger-status", trans("panel.set_user_role_failed"));
        }

    }

    public function sendPenguenWelcome($user_id) {

        if (Auth::user()->role!=1 ) {
            return Redirect::to(secure_url('/users'))->with("danger-status", trans("panel.unauthorized_process"));
        }

        $user = User::where("id", $user_id)->first();
        if($user==null) {
            $this->set_log("other", "Kullanıcı yok");
        }
        else {
            $email_redirects = EmailRedirects::where("user_id", $user_id)->first();
            if($email_redirects!=null) {
                $user->alias = $email_redirects->email_alias;
                Mail::to($email_redirects->email_alias)->send(new PenguenWelcome($user));
                $this->set_log("other", $email_redirects->email_alias." adresine yönlendirme başarılı e-postası gönderildi");
                return Redirect::to(secure_url('/users'))->with("success-status", trans("panel.send_penguen_welcome_success"));
            }
        }
        return Redirect::to(secure_url('/users'))->with("danger-status", trans("panel.send_penguen_welcome_failed"));
    }

    public function removePenguen($user_id) {

         if (Auth::user()->role!=1 ) {
            return Redirect::to(secure_url('/users'))->with("danger-status", trans("panel.unauthorized_process"));
        }

        $user = User::where("id", $user_id)->first();
        if($user==null) {
            $this->set_log("other", "Kullanıcı yok");
        }
        else {
            $email_redirect = EmailRedirects::where("user_id", $user_id)->first();
            if($email_redirect!=null) {
                $email_redirect->status = 0;
                $email_redirect->save();
                $this->set_log("other", $email_redirect->email_alias." adresi devre dışı bırakıldı");
                return Redirect::to(secure_url('/users'))->with("success-status", trans("panel.remove_penguen_success"));
            }
            else {
                return Redirect::to(secure_url('/users'))->with("danger-status", trans("panel.remove_penguen_failed"));
            }
        }

    }

}
