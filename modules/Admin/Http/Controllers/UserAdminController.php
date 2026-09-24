<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

use App\Models\User;
use BahriCanli\TcKimlik;

class UserAdminController extends Controller
{
    public function users() {

        $users = User::where("status", 1)->get();

        return view('admin::users', ["users" => $users]);
    }

    public function setManagerRole($user_id) {

        if (! Auth::user()->isOwner()) {
            return Redirect::route('admin.users')->with("danger-status", trans("panel.unauthorized_process"));
        }

        $user = User::where("id", $user_id)->first();
        if($user==null) {
            $this->set_log("other", "Kullanıcı yok");
        }
        else {
            $user->role = 2;
            if($user->save()) {
                $this->set_log("change", $user->name." ".$user->surname. " kullanıcısının rolü yönetici yapıldı");
                return Redirect::route('admin.users')->with("success-status", trans("panel.set_manager_role_success"));
            }

            return Redirect::route('admin.users')->with("danger-status", trans("panel.set_manager_role_failed"));
        }

    }

    public function setOwnerRole($user_id) {

        if (! Auth::user()->isOwner()) {
            return Redirect::route('admin.users')->with("danger-status", trans("panel.unauthorized_process"));
        }

        $user = User::where("id", $user_id)->first();
        if($user==null) {
            $this->set_log("other", "Kullanıcı yok");
        }
        else {
            $user->role = 1;
            if($user->save()) {
                $this->set_log("change", $user->name." ".$user->surname. " kullanıcısının rolü sahip yapıldı");
                return Redirect::route('admin.users')->with("success-status", trans("panel.set_owner_role_success"));
            }

            return Redirect::route('admin.users')->with("danger-status", trans("panel.set_owner_role_failed"));
        }

    }

    public function setUserRole($user_id) {

        if (! Auth::user()->isOwner()) {
            return Redirect::route('admin.users')->with("danger-status", trans("panel.unauthorized_process"));
        }

        $user = User::where("id", $user_id)->first();
        if($user==null) {
            $this->set_log("other", "Kullanıcı yok");
        }
        else {
            $user->role = 3;
            if($user->save()) {
                $this->set_log("change", $user->name." ".$user->surname. " kullanıcısının rolü kullanıcı yapıldı");
                return Redirect::route('admin.users')->with("success-status", trans("panel.set_user_role_success"));
            }

            return Redirect::route('admin.users')->with("danger-status", trans("panel.set_user_role_failed"));
        }

    }

    public function removeUser($user_id) {

        if (! Auth::user()->isOwner()) {
            return Redirect::route('admin.users')->with("danger-status", trans("panel.unauthorized_process"));
        }

        $user = User::where("id", $user_id)->first();
        if($user==null) {
            $this->set_log("other", "Kullanıcı yok");
        }
        else {
            $last_removed_user = User::selectRaw('*, SUBSTR(users.email, 5) AS email_sort')
                                        ->where("email", "LIKE", "XXXX%")
                                        ->orderByRaw("cast(email_sort as unsigned) DESC")
                                        ->first();
            if($last_removed_user==null) $last_removed_user = new User();
            $last_id = intval($last_removed_user->email_sort) + 1;

            $user->name = "XXXX$last_id";
            $user->surname = "XXXX$last_id";
            $user->email = "XXXX$last_id";
            $user->status = 0;

            if($user->save()) {
                return Redirect::route('admin.users')->with("success-status", trans("panel.remove_user_success"));
            }
            else {
                return Redirect::route('admin.users')->with("danger-status", trans("panel.remove_user_failed"));
            }
        }

    }

    public function tcKimlikDogrula($user_id) {

        $user = User::where("id", $user_id)->first();
        if($user==null) {
            return Redirect::route('admin.users')->with("danger-status", trans("panel.unauthorized_process"));
        }

        $birth_year = "";
        if(!empty($user->birthday)) {
            $birth_year = date("Y", strtotime($user->birthday));
        }

        $request_data = [
            'tcno' => $user->national_id,
            'isim' => $this->tr_ucwords($user->name),
            'soyisim' => $this->tr_ucwords($user->surname),
            'dogumyili' => $birth_year,
        ];

        try {
            $result = TcKimlik::validate($request_data);
        } catch (\Throwable) {
            return Redirect::route('admin.users')->with('danger-status', 'TC Kimlik doğrulama servisine ulaşılamadı.');
        }

        return Redirect::route('admin.users')->with(
            $result ? 'success-status' : 'danger-status',
            $result ? 'TC Kimlik doğrulaması başarılı.' : 'TC Kimlik doğrulaması başarısız.'
        );
    }

}
