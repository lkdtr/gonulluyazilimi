<?php

namespace Modules\EmailChange\Http\Controllers;

use App\Events\UserEmailChanged;
use App\Events\UserEmailChanging;
use App\Exceptions\ActionBlocked;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Modules\EmailChange\Mail\EmailChangeRequestProcessed;
use Modules\EmailChange\Mail\EmailChangeRequestSubmitted;
use Modules\EmailChange\Models\EmailChangeRequest;

class EmailChangeRequestController extends Controller
{
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

        $this->sendMail('yk@lkd.org.tr', new EmailChangeRequestSubmitted($emailChangeRequest->load('user')));
        $this->set_log('create', $user->email.' e-posta değişikliği talebi oluşturdu.');

        return redirect()->route('email-change-requests.create')
            ->with('success-status', 'E-posta değişikliği talebiniz yönetime iletildi.');
    }

    public function index()
    {
        $emailChangeRequests = EmailChangeRequest::with(['user', 'processor'])
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('email-change::admin.index', compact('emailChangeRequests'));
    }

    public function approve(Request $request, EmailChangeRequest $emailChangeRequest): RedirectResponse
    {
        if ($emailChangeRequest->status !== 'pending') {
            return back()->with('danger-status', 'Bu talep daha önce sonuçlandırılmış.');
        }

        $user = User::findOrFail($emailChangeRequest->user_id);

        if (User::where('email', $emailChangeRequest->requested_email)->where('id', '!=', $user->id)->exists()) {
            return back()->with('danger-status', 'İstenen e-posta adresi artık başka bir kayıtta kullanılıyor.');
        }

        try {
            event(new UserEmailChanging($user, $emailChangeRequest->requested_email));
        } catch (ActionBlocked $blocked) {
            return back()->with('danger-status', $blocked->getMessage());
        }

        DB::transaction(function () use ($emailChangeRequest, $user, $request) {
            $oldEmail = $user->email;
            $user->email = $emailChangeRequest->requested_email;
            $user->save();

            event(new UserEmailChanged($user, $oldEmail));

            $emailChangeRequest->status = 'approved';
            $emailChangeRequest->processed_by = $request->user()->id;
            $emailChangeRequest->processed_at = now();
            $emailChangeRequest->save();
        });

        $this->sendMail($emailChangeRequest->requested_email, new EmailChangeRequestProcessed($emailChangeRequest->load('user')));
        $this->set_log('change', $user->id.' numaralı kullanıcının e-posta değişikliği onaylandı.');

        return back()->with('success-status', 'E-posta adresi ve aktif yönlendirmesi güncellendi.');
    }

    public function reject(Request $request, EmailChangeRequest $emailChangeRequest): RedirectResponse
    {
        if ($emailChangeRequest->status !== 'pending') {
            return back()->with('danger-status', 'Bu talep daha önce sonuçlandırılmış.');
        }

        $emailChangeRequest->update([
            'status' => 'rejected',
            'processed_by' => $request->user()->id,
            'processed_at' => now(),
        ]);

        $this->sendMail($emailChangeRequest->current_email, new EmailChangeRequestProcessed($emailChangeRequest->load('user')));
        $this->set_log('change', $emailChangeRequest->id.' numaralı e-posta değişikliği talebi reddedildi.');

        return back()->with('success-status', 'Talep reddedildi.');
    }

    /**
     * The request is already saved when these notifications go out; a mail
     * delivery failure must not turn a completed action into an error page.
     */
    private function sendMail(string $to, Mailable $mailable): void
    {
        try {
            Mail::to($to)->send($mailable);
        } catch (\Throwable $exception) {
            report($exception);
        }
    }
}
