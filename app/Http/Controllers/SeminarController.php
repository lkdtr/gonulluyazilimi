<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Redirect;

class SeminarController extends Controller
{

    public function __construct()
    {
        $this->middleware(function ($request, $next) {

            $this->middleware('auth');

            if(!Auth::check() ) {
                return redirect('/login')->with('redirect', URL::full() );
            }

            return $next($request);
        });
    }

    public function getList() {

        if (Auth::user()->role!=1 ) {
            return Redirect::to(secure_url('/home'))->with("danger-status", trans("panel.unauthorized_process"));
        }

        return view('admin.seminar_requests');

    }

    public function getCreate() {

        if (Auth::user()->role!=1 ) {
            return Redirect::to(secure_url('/home'))->with("danger-status", trans("panel.under_construction"));
        }

        return view('user.create_seminar_request');

    }

    public function postCreate(Request $request) {

        return Redirect::to(secure_url('/home'))->with("danger-status", trans("panel.under_construction"));

    }

    public function getCreateSubject() {

        if (Auth::user()->role!=1 ) {
            return Redirect::to(secure_url('/home'))->with("danger-status", trans("panel.unauthorized_process"));
        }

        return Redirect::to(secure_url('/home'))->with("danger-status", trans("panel.under_construction"));

    }

    public function postCreateSubject() {

        if (Auth::user()->role!=1 ) {
            return Redirect::to(secure_url('/home'))->with("danger-status", trans("panel.unauthorized_process"));
        }

        return Redirect::to(secure_url('/home'))->with("danger-status", trans("panel.under_construction"));

    }

    public function getSubjectList() {

        if (Auth::user()->role!=1 ) {
            return Redirect::to(secure_url('/home'))->with("danger-status", trans("panel.unauthorized_process"));
        }

        return Redirect::to(secure_url('/home'))->with("danger-status", trans("panel.under_construction"));

    }
}
