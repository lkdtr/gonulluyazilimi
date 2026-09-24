<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Agreement;
use App\Models\AgreementAcceptance;
use App\Models\AgreementVersion;
use App\Support\HtmlSanitizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AgreementAdminController extends Controller
{
    public function index(): View
    {
        return view('admin::agreements.index', [
            'agreements' => Agreement::with(['currentVersion', 'draft'])->orderBy('title')->get()
                ->each(fn (Agreement $agreement) => $agreement->setAttribute('acceptance_count', AgreementAcceptance::whereIn('agreement_version_id', $agreement->versions()->select('id'))->count())),
        ]);
    }

    public function create(): View
    {
        return view('admin::agreements.form', ['agreement' => new Agreement(), 'content' => '']);
    }

    public function store(Request $request, HtmlSanitizer $sanitizer): RedirectResponse
    {
        $data = $request->validate($this->rules() + [
            'key' => ['required', 'string', 'max:50', 'regex:/^[a-z0-9]+(-[a-z0-9]+)*$/', 'unique:agreements,key'],
        ], [], $this->attributes());

        $agreement = Agreement::create(['key' => $data['key'], 'title' => $data['title'], 'description' => $data['description'] ?? null]);
        $this->saveContent($request, $agreement, $sanitizer->sanitizePage($data['content']));
        $this->set_log('create', "Sözleşme eklendi: {$agreement->title}");

        return $this->afterSave($request, $agreement);
    }

    public function edit(Agreement $agreement): View
    {
        $agreement->load(['currentVersion', 'draft']);

        return view('admin::agreements.form', [
            'agreement' => $agreement,
            'content' => ($agreement->draft ?? $agreement->currentVersion)?->content ?? '',
        ]);
    }

    public function update(Request $request, Agreement $agreement, HtmlSanitizer $sanitizer): RedirectResponse
    {
        $data = $request->validate($this->rules(), [], $this->attributes());

        $agreement->update(['title' => $data['title'], 'description' => $data['description'] ?? null]);
        $this->saveContent($request, $agreement, $sanitizer->sanitizePage($data['content']));
        $this->set_log('change', "Sözleşme düzenlendi: {$agreement->title}");

        return $this->afterSave($request, $agreement);
    }

    public function discardDraft(Agreement $agreement): RedirectResponse
    {
        $agreement->draft()->delete();

        return redirect()->route('admin.agreements.edit', $agreement)->with('success-status', 'Taslak silindi.');
    }

    public function show(Request $request, Agreement $agreement): View
    {
        $versions = $agreement->versions()->whereNotNull('published_at')->withCount('acceptances')->with('publisher')->get();

        return view('admin::agreements.show', [
            'agreement' => $agreement,
            'versions' => $versions,
            'acceptances' => AgreementAcceptance::with(['version', 'user', 'contact'])
                ->whereIn('agreement_version_id', $versions->pluck('id'))
                ->when($request->integer('version'), fn ($query, $version) => $query->whereHas('version', fn ($query) => $query->where('version', $version)))
                ->latest('accepted_at')
                ->paginate(50)
                ->withQueryString(),
        ]);
    }

    /**
     * Keep the text as the draft; "publish" makes the draft the version in
     * force. A published version is never edited.
     */
    private function saveContent(Request $request, Agreement $agreement, string $content): void
    {
        DB::transaction(function () use ($request, $agreement, $content) {
            $current = $agreement->currentVersion()->first();
            $draft = $agreement->draft()->first();

            // Nothing to keep when the text equals the version in force and no draft exists.
            if (! $draft && $current && $current->content === $content) {
                return;
            }

            $draft ??= $agreement->versions()->make(['version' => (int) $agreement->versions()->max('version') + 1]);
            $draft->content = $content;
            $draft->save();

            if ($request->input('action') === 'publish') {
                $draft->forceFill(['published_at' => now(), 'published_by' => Auth::id()])->save();
                $this->set_log('change', "Sözleşme yayınlandı: {$agreement->title}, sürüm {$draft->version}");
            }
        });
    }

    private function afterSave(Request $request, Agreement $agreement): RedirectResponse
    {
        $message = $request->input('action') === 'publish'
            ? 'Sözleşmenin yeni sürümü yayınlandı. Bundan sonraki kabuller bu sürüme kaydedilir.'
            : 'Taslak kaydedildi; yayınlanana kadar formlarda eski sürüm geçerli.';

        return redirect()->route('admin.agreements.edit', $agreement)->with('success-status', $message);
    }

    private function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:1000'],
            'content' => ['required', 'string', 'max:200000'],
            'action' => ['required', 'in:draft,publish'],
        ];
    }

    private function attributes(): array
    {
        return ['key' => 'Anahtar', 'title' => 'Başlık', 'description' => 'Açıklama', 'content' => 'Metin'];
    }
}
