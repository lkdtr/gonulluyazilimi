<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Redirect;

use App\Models\User;
use App\Models\Cities;

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

    public function getMyInfos() {
        $user_id = Auth::id();
        return $this->getUserInfos($user_id);
    }

    public function getUserInfos($user_id) {

        dump("here");exit;

        if ( (Auth::user()->role!=1 ) && (Auth::user()->role!=2 ) && (Auth::id()!=$user_id) ) {
            return Redirect::to(secure_url('/home'))->with("danger-status", trans("panel.unauthorized_process"));
        }

        $user = User::where("id", $user_id)->first();
        $cities = Cities::where("status", "1")->get();

        return view('admin.user_infos', ["user" => $user, "cities" => $cities]);
    }


    public function postMyInfos(Request $request) {
        $user_id = Auth::id();
        return $this->postUserInfos($request, $user_id);
    }

    public function postUserInfos(Request $request, $user_id) {

        if ( (Auth::user()->role!=1 ) && (Auth::user()->role!=2 ) && (Auth::id()!=$user_id) ) {
            return Redirect::to(secure_url('/users'))->with("danger-status", trans("panel.unauthorized_process"));
        }

        $validator = $request->validate([
            'city' => 'required',
        ]);

        $user = User::where("id", $user_id)->first();
        $user->city_id = $request->get("city");

        $user->save();

        return Redirect::back()->with("status", trans("panel.successfully_saved"));
    }


}
