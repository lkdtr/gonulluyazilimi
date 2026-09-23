<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

use App\Events\DashboardVisited;
use App\Models\UserEvents;

class HomeController extends Controller
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
    public function home()
    {
        event(new DashboardVisited(Auth::user()));

        $this->set_log("other", Auth::user()->email." giriş yaptı");

        return view('home');
    }

    public function postHome(Request $request) {

        $validator = $request->validate([
            'event_id' => 'required',
        ]);

        $event_id = $request->get("event_id");
        $user_id = Auth::id();

        $user_event = UserEvents::where("user_id", $user_id)->where("event_id", $event_id)->first();
        if($user_event==null) $user_event = new UserEvents();
        $user_event->user_id = $user_id;
        $user_event->event_id = $event_id;
        $user_event->status = 1;
        $user_event->save();

        return Redirect::back()->with("status",  "Etkinlik için kaydınız alındı");
    }

}
