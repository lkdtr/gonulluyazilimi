<?php

namespace Modules\IdCard\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AffiliationType;
use App\Modules\ContactFields;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\IdCard\Models\IdCard;
use Modules\IdCard\Models\IdCardTemplate;
use Modules\IdCard\Support\CardView;

class TemplateController extends Controller
{
    public function index(CardView $view): View
    {
        $templates = IdCardTemplate::with('affiliationType')->withCount('cards')->get()
            ->sortBy(fn (IdCardTemplate $template) => $template->affiliationType->sort);

        return view('id-card::admin.templates.index', [
            'templates' => $templates->map(fn (IdCardTemplate $template) => [
                'template' => $template,
                'fields' => $view->sampleFields($template),
            ]),
            'typesWithoutTemplate' => AffiliationType::whereNotIn('id', $templates->pluck('affiliation_type_id'))->orderBy('sort')->get(),
        ]);
    }

    public function create(Request $request): View
    {
        $type = AffiliationType::whereDoesntHave('idCardTemplate')->findOrFail($request->integer('type'));

        return view('id-card::admin.templates.form', $this->formData(new IdCardTemplate([
            'affiliation_type_id' => $type->id,
            'name' => $type->name.' Kartı',
            'organization_name' => (string) config('app.name'),
        ])->setRelation('affiliationType', $type)));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules() + [
            'affiliation_type_id' => ['required', 'integer', Rule::exists('affiliation_types', 'id'), Rule::unique('id_card_templates', 'affiliation_type_id')],
        ], [], $this->attributes());

        $template = new IdCardTemplate($this->values($request, $data) + ['affiliation_type_id' => $data['affiliation_type_id']]);
        $this->storeLogo($request, $template);
        $template->save();
        $this->set_log('create', "Kimlik kartı şablonu eklendi: {$template->name}");

        return redirect()->route('admin.id-cards')->with('success-status', 'Kart şablonu eklendi.');
    }

    public function edit(IdCardTemplate $template): View
    {
        return view('id-card::admin.templates.form', $this->formData($template->load('affiliationType')));
    }

    public function update(Request $request, IdCardTemplate $template): RedirectResponse
    {
        $data = $request->validate($this->rules(), [], $this->attributes());

        $template->fill($this->values($request, $data));
        $this->storeLogo($request, $template);
        $template->save();
        $this->set_log('change', "Kimlik kartı şablonu düzenlendi: {$template->name}");

        return redirect()->route('admin.id-cards')->with('success-status', 'Kart şablonu güncellendi.');
    }

    public function destroy(IdCardTemplate $template): RedirectResponse
    {
        if ($template->cards()->exists()) {
            return back()->with('danger-status', 'Kart verilmiş bir şablon silinemez; kapatabilirsiniz.');
        }

        $template->delete();
        $this->set_log('delete', "Kimlik kartı şablonu silindi: {$template->name}");

        return back()->with('success-status', 'Kart şablonu silindi.');
    }

    private function formData(IdCardTemplate $template): array
    {
        return [
            'template' => $template,
            'fieldOptions' => app(ContactFields::class)->options(),
            'maxFields' => IdCardTemplate::MAX_FIELDS,
            'numberExample' => $template->formatNumber(1),
        ];
    }

    private function rules(): array
    {
        $color = ['required', 'regex:/^#[0-9a-fA-F]{6}$/'];

        return [
            'name' => ['required', 'string', 'max:100'],
            'organization_name' => ['required', 'string', 'max:150'],
            'background_color' => $color,
            'text_color' => $color,
            'accent_color' => $color,
            'number_prefix' => ['nullable', 'string', 'max:10', 'regex:/^[A-Za-z0-9-]*$/'],
            'number_digits' => ['required', 'integer', 'min:1', 'max:10'],
            'fields' => ['array', 'max:'.IdCardTemplate::MAX_FIELDS],
            'fields.*' => ['string', Rule::in(array_keys(app(ContactFields::class)->options()))],
            'footer_text' => ['nullable', 'string', 'max:150'],
            'logo' => ['nullable', 'file', 'image', 'mimes:png,jpeg,webp', 'max:1024'],
        ];
    }

    private function attributes(): array
    {
        return [
            'name' => 'Kart adı', 'organization_name' => 'Kurum adı', 'background_color' => 'Arka plan rengi',
            'text_color' => 'Yazı rengi', 'accent_color' => 'Vurgu rengi', 'number_prefix' => 'Kart no öneki',
            'number_digits' => 'Kart no hane sayısı', 'fields' => 'Alanlar', 'footer_text' => 'Alt yazı', 'logo' => 'Logo',
        ];
    }

    private function values(Request $request, array $data): array
    {
        return [
            'name' => $data['name'],
            'organization_name' => $data['organization_name'],
            'background_color' => strtolower($data['background_color']),
            'text_color' => strtolower($data['text_color']),
            'accent_color' => strtolower($data['accent_color']),
            'number_prefix' => (string) ($data['number_prefix'] ?? ''),
            'number_digits' => $data['number_digits'],
            'fields' => array_values(array_unique($data['fields'] ?? [])),
            'footer_text' => $data['footer_text'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'requires_photo' => $request->boolean('requires_photo'),
        ];
    }

    private function storeLogo(Request $request, IdCardTemplate $template): void
    {
        $remove = $request->boolean('remove_logo') || $request->hasFile('logo');

        if ($remove && $template->logo_path) {
            Storage::disk('local')->delete($template->logo_path);
            $template->logo_path = null;
        }

        if ($request->hasFile('logo')) {
            $template->logo_path = $request->file('logo')->store(IdCardTemplate::LOGO_DIRECTORY, 'local');
        }
    }
}
