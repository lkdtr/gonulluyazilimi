<?php

namespace Modules\Seminar\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Seminar\Models\SeminarOffers;
use Modules\Seminar\Models\SeminarSubjectProposals;
use Modules\Seminar\Models\SeminarSubjects;

class SeminarOfferController extends Controller
{
    public function index()
    {
        $seminarOffers = SeminarOffers::with(['user', 'seminarSubject', 'seminarSubjectProposal'])->latest()->get();
        return view('seminar::admin.offers', compact('seminarOffers'));
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
