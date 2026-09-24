<?php

namespace App\Http\Controllers;

use App\Models\DataDeletionRequest;
use App\Support\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

/**
 * KVKK deletion request of the signed-in person, from the profile page.
 */
class DataDeletionController extends Controller
{
    public function store(Request $request, Organization $organization): RedirectResponse
    {
        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:2000'],
            'password' => ['required', 'string'],
        ], [], ['reason' => 'Gerekçe', 'password' => 'Parola']);

        $user = Auth::user();
        if (! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages(['password' => 'Parola hatalı.'])->errorBag('deletion');
        }

        $contact = $user->syncContact();
        if (DataDeletionRequest::pending()->where('contact_id', $contact->id)->exists()) {
            return redirect(route('my-infos').'#deletion');
        }

        $deletion = DataDeletionRequest::create(['contact_id' => $contact->id, 'user_id' => $user->id, 'reason' => $data['reason'] ?? null]);
        $this->set_log('create', "Veri silme talebi oluşturuldu (#{$deletion->id})");

        if ($notify = $organization->notificationEmail()) {
            Mail::raw("Portalda yeni bir kişisel veri silme talebi var (#{$deletion->id}). Yönetim panelinde \"Veri silme talepleri\" sayfasından değerlendirebilirsiniz.", fn ($message) => $message->to($notify)->subject('Yeni veri silme talebi'));
        }

        return redirect(route('my-infos').'#deletion')->with('deletion-status', 'Talebiniz alındı. Değerlendirildikten sonra e-posta ile bilgilendirileceksiniz.');
    }

    public function cancel(): RedirectResponse
    {
        $deletion = DataDeletionRequest::pending()->where('user_id', Auth::id())->firstOrFail();
        $deletion->forceFill(['status' => DataDeletionRequest::CANCELLED])->save();
        $this->set_log('change', "Veri silme talebinden vazgeçildi (#{$deletion->id})");

        return redirect(route('my-infos').'#deletion')->with('deletion-status', 'Veri silme talebinizden vazgeçtiniz.');
    }
}
