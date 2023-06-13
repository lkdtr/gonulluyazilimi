<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Redirect;

use App\Models\SeminarSubjects;

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

        $seminarSubject = new SeminarSubjects();
        return view('admin.create_edit_seminar_subject', ["seminarSubject" => $seminarSubject]);
    }

    public function postCreateSubject(Request $request) {

        if (Auth::user()->role!=1 ) {
            return Redirect::to(secure_url('/home'))->with("danger-status", trans("panel.unauthorized_process"));
        }

        $validator = $request->validate([
            'subject' => 'required',
            'summary' => 'required',
            'duration' => 'required',
        ]);

        $seminarSubject = new SeminarSubjects();
        $seminarSubject->subject = $request->get("subject");
        $seminarSubject->type = $request->get("type");
        $seminarSubject->summary = $request->get("summary");
        $seminarSubject->syllabus = $request->get("syllabus");
        $seminarSubject->duration = $request->get("duration");
        $seminarSubject->status = 1;
        $seminarSubject->created_by = Auth::id();
        $seminarSubject->save();

        $this->set_log("create", $seminarSubject->subject. " semineri eklendi");

        return Redirect::to(secure_url('/seminar-subjects'))->with("success-status", trans("panel.successfully_saved"));
    }

    public function getEditSubject($id) {

        if (Auth::user()->role!=1 ) {
            return Redirect::to(secure_url('/home'))->with("danger-status", trans("panel.unauthorized_process"));
        }

        $seminarSubject = SeminarSubjects::where("id", $id)->first();
        return view('admin.create_edit_seminar_subject', ["seminarSubject" => $seminarSubject]);
    }

    public function postEditSubject(Request $request, $id) {

        if (Auth::user()->role!=1 ) {
            return Redirect::to(secure_url('/home'))->with("danger-status", trans("panel.unauthorized_process"));
        }

        $validator = $request->validate([
            'subject' => 'required',
            'summary' => 'required',
            'duration' => 'required',
        ]);

        $seminarSubject = SeminarSubjects::where("id", $id)->first();
        $seminarSubject->subject = $request->get("subject");
        $seminarSubject->type = $request->get("type");
        $seminarSubject->summary = $request->get("summary");
        $seminarSubject->syllabus = $request->get("syllabus");
        $seminarSubject->duration = $request->get("duration");
        $seminarSubject->status = 1;
        $seminarSubject->updated_by = Auth::id();
        $seminarSubject->save();

        $this->set_log("change", $seminarSubject->subject. " semineri güncellendi");

        return Redirect::to(secure_url('/seminar-subjects'))->with("success-status", trans("panel.successfully_saved"));
    }

    public function getSubjectList() {

        if (Auth::user()->role!=1 ) {
            return Redirect::to(secure_url('/home'))->with("danger-status", trans("panel.unauthorized_process"));
        }

        $seminarSubjects = SeminarSubjects::where("status", 1)->get();

        return view('admin.seminar_subjects', ["seminarSubjects" => $seminarSubjects]);
    }
}
