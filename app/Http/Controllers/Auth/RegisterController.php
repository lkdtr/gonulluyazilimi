<?php

namespace App\Http\Controllers\Auth;

use App\Models\Agreement;
use App\Http\Controllers\Controller;
use App\Mail\Welcome;
use App\Models\User;
use App\Models\PhoneVerification;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        $inIframe = request()->boolean('in-iframe');
        $response = response()->view('auth.register', compact('inIframe'));

        if ($inIframe) {
            $response->headers->set('Content-Security-Policy', app(\App\Support\Organization::class)->frameAncestorsPolicy());
        }

        return $response;
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'min:3'],
            'surname' => ['required', 'string', 'max:255', 'min:2'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone_number' => ['required', 'string', 'regex:/^\+?[0-9]{10,15}$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'agreement' => app(\App\Support\Agreements::class)->rules(Agreement::PRIVACY),
        ]);

        $phoneVerification = PhoneVerification::query()
            ->where('value_type', 'phone_number')
            ->where('value', $data['phone_number'])
            ->where('verified', true)
            ->where('verified_at', '>=', now()->subMinutes(30))
            ->first();

        if (! $phoneVerification) {
            return back()->withErrors(['phone_number' => 'Telefon numaranızı doğrulamanız gerekiyor.'])->withInput();
        }

        $user = $this->create($data, $phoneVerification);
        app(\App\Support\Agreements::class)->accept($user, 'register', Agreement::PRIVACY);
        // The form asks these three; unticked means declined.
        app(\App\Support\Consents::class)->set($user->contact, collect(['email', 'sms', 'whatsapp'])->mapWithKeys(fn ($channel) => [$channel => $request->boolean("consents.{$channel}")])->all(), 'register');
        event(new Registered($user));
        Auth::login($user);

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    protected function create(array $data, PhoneVerification $phoneVerification): User
    {
        $user = User::create([
            'name' => $this->tr_ucwords($data['name']),
            'surname' => $this->tr_ucwords($data['surname']),
            'national_id' => null,
            'email' => strtolower($data['email']),
            'phone_number' => $data['phone_number'],
            'password' => Hash::make($data['password']),
            'agreement_at' => now(),
            'phone_number_verified_at' => $phoneVerification->verified_at,
        ]);

        Mail::to($user->email)->send(new Welcome($user));
        $this->set_log('other', $user->email.' adresine hoş geldiniz e-postası gönderildi');

        return $user;
    }
}
