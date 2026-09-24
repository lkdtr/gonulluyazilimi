<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\CustomField;
use App\Support\CustomFields;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomFieldController extends Controller
{
    public function index(CustomFields $customFields): View
    {
        return view('admin::custom-fields.index', [
            'groups' => $customFields->groups(),
            'fields' => CustomField::withCount('values')->orderBy('sort')->orderBy('id')->get()->groupBy('group'),
        ]);
    }

    public function create(CustomFields $customFields): View
    {
        return view('admin::custom-fields.form', ['field' => new CustomField(), 'groups' => $customFields->groups()]);
    }

    public function store(Request $request, CustomFields $customFields): RedirectResponse
    {
        $data = $request->validate($this->rules($customFields) + [
            'key' => ['required', 'string', 'max:50', 'regex:/^[a-z0-9]+(_[a-z0-9]+)*$/', 'unique:custom_fields,key'],
            'type' => ['required', Rule::in(array_keys(CustomField::TYPES))],
        ], [], $this->attributes());

        $field = CustomField::create($this->values($request, $data) + ['key' => $data['key'], 'type' => $data['type']]);

        return redirect()->route('admin.custom-fields')->with('success-status', "\"{$field->label}\" alanı eklendi.");
    }

    public function edit(CustomField $customField, CustomFields $customFields): View
    {
        return view('admin::custom-fields.form', ['field' => $customField, 'groups' => $customFields->groups()]);
    }

    public function update(Request $request, CustomField $customField, CustomFields $customFields): RedirectResponse
    {
        $data = $request->validate($this->rules($customFields, $customField->type), [], $this->attributes());

        $customField->update($this->values($request, $data, $customField->type));

        return redirect()->route('admin.custom-fields')->with('success-status', 'Alan güncellendi.');
    }

    public function destroy(CustomField $customField): RedirectResponse
    {
        $customField->delete();

        return redirect()->route('admin.custom-fields')->with('success-status', "\"{$customField->label}\" alanı ve kişilerdeki değerleri silindi.");
    }

    private function rules(CustomFields $customFields, ?string $type = null): array
    {
        $type ??= request('type');

        return [
            'label' => ['required', 'string', 'max:100'],
            'group' => ['required', Rule::in(array_keys($customFields->groups()))],
            'options' => [$type === 'select' ? 'required' : 'nullable', 'string', 'max:5000'],
            'help' => ['nullable', 'string', 'max:500'],
            'member_access' => ['required', Rule::in(array_keys(CustomField::ACCESS))],
            'applies_to' => ['required', Rule::in(array_keys(CustomField::APPLIES_TO))],
            'sort' => ['required', 'integer', 'min:0', 'max:10000'],
        ];
    }

    private function values(Request $request, array $data, ?string $type = null): array
    {
        $type ??= $data['type'];
        $options = collect(preg_split('/\R/', (string) ($data['options'] ?? '')))->map(fn ($option) => trim($option))->filter()->unique()->values()->all();

        return [
            'label' => $data['label'],
            'group' => $data['group'],
            'options' => $type === 'select' ? $options : null,
            'help' => $data['help'] ?? null,
            'member_access' => $data['member_access'],
            'applies_to' => $data['applies_to'],
            'sort' => $data['sort'],
            'is_required' => $request->boolean('is_required'),
            'is_active' => $request->boolean('is_active'),
        ];
    }

    private function attributes(): array
    {
        return ['key' => 'Anahtar', 'label' => 'Ad', 'group' => 'Grup', 'type' => 'Tür', 'options' => 'Seçenekler', 'help' => 'Açıklama', 'member_access' => 'Kişinin erişimi', 'applies_to' => 'Uygulandığı kayıt', 'sort' => 'Sıra'];
    }
}
