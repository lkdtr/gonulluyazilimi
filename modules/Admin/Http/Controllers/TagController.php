<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TagController extends Controller
{
    public function index(): View
    {
        return view('admin::tags.index', ['tags' => Tag::withCount('contacts')->orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $tag = Tag::create($request->validate($this->rules()));

        return back()->with('success-status', "\"{$tag->name}\" etiketi eklendi.");
    }

    public function update(Request $request, Tag $tag): RedirectResponse
    {
        $tag->update($request->validate($this->rules($tag)));

        return back()->with('success-status', 'Etiket güncellendi.');
    }

    public function destroy(Tag $tag): RedirectResponse
    {
        $tag->delete();

        return back()->with('success-status', "\"{$tag->name}\" etiketi silindi; kişilerden de kaldırıldı.");
    }

    private function rules(?Tag $tag = null): array
    {
        return [
            'name' => ['required', 'string', 'max:60', Rule::unique('tags', 'name')->ignore($tag)],
            'color' => ['required', Rule::in(Tag::COLORS)],
        ];
    }
}
