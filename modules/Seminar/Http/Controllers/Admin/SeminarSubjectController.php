<?php

namespace Modules\Seminar\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Modules\Seminar\Models\SeminarSubjects;

class SeminarSubjectController extends Controller
{
    public function getSubjectList() {

        if (Auth::user()->role!=1 ) {
            return Redirect::to(secure_url('/home'))->with("danger-status", trans("panel.unauthorized_process"));
        }

        $seminarSubjects = SeminarSubjects::where("status", 1)->get();

        return view('seminar::admin.subjects', ["seminarSubjects" => $seminarSubjects]);
    }

    public function getCreateSubject() {

        if (Auth::user()->role!=1 ) {
            return Redirect::to(secure_url('/home'))->with("danger-status", trans("panel.unauthorized_process"));
        }

        $seminarSubject = new SeminarSubjects();
        return view('seminar::admin.subject_form', ["seminarSubject" => $seminarSubject]);
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

        return Redirect::route('admin.seminar-subjects')->with("success-status", trans("panel.successfully_saved"));
    }

    public function getEditSubject($id) {

        if (Auth::user()->role!=1 ) {
            return Redirect::to(secure_url('/home'))->with("danger-status", trans("panel.unauthorized_process"));
        }

        $seminarSubject = SeminarSubjects::where("id", $id)->first();
        return view('seminar::admin.subject_form', ["seminarSubject" => $seminarSubject]);
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

        return Redirect::route('admin.seminar-subjects')->with("success-status", trans("panel.successfully_saved"));
    }
}
