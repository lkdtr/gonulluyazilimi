<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordChangeController extends Controller
{
    public function edit()
    {
        return view('password.change');
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $request->user()->update([
            'password' => Hash::make($request->string('password')->value()),
        ]);

        $this->set_log('change', 'Kullanıcı parolasını değiştirdi.');

        return redirect()->route('password.change.edit')
            ->with('success-status', 'Parolanız başarıyla değiştirildi.');
    }
}
