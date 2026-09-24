<?php

namespace Modules\EmailChange\Http\Controllers;

use App\Events\UserEmailChanging;
use App\Exceptions\ActionBlocked;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Modules\EmailChange\Mail\EmailChangeRequestSubmitted;
use Modules\EmailChange\Models\EmailChangeRequest;
use Modules\EmailChange\Support\SendsNotifications;

class EmailChangeRequestController extends Controller
{
    use SendsNotifications;

    public function create()
    {
        $pendingRequest = EmailChangeRequest::query()
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->latest()
            ->first();

        return view('email-change::create', compact('pendingRequest'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->merge([
            'requested_email' => strtolower(trim((string) $request->input('requested_email'))),
        ]);

        $request->validate([
            'requested_email' => [
                'required', 'string', 'email:rfc', 'max:255', Rule::notIn([$user->email]),
                Rule::unique('users', 'email'),
            ],
            'reason' => ['nullable', 'string', 'max:2000'],
            'password' => ['required', 'current_password'],
        ]);

        try {
            event(new UserEmailChanging($user, $request->input('requested_email'), dryRun: true));
        } catch (ActionBlocked $blocked) {
            throw ValidationException::withMessages(['requested_email' => $blocked->getMessage()]);
        }

        if (EmailChangeRequest::where('user_id', $user->id)->where('status', 'pending')->exists()) {
            return back()->with('danger-status', 'Değerlendirmede olan bir e-posta değişikliği talebiniz zaten var.');
        }

        $emailChangeRequest = EmailChangeRequest::create([
            'user_id' => $user->id,
            'current_email' => $user->email,
            'requested_email' => strtolower($request->string('requested_email')->trim()->value()),
            'reason' => $request->string('reason')->trim()->value() ?: null,
        ]);

        if ($notify = app(\App\Support\Organization::class)->notificationEmail()) {
            $this->sendMail($notify, new EmailChangeRequestSubmitted($emailChangeRequest->load('user')));
        }
        $this->set_log('create', $user->email.' e-posta değişikliği talebi oluşturdu.');

        return redirect()->route('email-change-requests.create')
            ->with('success-status', 'E-posta değişikliği talebiniz yönetime iletildi.');
    }
}
