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
        // A tab's form carries only its own fields; leave the others alone.
        $submitted = array_keys((array) $request->input('fields', []));
        $fields = $customFields->fieldsFor($contact, member: true)->where('member_access', 'editable')->whereIn('key', $submitted);

        $data = $request->validateWithBag('memberFields', $customFields->rules($fields), [], $customFields->attributes($fields));
        $customFields->save($contact, $fields, $data['fields'] ?? []);

        $anchor = preg_match('/^[a-z0-9-]+$/', (string) $request->input('anchor')) ? $request->input('anchor') : 'fields';

        return redirect(route('my-infos').'#'.$anchor)->with('fields-status', 'Bilgileriniz kaydedildi.');
    }
}
