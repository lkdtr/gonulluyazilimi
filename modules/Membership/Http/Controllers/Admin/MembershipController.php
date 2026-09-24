<?php

namespace Modules\Membership\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\Membership\Models\Membership;
use Modules\Membership\Support\MembershipService;

class MembershipController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));
        $status = $request->query('status', Membership::ACTIVE);

        return view('membership::admin.index', [
            'memberships' => Membership::with('contact')
                ->when(array_key_exists((string) $status, Membership::STATUSES), fn ($query) => $query->where('status', $status))
                ->when($search !== '', fn ($query) => $query->where(fn ($query) => $query
                    ->where('number', 'like', "%{$search}%")
                    ->orWhereHas('contact', fn ($query) => $query->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))))
                ->orderByRaw('number is null')->orderByRaw('cast(number as unsigned)')->orderBy('id')
                ->paginate(50)
                ->withQueryString(),
            'counts' => Membership::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status'),
            'status' => $status,
            'search' => $search,
        ]);
    }

    /**
     * Make a contact a member.
     */
    public function store(Request $request, Contact $contact, MembershipService $service): RedirectResponse
    {
        $data = $request->validate([
            'number' => ['nullable', 'string', 'max:20', Rule::unique('memberships', 'number')->ignore($contact->id, 'contact_id')],
            'joined_at' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:500'],
        ], [], ['number' => 'Üye no', 'joined_at' => 'Katılma tarihi']);

        $service->start($contact, $data['number'] ?? null, Carbon::parse($data['joined_at']), $data['note'] ?? null);

        return back()->with('success-status', "{$contact->display_name} üye yapıldı.");
    }

    public function update(Request $request, Membership $membership, MembershipService $service): RedirectResponse
    {
        $data = $request->validate([
            'number' => ['nullable', 'string', 'max:20', Rule::unique('memberships', 'number')->ignore($membership)],
            'applied_at' => ['nullable', 'date'],
            'joined_at' => ['nullable', 'date'],
            'derbis_registered' => ['nullable', 'in:0,1'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ], [], ['number' => 'Üye no', 'applied_at' => 'Başvuru tarihi', 'joined_at' => 'Katılma tarihi', 'notes' => 'Not']);

        $service->changeNumber($membership, $data['number'] ?? null);
        $membership->update([
            'applied_at' => $data['applied_at'] ?? null,
            'joined_at' => $data['joined_at'] ?? null,
            'derbis_registered' => isset($data['derbis_registered']) ? (bool) $data['derbis_registered'] : null,
            'notes' => $data['notes'] ?? null,
        ]);

        return back()->with('success-status', 'Üyelik bilgileri kaydedildi.');
    }

    public function status(Request $request, Membership $membership, MembershipService $service): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in([Membership::ACTIVE, Membership::SUSPENDED, Membership::LEFT])],
            'date' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:500'],
        ], [], ['status' => 'Durum', 'date' => 'Tarih', 'note' => 'Not']);

        $service->changeStatus($membership, $data['status'], Carbon::parse($data['date']), $data['note'] ?? null);

        return back()->with('success-status', 'Üyelik durumu: '.Membership::STATUSES[$data['status']].'.');
    }

    public function event(Request $request, Membership $membership, MembershipService $service): RedirectResponse
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'note' => ['required', 'string', 'max:500'],
            'extra_months' => ['nullable', 'integer', 'between:-120,120'],
        ], [], ['date' => 'Tarih', 'note' => 'Not', 'extra_months' => 'Ek üyelik süresi']);

        $service->event($membership, 'note', Carbon::parse($data['date']), $data['note'], $data['extra_months'] ?? null);

        return back()->with('success-status', 'Tarihçeye not eklendi.');
    }
}
