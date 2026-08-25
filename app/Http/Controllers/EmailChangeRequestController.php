<?php

namespace App\Http\Controllers;

use App\Mail\EmailChangeRequestProcessed;
use App\Mail\EmailChangeRequestSubmitted;
use App\Models\EmailChangeRequest;
use App\Models\EmailRedirects;
use App\Models\User;
use App\Services\PostfixAdminClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class EmailChangeRequestController extends Controller
{
    public function create()
    {
        $pendingRequest = EmailChangeRequest::query()
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->latest()
            ->first();

        return view('email-change-requests.create', compact('pendingRequest'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        $emailRedirect = EmailRedirects::where('user_id', $user->id)->first();

        $request->merge([
            'requested_email' => strtolower(trim((string) $request->input('requested_email'))),
        ]);

        $request->validate([
            'requested_email' => [
                'required', 'string', 'email:rfc', 'max:255', Rule::notIn([$user->email]),
                Rule::unique('users', 'email'),
                Rule::unique('email_redirects', 'email_forwarding')->ignore($emailRedirect?->id),
            ],
            'reason' => ['nullable', 'string', 'max:2000'],
            'password' => ['required', 'current_password'],
        ]);

        if (EmailChangeRequest::where('user_id', $user->id)->where('status', 'pending')->exists()) {
            return back()->with('danger-status', 'Değerlendirmede olan bir e-posta değişikliği talebiniz zaten var.');
        }

        $emailChangeRequest = EmailChangeRequest::create([
            'user_id' => $user->id,
            'current_email' => $user->email,
            'requested_email' => strtolower($request->string('requested_email')->trim()->value()),
            'reason' => $request->string('reason')->trim()->value() ?: null,
        ]);

        Mail::to('yk@lkd.org.tr')->send(new EmailChangeRequestSubmitted($emailChangeRequest->load('user')));
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

        return view('admin.email_change_requests', compact('emailChangeRequests'));
    }

    public function approve(Request $request, EmailChangeRequest $emailChangeRequest, PostfixAdminClient $postfixAdmin): RedirectResponse
    {
        if ($emailChangeRequest->status !== 'pending') {
            return back()->with('danger-status', 'Bu talep daha önce sonuçlandırılmış.');
        }

        $user = User::findOrFail($emailChangeRequest->user_id);
        $emailRedirect = EmailRedirects::where('user_id', $user->id)->first();

        if (User::where('email', $emailChangeRequest->requested_email)->where('id', '!=', $user->id)->exists()
            || EmailRedirects::where('email_forwarding', $emailChangeRequest->requested_email)->when($emailRedirect, fn ($query) => $query->where('id', '!=', $emailRedirect->id))->exists()) {
            return back()->with('danger-status', 'İstenen e-posta adresi artık başka bir kayıtta kullanılıyor.');
        }

        if ($emailRedirect?->status === 1 && ! $postfixAdmin->updateAlias($emailRedirect->email_alias, $emailChangeRequest->requested_email)) {
            return back()->with('danger-status', 'PostfixAdmin yönlendirmesi güncellenemedi; e-posta değişikliği uygulanmadı.');
        }

        DB::transaction(function () use ($emailChangeRequest, $user, $emailRedirect, $request) {
            $user->email = $emailChangeRequest->requested_email;
            $user->save();

            if ($emailRedirect) {
                $emailRedirect->email_forwarding = $emailChangeRequest->requested_email;
                $emailRedirect->save();
            }

            $emailChangeRequest->status = 'approved';
            $emailChangeRequest->processed_by = $request->user()->id;
            $emailChangeRequest->processed_at = now();
            $emailChangeRequest->save();
        });

        Mail::to($emailChangeRequest->requested_email)->send(new EmailChangeRequestProcessed($emailChangeRequest->load('user')));
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

        Mail::to($emailChangeRequest->current_email)->send(new EmailChangeRequestProcessed($emailChangeRequest->load('user')));
        $this->set_log('change', $emailChangeRequest->id.' numaralı e-posta değişikliği talebi reddedildi.');

        return back()->with('success-status', 'Talep reddedildi.');
    }
}
