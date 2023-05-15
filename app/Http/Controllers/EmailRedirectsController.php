<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

use App\Models\User;
use App\Models\EmailRedirects;

use Epigra\TcKimlik;

class EmailRedirectsController extends Controller
{
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
        $user->birthday = date("Y-m-d", strtotime($birthday));
        $user->save();


        dump($request->all());
    }
}
