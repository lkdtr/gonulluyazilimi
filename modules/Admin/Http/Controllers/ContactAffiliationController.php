<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AffiliationType;
use App\Models\Contact;
use App\Models\ContactAffiliation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ContactAffiliationController extends Controller
{
    public function store(Request $request, Contact $contact): RedirectResponse
    {
        $data = $request->validate([
            'affiliation_type_id' => ['required', 'exists:affiliation_types,id'],
            'title' => ['nullable', 'string', 'max:100'],
            'started_at' => ['nullable', 'date'],
            'ended_at' => ['nullable', 'date', Rule::when($request->filled('started_at'), 'after:started_at')],
            'note' => ['nullable', 'string', 'max:1000'],
        ], [], [
            'affiliation_type_id' => 'Sıfat',
            'title' => 'Görev',
            'started_at' => 'Başlangıç',
            'ended_at' => 'Bitiş',
            'note' => 'Not',
        ]);

        $type = AffiliationType::with('roles')->findOrFail($data['affiliation_type_id']);
        if ($denied = $this->deniedForRoles($type)) {
            return $denied;
        }

        if ($contact->affiliations()->active()->where('affiliation_type_id', $type->id)->exists()) {
            return back()->withInput()->with('danger-status', "\"{$type->name}\" sıfatı bu kişide zaten sürüyor. Yeni dönem için önce mevcut olanı sona erdirin.");
        }

        $contact->affiliations()->create($data);
        $this->set_log('create', "{$contact->display_name} (#{$contact->id}) kişisine \"{$type->name}\" sıfatı verildi.");

        return back()->with('success-status', 'Sıfat eklendi.');
    }

    public function end(Contact $contact, ContactAffiliation $affiliation): RedirectResponse
    {
        abort_unless($affiliation->contact_id === $contact->id, 404);

        if ($denied = $this->deniedForRoles($affiliation->type)) {
            return $denied;
        }

        if ($affiliation->isActive()) {
            $affiliation->update(['ended_at' => today()]);
            $this->set_log('change', "{$contact->display_name} (#{$contact->id}) kişisinin \"{$affiliation->type->name}\" sıfatı sona erdirildi.");
        }

        return back()->with('success-status', 'Sıfat sona erdirildi; tarihçede kalır.');
    }

    /**
     * For entries made by mistake; an affiliation that really ended is ended,
     * not deleted.
     */
    public function destroy(Contact $contact, ContactAffiliation $affiliation): RedirectResponse
    {
        abort_unless($affiliation->contact_id === $contact->id, 404);

        if ($denied = $this->deniedForRoles($affiliation->type)) {
            return $denied;
        }

        $affiliation->delete();
        $this->set_log('delete', "{$contact->display_name} (#{$contact->id}) kişisinin \"{$affiliation->type->name}\" sıfat kaydı silindi.");

        return back()->with('success-status', 'Sıfat kaydı silindi.');
    }

    /**
     * An affiliation with a role template hands out permissions, so only
     * those who manage roles may change it.
     */
    private function deniedForRoles(AffiliationType $type): ?RedirectResponse
    {
        if ($type->roles()->exists() && ! Auth::user()->hasPermission('roles.manage')) {
            return back()->with('danger-status', "\"{$type->name}\" sıfatı yetki verdiği için yalnız rolleri yönetebilenler değiştirebilir.");
        }

        return null;
    }
}
