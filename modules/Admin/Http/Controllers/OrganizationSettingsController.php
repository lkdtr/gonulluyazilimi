<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Support\HtmlSanitizer;
use App\Support\Organization;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class OrganizationSettingsController extends Controller
{
    /** Text settings and their rules; files and the home page are handled apart. */
    private const TEXT_RULES = [
        'name' => ['required', 'string', 'max:150'],
        'short_name' => ['nullable', 'string', 'max:30'],
        'website_url' => ['nullable', 'url:http,https', 'max:255'],
        'primary_color' => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
        'contact_email' => ['nullable', 'email', 'max:150'],
        'phone' => ['nullable', 'string', 'max:30'],
        'address' => ['nullable', 'string', 'max:255'],
        'notification_email' => ['nullable', 'email', 'max:150'],
        'sender_email' => ['nullable', 'email', 'max:150'],
        'sender_name' => ['nullable', 'string', 'max:150'],
        'source_url' => ['nullable', 'url:http,https', 'max:255'],
        'home_title' => ['nullable', 'string', 'max:150'],
        'ga_measurement_id' => ['nullable', 'regex:/^G-[A-Z0-9]{4,20}$/'],
        'gtm_container_id' => ['nullable', 'regex:/^GTM-[A-Z0-9]{4,20}$/'],
    ];

    public function edit(Organization $organization): View
    {
        return view('admin::settings.organization', ['organization' => $organization]);
    }

    public function update(Request $request, Organization $organization, HtmlSanitizer $sanitizer): RedirectResponse
    {
        $socialRules = array_fill_keys(array_keys(Organization::SOCIALS), ['nullable', 'url:http,https', 'max:255']);

        $data = $request->validate(self::TEXT_RULES + $socialRules + [
            'frame_ancestors' => ['nullable', 'string', 'max:1000', function ($attribute, $value, $fail) {
                foreach (preg_split('/\s+/', trim((string) $value)) as $origin) {
                    if ($origin !== '' && ! preg_match('#^https://[a-z0-9.-]+(:\d+)?$#i', $origin)) {
                        $fail("Yalnız https adresi girin (ör. https://www.ornek.org.tr): {$origin}");
                    }
                }
            }],
            'home_content' => ['nullable', 'string', 'max:100000'],
            'logo' => ['nullable', 'file', 'image', 'mimes:png,jpeg,webp', 'max:2048'],
            'favicon' => ['nullable', 'file', 'mimes:png,ico', 'max:512'],
        ], [], $this->attributes());

        $values = collect($data)->only(array_merge(array_keys(self::TEXT_RULES), array_keys($socialRules)))
            ->map(fn ($value) => $value === null ? null : trim($value))
            ->all();
        $values['primary_color'] = isset($values['primary_color']) ? strtolower($values['primary_color']) : null;
        $values['frame_ancestors'] = trim(implode("\n", preg_split('/\s+/', trim((string) ($data['frame_ancestors'] ?? ''))))) ?: null;
        $values['home_content'] = trim($sanitizer->sanitizePage($data['home_content'] ?? '')) ?: null;

        foreach (['logo' => 'logo_path', 'favicon' => 'favicon_path'] as $field => $key) {
            $old = $organization->get($key);
            if ($request->hasFile($field) || $request->boolean("remove_{$field}")) {
                $old && Storage::disk('local')->delete($old);
                $values[$key] = $request->hasFile($field) ? $request->file($field)->store(Organization::DIRECTORY, 'local') : null;
            }
        }

        $organization->save($values);
        $this->set_log('change', 'Kurum ayarları güncellendi.');

        return redirect()->route('admin.settings.organization')->with('success-status', 'Kurum ayarları kaydedildi.');
    }

    /**
     * Image upload of the home page editor; answers with the image address.
     */
    public function uploadImage(Request $request): JsonResponse
    {
        $request->validate(['file' => ['required', 'file', 'image', 'mimes:png,jpeg,webp,gif', 'max:4096']]);

        $file = $request->file('file');
        $name = Str::random(24).'.'.$file->extension();
        $file->storeAs(Organization::DIRECTORY.'/images', $name, 'local');

        return response()->json(['location' => route('organization.image', $name)]);
    }

    private function attributes(): array
    {
        return [
            'name' => 'Kurum adı', 'short_name' => 'Kısa ad', 'website_url' => 'Web sitesi', 'primary_color' => 'Ana renk',
            'contact_email' => 'İletişim e-postası', 'phone' => 'Telefon', 'address' => 'Adres',
            'notification_email' => 'Bildirim adresi', 'sender_email' => 'Gönderici adresi', 'sender_name' => 'Gönderici adı',
            'source_url' => 'Kaynak kodu adresi', 'home_title' => 'Ana sayfa başlığı', 'home_content' => 'Ana sayfa içeriği',
            'ga_measurement_id' => 'Google Analytics ölçüm kimliği', 'gtm_container_id' => 'Google Tag Manager kimliği',
            'frame_ancestors' => 'Gömülebilecek siteler', 'logo' => 'Logo', 'favicon' => 'Favicon',
        ] + collect(Organization::SOCIALS)->map(fn ($social) => $social[0])->all();
    }
}
