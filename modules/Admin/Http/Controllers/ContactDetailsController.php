<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Support\CustomFields;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

/**
 * Tags and custom field values of a contact, edited on the contact page.
 */
class ContactDetailsController extends Controller
{
    public function tags(Request $request, Contact $contact): RedirectResponse
    {
        $data = $request->validate(['tags' => ['array'], 'tags.*' => ['integer', Rule::exists('tags', 'id')]]);

        $changes = $contact->tags()->sync($data['tags'] ?? []);
        if (array_filter($changes)) {
            $this->set_log('change', "Etiketler güncellendi: {$contact->display_name}");
        }

        return back()->with('success-status', 'Etiketler kaydedildi.');
    }

    public function fields(Request $request, Contact $contact, CustomFields $customFields): RedirectResponse
    {
        $fields = $customFields->fieldsFor($contact);
        $data = $request->validateWithBag('contactFields', $customFields->rules($fields), [], $customFields->attributes($fields));

        $customFields->save($contact, $fields, $data['fields'] ?? []);

        return back()->with('success-status', 'Ek bilgiler kaydedildi.');
    }
}
