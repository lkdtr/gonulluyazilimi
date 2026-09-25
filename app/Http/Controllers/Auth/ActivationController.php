<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Agreement;
use App\Models\Contact;
use App\Models\User;
use App\Support\AccountActivation;
use App\Support\Agreements;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

/**
 * A contact without an account sets a password through an emailed link.
 */
class ActivationController extends Controller
{
    public function create(): View
    {
        return view('auth.activate-request');
    }

    /**
     * Send the link. The answer is the same whether or not a record exists,
     * so the form does not reveal who is registered.
     */
    public function send(Request $request, AccountActivation $activation): RedirectResponse
    {
        $data = $request->validate(['email' => ['required', 'email', 'max:255']], [], ['email' => 'E-posta']);

        if ($contact = $activation->findByEmail($data['email'])) {
            $activation->send($contact);
            $this->set_log('other', "Hesap etkinleştirme bağlantısı gönderildi (kişi #{$contact->id})");
        }

        return back()->with('status', 'Bu adreste etkinleştirilebilecek bir kaydınız varsa, parola belirleme bağlantısını e-posta ile gönderdik.');
    }

    public function edit(Request $request, Contact $contact, string $hash, AccountActivation $activation): View|RedirectResponse
    {
        if (! hash_equals($activation->hash($contact), $hash) || ! $activation->eligible($contact)) {
            return redirect()->route('login')->with('status', 'Bu bağlantı artık geçerli değil. Hesabınız zaten etkinse giriş yapabilirsiniz.');
        }

        return view('auth.activate', ['contact' => $contact, 'action' => $request->fullUrl()]);
    }

    public function store(Request $request, Contact $contact, string $hash, AccountActivation $activation, Agreements $agreements): RedirectResponse
    {
        abort_unless(hash_equals($activation->hash($contact), $hash) && $activation->eligible($contact), 403);

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'agreement' => $agreements->rules(Agreement::PRIVACY),
        ], [], ['password' => 'Parola']);

        $user = new User([
            'name' => $contact->first_name,
            'surname' => $contact->last_name,
            'email' => strtolower($contact->email),
            'phone_number' => $contact->phone,
            'national_id' => $contact->identity_number,
            'birthday' => $contact->birthday,
            'city_id' => $contact->city_id ?? 0,
            'password' => Hash::make($request->input('password')),
            'agreement_at' => now(),
        ]);
        // Link to the existing contact before the first save, so its
        // affiliations and membership carry over instead of a new contact.
        $user->contact_id = $contact->id;
        $user->email_verified_at = now();
        $user->save();

        $agreements->accept($user, 'activation', Agreement::PRIVACY);
        $this->set_log('create', "Hesap etkinleştirildi (kişi #{$contact->id})");

        Auth::login($user);

        return redirect()->route('home')->with('success-status', 'Hesabınız etkinleştirildi, hoş geldiniz.');
    }
}
