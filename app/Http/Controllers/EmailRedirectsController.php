<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

use App\Mail\PenguenWelcome;

use App\Models\User;
use App\Models\EmailRedirects;

use Epigra\TcKimlik;
use Carbon\Carbon;

use App\Traits\PostfixAdmin;

class EmailRedirectsController extends Controller
{
    use PostfixAdmin;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getValidation()
    {
        $user_id = Auth::id();
        $user = User::where("id", $user_id)->first();
        $email_redirects = EmailRedirects::where("user_id", $user_id)->first();
        if($email_redirects==null) $email_redirects = new EmailRedirects();
        $user->birthday = date("d-m-Y", strtotime($user->birthday));

        return view('email-redirects', [
            "user" => $user,
            "email_redirects" => $email_redirects,
        ]);
    }

    public function postValidation(Request $request)
    {
        $validator = $request->validate([
            'name' => ['required', 'string', 'max:255', 'min:3'],
            'surname' => ['required', 'string', 'max:255', 'min:2'],
            'national_id' => ['required', 'string', 'max:11', 'tckimlik'],
            'birthday' => ['required'],
            'agreement' => ['required']
        ]);

        $name = $request->get("name");
        $surname = $request->get("surname");
        $national_id = $request->get("national_id");
        $birthday = $request->get("birthday");

        $birty_year = date("Y", strtotime($birthday));

        $data = [
            'tcno'          => $national_id,
            'isim'          => $name,
            'soyisim'       => $surname,
            'dogumyili'     => $birty_year,
        ];

        if (!TcKimlik::validate($data)) {
            return back()->withErrors(["national_id" => "TC Kimlik Numarası vermiş olduğunuz kimlik bilgilerinizle eşleşmiyor"])->withInput();
        }

        $user_id = Auth::id();
        $user = User::where("id", $user_id)->first();
        $user->birthday = Carbon::parse($birthday)->format("Y-m-d");
        $user->name = $this->tr_ucwords($name);
        $user->surname = $this->tr_ucwords($surname);
        $user->save();

        $user->name = $this->slug(mb_strtolower($user->name));
        $user->surname = $this->slug(mb_strtolower($user->surname));

        $email_redirects = EmailRedirects::where("user_id", $user_id)->first();
        if($email_redirects==null) {
            $email_redirects = new EmailRedirects();
            $email_redirects->user_id = $user->id;
            $email_redirects->email_forwarding = $user->email;
            $email_redirects->email_alias = $user->name.".".$user->surname."@penguen.org.tr";
            $email_redirects->status = 0;
            $email_redirects->save();
        }

        $name_array = explode("-", $user->name);
        $surname_array = explode("-", $user->surname);

        $user->name = str_replace("-", "", $user->name);
        $user->surname = str_replace("-", "", $user->surname);

        if ( (count($name_array) == 1) && (count($surname_array) == 1) ) {
            $name_array = [];
            $surname_array = [];
        }

        return view('email-forwarding', [
            "user" => $user,
            "email_redirects" => $email_redirects,
            "name_array" => $name_array,
            "surname_array" => $surname_array
        ]);
    }

    public function postForwarding(Request $request) {

        $validator = $request->validate([
            'email_alias' => ['required'],
            'agreement' => ['required']
        ]);

        $email_alias = $request->get("email_alias");
        $user_id = Auth::id();
        $user = User::where("id", $user_id)->first();

        $email_redirects = EmailRedirects::where("user_id", $user_id)->first();

        if( ($email_alias == $email_redirects->email_alias) && ($email_redirects->status == 1) ) {
            return Redirect::to(secure_url('/home'))->with("danger-status", trans("panel.email_forwarding_notchange"));
        }

        $email_redirects->status = 1;
        $email_redirects->email_alias = $email_alias;
        $email_redirects->save();

        try {
            $result = $this->create_alias($email_redirects->email_alias, $email_redirects->email_forwarding);
        }
        catch(Exception $e) {
            $this->set_log("create", $e->getMessage() );
            return Redirect::to(secure_url('/home'))->with("danger-status", $e->getMessage() );
        }

        Log::info($result);

        if($result) {

            $user->alias = $email_redirects->email_alias;
            Mail::to($email_redirects->email_alias)->send(new PenguenWelcome($user));
            $this->set_log("other", $email_redirects->email_alias." adresine yönlendirme başarılı e-postası gönderildi");

            $this->set_log("create", $email_redirects->email_alias. " e-posta yönlendirmesi eklendi");
            return Redirect::to(secure_url('/home'))->with("forwarding-success", trans("panel.email_forwarding_success"));
        }

        $this->set_log("create", $email_redirects->email_alias. " e-posta yönlendirmesi eklenemedi");
        return Redirect::to(secure_url('/home'))->with("danger-status", trans("panel.email_forwarding_failed"));
    }

}
