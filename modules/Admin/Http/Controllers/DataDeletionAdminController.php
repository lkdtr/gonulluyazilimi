<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\DataDeletionRequest;
use App\Support\Anonymizer;
use App\Support\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class DataDeletionAdminController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', DataDeletionRequest::PENDING);

        return view('admin::data-deletions.index', [
            'requests' => DataDeletionRequest::with(['contact', 'user', 'reviewer'])
                ->when(array_key_exists($status, DataDeletionRequest::STATUSES), fn ($query) => $query->where('status', $status))
                ->latest('id')
                ->paginate(25)
                ->withQueryString(),
            'status' => $status,
        ]);
    }

    public function approve(DataDeletionRequest $deletion, Anonymizer $anonymizer, Organization $organization): RedirectResponse
    {
        abort_unless($deletion->isPending(), 404);

        // Read before the address is removed, to tell the person.
        $email = $deletion->user?->email ?? $deletion->contact->email;

        $anonymizer->anonymize($deletion->contact, "veri silme talebi #{$deletion->id}");
        $deletion->forceFill([
            'status' => DataDeletionRequest::COMPLETED,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'reason' => null,
        ])->save();

        $this->notify($email, "{$organization->name()} portalındaki kişisel verilerinizin silinmesi talebiniz tamamlandı. Hesabınız kapatıldı ve kişisel verileriniz silindi.");

        return back()->with('success-status', "Talep #{$deletion->id}: kişisel veriler silindi.");
    }

    public function reject(Request $request, DataDeletionRequest $deletion, Organization $organization): RedirectResponse
    {
        abort_unless($deletion->isPending(), 404);

        $data = $request->validate(['response' => ['required', 'string', 'max:2000']], [], ['response' => 'Gerekçe']);

        $deletion->forceFill([
            'status' => DataDeletionRequest::REJECTED,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'response' => $data['response'],
        ])->save();
        $this->set_log('change', "Veri silme talebi reddedildi (#{$deletion->id})");

        $this->notify($deletion->user?->email ?? $deletion->contact->email, "{$organization->name()} portalındaki kişisel verilerinizin silinmesi talebiniz reddedildi.\n\nGerekçe: {$data['response']}");

        return back()->with('success-status', "Talep #{$deletion->id} reddedildi.");
    }

    private function notify(?string $email, string $text): void
    {
        if (! $email) {
            return;
        }

        try {
            Mail::raw($text, fn ($message) => $message->to($email)->subject('Kişisel veri silme talebiniz'));
        } catch (Throwable $e) {
            report($e);
        }
    }
}
