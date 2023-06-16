<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

use App\Models\Announcements;

class AnnouncementController extends Controller
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

    public function getCreate() {

        $announcement = new Announcements();
        return view('admin.create_edit_announcement', ["announcement" => $announcement]);
    }

    public function postCreate() {

        $announcement = new Announcements();
        $announcement->subject = "Bizi sosyal medya üzerinden takip ediyormusunuz?";
        $announcement->detail = '<p>Linux Kullanıcıları Derneği Sosyal Medya Hesaplarını takip edin, gelişmelerden habersiz kalmayın.</p>

        <h3>LKD Sosyal Medya Hesapları</h3>

        <p>Telegram: <a href="https://t.me/lkd_tr">https://t.me/lkd_tr</a></p>
        <p>Twitter: <a href="https://twitter.com/lkdtr">https://twitter.com/lkdtr</a></p>
        <p>Facebook: <a href="https://www.facebook.com/lkdtr">https://www.facebook.com/lkdtr</a></p>
        <p>Instagram: <a href="https://www.instagram.com/lkdorgtr/">https://www.instagram.com/lkdorgtr/</a></p>
        <p>Tiktok: <a href="https://www.tiktok.com/@lkdtr">https://www.tiktok.com/@lkdtr</a></p>
        ';
        $announcement->started_at = date("Y-m-d H:i:s");
        $announcement->finished_at = date("Y-m-d H:i:s", strtotime("+1 year"));
        $announcement->status = 1;
        $announcement->created_by = 1;
        $announcement->save();

        dump($announcement);

    }

    public function getList() {

        if (Auth::user()->role!=1 ) {
            return Redirect::to(secure_url('/home'))->with("danger-status", trans("panel.unauthorized_process"));
        }

        $announcements = Announcements::where("status", 1)->get();

        return view('admin.announcements', ["announcements" => $announcements]);

    }

}
