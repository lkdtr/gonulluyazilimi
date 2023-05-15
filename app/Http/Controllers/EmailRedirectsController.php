<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Models\EmailRedirects;

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

        return view('email_redirects', [
            "user" => $user,
            "email_redirects" => $email_redirects,
        ]);
    }

    public function postValidation(Request $request)
    {

    }
}
