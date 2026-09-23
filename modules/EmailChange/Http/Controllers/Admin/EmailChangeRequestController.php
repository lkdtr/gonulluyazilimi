<?php

namespace Modules\EmailChange\Http\Controllers\Admin;

use App\Events\UserEmailChanged;
use App\Events\UserEmailChanging;
use App\Exceptions\ActionBlocked;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\EmailChange\Mail\EmailChangeRequestProcessed;
use Modules\EmailChange\Models\EmailChangeRequest;
use Modules\EmailChange\Support\SendsNotifications;

class EmailChangeRequestController extends Controller
{
    use SendsNotifications;

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
}
