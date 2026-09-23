<?php

namespace Modules\Seminar\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

use Modules\Seminar\Models\SeminarSubjects;
use Modules\Seminar\Models\SeminarRequests;
use Modules\Seminar\Models\Organizations;
use Modules\Seminar\Mail\SeminarRequestNotification;
use Modules\Seminar\Mail\SeminarRequestReceived;

class SeminarController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->only(['postCreate']);
    }

    public function getCreate() {

        $inIframe = request()->boolean('in-iframe');
        $seminarSubjects = SeminarSubjects::where('status', 1)->orderBy('subject')->get();
        $organizations = Organizations::orderBy('name')->get();
        $minimumSeminarDate = now()->addDays(60)->toDateString();

        $response = response()->view('seminar::create_request', compact('seminarSubjects', 'organizations', 'minimumSeminarDate', 'inIframe'));

        if ($inIframe) {
            $response->headers->set('Content-Security-Policy', "frame-ancestors 'self' https://lkd.org.tr https://www.lkd.org.tr");
        }

        return $response;

    }

    public function postCreate(Request $request) {

        $inIframe = $request->boolean('in-iframe');
        $minimumSeminarDate = now()->addDays(60)->toDateString();
        $data = $request->validate([
            'seminar_subject_id' => ['required', 'integer', 'exists:seminar_subjects,id'],
            'organization' => ['required', 'string', 'max:255'],
            'seminar_type' => ['required', 'in:in_person,online'],
            'location' => ['nullable', 'string', 'max:255', 'required_if:seminar_type,in_person'],
            'seminar_start_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:'.$minimumSeminarDate],
            'seminar_end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:seminar_start_date'],
        ]);

        $seminarSubject = SeminarSubjects::where('id', $data['seminar_subject_id'])
            ->where('status', 1)
            ->firstOrFail();

        $organizationName = $this->tr_ucwords(Str::squish($data['organization']));
        $organization = Organizations::firstOrCreate(
            ['normalized_name' => Str::lower($organizationName)],
            ['name' => $organizationName]
        );

        $seminarRequest = new SeminarRequests();
        $seminarRequest->user_id = Auth::id();
        $seminarRequest->seminar_subject_id = $seminarSubject->id;
        $seminarRequest->organization_id = $organization->id;
        $seminarRequest->organization = $organization->name;
        $seminarRequest->seminar_type = $data['seminar_type'];
        $seminarRequest->location = $data['seminar_type'] === 'online' ? '' : $data['location'];
        // Keep the original column populated for existing reports and integrations.
        $seminarRequest->seminar_date = $data['seminar_start_date'];
        $seminarRequest->seminar_start_date = $data['seminar_start_date'];
        $seminarRequest->seminar_end_date = $data['seminar_end_date'];
        $seminarRequest->status = 'pending';
        $seminarRequest->save();
        $seminarRequest->load(['user', 'seminarSubject', 'organizationRecord']);

        Mail::to('yk@lkd.org.tr')->send(new SeminarRequestNotification($seminarRequest));
        Mail::to($seminarRequest->user->email)->send(new SeminarRequestReceived($seminarRequest));

        $this->set_log('create', $seminarSubject->subject.' semineri için talep oluşturuldu');

        return Redirect::to(route('create-seminar-request', $inIframe ? ['in-iframe' => 1] : []))
            ->with('success-status', 'Seminer talebiniz alındı ve değerlendirmeye gönderildi.');

    }
}
