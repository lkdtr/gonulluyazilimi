<?php

namespace App\Http\Controllers;

use App\Models\Agreement;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Public text of an agreement: the version in force, or an earlier published
 * version with ?version=N. ?iframe shows it bare for the forms' modal.
 */
class AgreementController extends Controller
{
    public function show(Request $request, string $key): View
    {
        $agreement = Agreement::where('key', $key)->firstOrFail();

        $version = $request->filled('version')
            ? $agreement->versions()->whereNotNull('published_at')->where('version', $request->integer('version'))->firstOrFail()
            : $agreement->currentVersion ?? abort(404);

        $data = [
            'title' => $agreement->title,
            'content' => $version->content,
            'version' => $version,
            'history' => $agreement->versions()->whereNotNull('published_at')->get(['id', 'agreement_id', 'version', 'published_at']),
            'agreement' => $agreement,
        ];

        return view($request->has('iframe') ? 'agreement-iframe' : 'agreement', $data);
    }

    // Addresses used before agreements were editable.

    public function userAgreement(Request $request): View
    {
        return $this->show($request, Agreement::PRIVACY);
    }

    public function emailAgreement(Request $request): View
    {
        return $this->show($request, Agreement::EMAIL_USAGE);
    }
}
