<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

use App\Models\User;

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

    public function set_manager_role($user_id) {

        $user = User::where("id", $user_id)->first();
        if($user==null) {
            return Redirect::to(secure_url('/users'))->with("danger-status", trans("panel.set_manager_role_failed"));
        }
        else {
            $user->role = 2;
            $user->save();
            return Redirect::to(secure_url('/users'))->with("success-status", trans("panel.set_manager_role_success"));
        }

    }


}
