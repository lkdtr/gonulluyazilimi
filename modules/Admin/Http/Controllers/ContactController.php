<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AffiliationType;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));
        $type = $request->query('type');
        $affiliation = $request->query('affiliation');

        $contacts = Contact::query()
            ->with(['affiliations' => fn ($query) => $query->active()->with('type'), 'user'])
            ->when($search !== '', fn ($query) => $query->where(fn ($query) => $query
                ->where('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%")
                ->orWhere('organization_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('identity_number', 'like', "%{$search}%")))
            ->when(in_array($type, [Contact::TYPE_PERSON, Contact::TYPE_ORGANIZATION], true), fn ($query) => $query->where('type', $type))
            ->when($affiliation, fn ($query) => $query->whereHas('affiliations', fn ($query) => $query->active()->ofType($affiliation)))
            ->orderBy('organization_name')->orderBy('first_name')->orderBy('last_name')
            ->paginate(25)
            ->withQueryString();

        return view('admin::contacts.index', [
            'contacts' => $contacts,
            'types' => AffiliationType::orderBy('sort')->get(),
            'filters' => compact('search', 'type', 'affiliation'),
        ]);
    }

    public function show(Contact $contact): View
    {
        $contact->load(['user', 'affiliations' => fn ($query) => $query->with('type.roles')->orderByDesc('started_at')]);

        return view('admin::contacts.show', [
            'contact' => $contact,
            'types' => AffiliationType::with('roles')->orderBy('sort')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin::contacts.form', ['contact' => new Contact()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $contact = Contact::create($this->validated($request));
        $this->set_log('create', "Kişi/kurum eklendi: {$contact->display_name} (#{$contact->id})");

        return redirect()->route('admin.contacts.show', $contact)->with('success-status', 'Kayıt eklendi.');
    }

    public function edit(Contact $contact): View
    {
        return view('admin::contacts.form', ['contact' => $contact]);
    }

    public function update(Request $request, Contact $contact): RedirectResponse
    {
        // A contact with an account mirrors it; its profile is edited there.
        if ($contact->user) {
            return redirect()->route('admin.contacts.show', $contact)->with('danger-status', 'Hesabı olan kişinin bilgileri hesap profilinden düzenlenir.');
        }

        $contact->update($this->validated($request));
        $this->set_log('change', "Kişi/kurum düzenlendi: {$contact->display_name} (#{$contact->id})");

        return redirect()->route('admin.contacts.show', $contact)->with('success-status', 'Kayıt güncellendi.');
    }

    private function validated(Request $request): array
    {
        $organization = $request->input('type') === Contact::TYPE_ORGANIZATION;

        $data = $request->validate([
            'type' => ['required', Rule::in([Contact::TYPE_PERSON, Contact::TYPE_ORGANIZATION])],
            'first_name' => [Rule::requiredIf(! $organization), 'nullable', 'string', 'max:255'],
            'last_name' => [Rule::requiredIf(! $organization), 'nullable', 'string', 'max:255'],
            'organization_name' => [Rule::requiredIf($organization), 'nullable', 'string', 'max:255'],
            // TC kimlik no has 11 digits, a tax number 10.
            'identity_number' => ['nullable', $organization ? 'digits:10' : 'digits:11'],
            'email' => ['nullable', 'email', 'max:100'],
            'phone' => ['nullable', 'string', 'max:30'],
            'birthday' => ['nullable', 'date', 'before:today'],
        ], [], [
            'first_name' => 'Ad',
            'last_name' => 'Soyad',
            'organization_name' => 'Kurum adı',
            'identity_number' => $organization ? 'Vergi no' : 'TC kimlik no',
            'email' => 'E-posta',
            'phone' => 'Telefon',
            'birthday' => 'Doğum tarihi',
        ]);

        // Only the fields of the chosen type are kept.
        if ($organization) {
            $data['first_name'] = $data['last_name'] = $data['birthday'] = null;
        } else {
            $data['organization_name'] = null;
        }

        return $data;
    }
}
