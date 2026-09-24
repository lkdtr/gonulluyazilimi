<?php

namespace Modules\Seminar\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Modules\Seminar\Models\SeminarRequests;

class SeminarRequestController extends Controller
{
    public function getList() {

        if (! Auth::user()->isOwner()) {
            return Redirect::to(secure_url('/home'))->with("danger-status", trans("panel.unauthorized_process"));
        }

        $seminarRequests = SeminarRequests::with(['user', 'seminarSubject', 'organizationRecord'])
            ->latest()
            ->get();

        return view('seminar::admin.requests', compact('seminarRequests'));

    }
}
