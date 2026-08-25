<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\Welcome;
use App\Models\User;
use App\Models\ContactPermissions;
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
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'min:3'],
            'surname' => ['required', 'string', 'max:255', 'min:2'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone_number' => ['required', 'string', 'regex:/^\+?[0-9]{10,15}$/'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'agreement' => ['required'],
        ]);

        $phoneVerification = ContactPermissions::query()
            ->where('value_type', 'phone_number')
            ->where('value', $data['phone_number'])
            ->where('verified', true)
            ->where('verified_at', '>=', now()->subMinutes(30))
            ->first();

        if (! $phoneVerification) {
            return back()->withErrors(['phone_number' => 'Telefon numaranızı doğrulamanız gerekiyor.'])->withInput();
        }

        $user = $this->create($data, $phoneVerification);
        event(new Registered($user));
        Auth::login($user);

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    protected function create(array $data, ContactPermissions $phoneVerification): User
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
