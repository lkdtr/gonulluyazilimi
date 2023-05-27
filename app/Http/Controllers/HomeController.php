<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

use App\Models\UserEvents;
use App\Models\EmailRedirects;
use App\Models\Announcements;

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
        $user_id = Auth::id();

        $email_redirect_is_exist = EmailRedirects::where("user_id", $user_id)->where("status", 1)->first();

        $announcements = Announcements::where("status", 1)->whereRaw('finished_at > NOW()')->get();
        /*
        $user_events = UserEvents::where("status", 1)->where("user_id", $user_id)->get();
        $joined_events = [];
        foreach($user_events as $user_event) {
            $joined_events[$user_event->event_id] = 1;
        }
        */

        return view('home', [
            "announcements" => $announcements,
            //"joined_events" => $joined_events,
            "email_redirect_is_exist" => $email_redirect_is_exist,
        ]);
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
