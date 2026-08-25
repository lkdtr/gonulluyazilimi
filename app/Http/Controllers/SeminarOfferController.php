<?php

namespace App\Http\Controllers;

use App\Mail\SeminarOfferNotification;
use App\Mail\SeminarOfferReceived;
use App\Models\SeminarOffers;
use App\Models\SeminarSubjectProposals;
use App\Models\SeminarSubjects;
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
        $response = response()->view('user.create_seminar_offer', compact('seminarSubjects', 'formData', 'inIframe'));

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
            return redirect()->guest(route('login'));
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

    public function index()
    {
        $seminarOffers = SeminarOffers::with(['user', 'seminarSubject', 'seminarSubjectProposal'])->latest()->get();
        return view('admin.seminar_offers', compact('seminarOffers'));
    }

    public function acceptSubjectProposal(SeminarSubjectProposals $seminarSubjectProposal)
    {
        if ($seminarSubjectProposal->status === 'accepted') return back();
        $subject = SeminarSubjects::firstOrCreate(['subject' => $seminarSubjectProposal->subject], [
            'summary' => 'Seminer verme başvurusu ile önerildi.', 'duration' => 1, 'status' => 1, 'created_by' => Auth::id(),
        ]);
        $seminarSubjectProposal->update(['status' => 'accepted', 'accepted_by' => Auth::id(), 'accepted_at' => now()]);
        SeminarOffers::where('seminar_subject_proposal_id', $seminarSubjectProposal->id)->update(['seminar_subject_id' => $subject->id]);
        return back()->with('success-status', 'Konu seminer havuzuna eklendi.');
    }
}
