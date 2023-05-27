<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

use App\Models\User;

class UserController extends Controller
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

            if(Auth::user()->role!=1 )  {
                return redirect('/login')->with('redirect', URL::full() );
            }

            return $next($request);
        });
    }

    public function getUserInfos($user_id) {

        if ( (Auth::user()->role!=1 ) && (Auth::id()!=$user_id) ) {
            return Redirect::to(secure_url('/users'))->with("danger-status", trans("panel.unauthorized_process"));
        }

        if(Auth::user()->role!=1 )  {
            return Redirect::to(secure_url('/home'))->with("status", "Bu bölüm yapım aşamasında");
        }

        $user = User::where("id", $user_id)->first();

        dump($user);
    }

    public function getMyInfos() {
        $user_id = Auth::id();
        return $this->getUserInfos($user_id);
    }

}
