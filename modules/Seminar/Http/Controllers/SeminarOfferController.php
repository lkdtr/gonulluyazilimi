<?php

namespace Modules\Seminar\Http\Controllers;

use App\Http\Controllers\Controller;

use Modules\Seminar\Mail\SeminarOfferNotification;
use Modules\Seminar\Mail\SeminarOfferReceived;
use Modules\Seminar\Models\SeminarOffers;
use Modules\Seminar\Models\SeminarSubjectProposals;
use Modules\Seminar\Models\SeminarSubjects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SeminarOfferController extends Controller
{
    public function create()
    {
        $inIframe = request()->boolean('in-iframe');
        $seminarSubjects = SeminarSubjects::where('status', 1)->orderBy('subject')->get();
        $formData = session('seminar_offer_form', []);
        $response = response()->view('seminar::create_offer', compact('seminarSubjects', 'formData', 'inIframe'));

        if ($inIframe) {
            $response->headers->set('Content-Security-Policy', "frame-ancestors 'self' https://lkd.org.tr https://www.lkd.org.tr");
        }

        return $response;
    }

    public function store(Request $request)
    {
        $inIframe = $request->boolean('in-iframe');
        $data = $request->validate([
            'subject_choice' => ['required', 'in:existing,proposed'],
            'seminar_subject_id' => ['nullable', 'required_if:subject_choice,existing', 'exists:seminar_subjects,id'],
            'proposed_subject' => ['nullable', 'required_if:subject_choice,proposed', 'string', 'max:255'],
            'summary' => ['required', 'string', 'max:4000'],
            'target_audience' => ['required', 'string', 'max:255'],
            'seminar_type' => ['required', 'in:in_person,online,either'],
            'duration' => ['required', 'integer', 'min:1', 'max:48'],
            'availability_start_date' => ['nullable', 'date_format:Y-m-d'],
            'availability_end_date' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:availability_start_date'],
            'cities' => ['nullable', 'string', 'max:2000'],
            'technical_requirements' => ['nullable', 'string', 'max:4000'],
            'biography' => ['required', 'string', 'max:10000'],
            'reference_links' => ['nullable', 'string', 'max:4000'],
            'notes' => ['nullable', 'string', 'max:4000'],
        ]);

        if (! Auth::check()) {
            session(['seminar_offer_form' => $data]);
            return redirect()->guest(route('login', $inIframe ? ['in-iframe' => 1] : []));
        }

        $proposal = null;
        if ($data['subject_choice'] === 'proposed') {
            $subject = $this->tr_ucwords(Str::squish($data['proposed_subject']));
            $proposal = SeminarSubjectProposals::firstOrCreate(
                ['normalized_subject' => Str::lower($subject)],
                ['subject' => $subject]
            );
        }

        $seminarOffer = SeminarOffers::create(array_merge($data, [
            'user_id' => Auth::id(),
            'seminar_subject_id' => $data['subject_choice'] === 'existing' ? $data['seminar_subject_id'] : null,
            'seminar_subject_proposal_id' => $proposal?->id,
        ]));
        unset($seminarOffer->subject_choice, $seminarOffer->proposed_subject);
        $seminarOffer->load(['user', 'seminarSubject', 'seminarSubjectProposal']);
        session()->forget('seminar_offer_form');

        Mail::to('yk@lkd.org.tr')->send(new SeminarOfferNotification($seminarOffer));
        Mail::to($seminarOffer->user->email)->send(new SeminarOfferReceived($seminarOffer));
        $this->set_log('create', 'Seminer verme başvurusu oluşturuldu.');

        return redirect()->route('create-seminar-offer', $inIframe ? ['in-iframe' => 1] : [])
            ->with('success-status', 'Seminer verme başvurunuz alındı ve değerlendirmeye gönderildi.');
    }
}
