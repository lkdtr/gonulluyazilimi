<?php

namespace Modules\Announcements\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Mail;

use Modules\Announcements\Models\Announcements;

use Carbon\Carbon;

use App\Mail\AnnouncementMailing;
use App\Support\HtmlSanitizer;

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

            if(! Auth::user()->isOwner())  {
                return redirect('/login')->with('redirect', URL::full() );
            }

            return $next($request);
        });
    }

    public function getCreate() {

        $announcement = new Announcements();
        $announcement->status = 1;
        return view('announcements::admin.form', ["announcement" => $announcement]);
    }

    public function postCreate(Request $request) {

       if (! Auth::user()->isOwner()) {
            return Redirect::to(secure_url('/home'))->with("danger-status", trans("panel.unauthorized_process"));
        }

        $validator = $request->validate([
            'subject' => 'required',
            'detail' => 'required',
            'started_at' => 'required',
            'finished_at' => 'required',
        ]);

        $announcement = new Announcements();
        $announcement->subject = $request->input("subject");
        $announcement->detail = app(HtmlSanitizer::class)->sanitize($request->input('detail'));
        $announcement->started_at = Carbon::parse($request->get("started_at"))->format("Y-m-d H:i:s");
        $announcement->finished_at = Carbon::parse($request->get("finished_at"))->format("Y-m-d H:i:s");
        $announcement->status = $request->input("status");
        $announcement->send_mailing = $request->has("is_send_email")?1:0;
        $announcement->created_by = Auth::id();
        $announcement->updated_by = Auth::id();
        $announcement->save();

        $mailing = false;
        if($request->has("is_send_email")) {
            $mailing = true;
            $tomail = config('announcements.mailing_list');
        }
        elseif($request->has("is_send_test_email")) {
            $mailing = true;
            $tomail = config('announcements.test_mailing_list');
        }

        if($mailing) {
            $data = (object) [
                "subject" => $announcement->subject,
                "content" => $announcement->detail
            ];

            $result = Mail::to($tomail)->send(new AnnouncementMailing($data));
        }


        return Redirect::route('admin.announcements')->with("success-status", trans("panel.save_announcement_success"));
    }

    public function getList() {

        if (! Auth::user()->isOwner()) {
            return Redirect::to(secure_url('/home'))->with("danger-status", trans("panel.unauthorized_process"));
        }

        $announcements = Announcements::where("status", 1)->get();

        return view('announcements::admin.index', ["announcements" => $announcements]);

    }

    public function getEdit($id) {

        $announcement = Announcements::where("id", $id)->first();
        return view('announcements::admin.form', ["announcement" => $announcement]);
    }

    public function postEdit(Request $request, $id) {

       if (! Auth::user()->isOwner()) {
            return Redirect::to(secure_url('/home'))->with("danger-status", trans("panel.unauthorized_process"));
        }

        $validator = $request->validate([
            'subject' => 'required',
            'detail' => 'required',
            'started_at' => 'required',
            'finished_at' => 'required',
        ]);

        $announcement = Announcements::where("id", $id)->first();
        $announcement->subject = $request->input("subject");
        $announcement->detail = app(HtmlSanitizer::class)->sanitize($request->input('detail'));
        $announcement->started_at = Carbon::parse($request->get("started_at"))->format("Y-m-d H:i:s");
        $announcement->finished_at = Carbon::parse($request->get("finished_at"))->format("Y-m-d H:i:s");
        $announcement->status = $request->input("status");
        $announcement->send_mailing = $request->has("is_send_email")?1:0;
        $announcement->updated_by = Auth::id();
        $announcement->save();

        $mailing = false;
        if($request->has("is_send_email")) {
            $mailing = true;
            $tomail = config('announcements.mailing_list');
        }
        elseif($request->has("is_send_test_email")) {
            $mailing = true;
            $tomail = config('announcements.test_mailing_list');
        }

        if($mailing) {
            $data = (object) [
                "subject" => $announcement->subject,
                "content" => $announcement->detail
            ];

            $result = Mail::to($tomail)->send(new AnnouncementMailing($data));
        }

        return Redirect::route('admin.announcements')->with("success-status", trans("panel.save_announcement_success"));
    }


}
