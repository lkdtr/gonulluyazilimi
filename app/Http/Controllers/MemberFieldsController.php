<?php

namespace App\Http\Controllers;

use App\Support\CustomFields;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Custom fields the signed-in person may edit, on the profile page.
 */
class MemberFieldsController extends Controller
{
    public function update(Request $request, CustomFields $customFields): RedirectResponse
    {
        $contact = Auth::user()->syncContact();
        $fields = $customFields->fieldsFor($contact, member: true)->where('member_access', 'editable');

        $data = $request->validateWithBag('memberFields', $customFields->rules($fields), [], $customFields->attributes($fields));
        $customFields->save($contact, $fields, $data['fields'] ?? []);

        return redirect(route('my-infos').'#fields')->with('fields-status', 'Bilgileriniz kaydedildi.');
    }
}
