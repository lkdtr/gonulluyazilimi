<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AffiliationType;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AffiliationTypeController extends Controller
{
    public function index(): View
    {
        return view('admin::affiliation-types.index', [
            'types' => AffiliationType::with('roles')
                ->withCount(['affiliations as active_count' => fn ($query) => $query->active()])
                ->orderBy('sort')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin::affiliation-types.form', $this->formData(new AffiliationType(['sort' => 100])));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules() + [
            'key' => ['required', 'string', 'max:50', 'regex:/^[a-z0-9]+(-[a-z0-9]+)*$/', 'unique:affiliation_types,key'],
        ], [], $this->attributes());

        $type = AffiliationType::create($data + ['has_term' => $request->boolean('has_term')]);
        $this->syncRoles($request, $type);
        $this->set_log('create', "Sıfat türü eklendi: {$type->name}");

        return redirect()->route('admin.affiliation-types')->with('success-status', 'Sıfat eklendi.');
    }

    public function edit(AffiliationType $affiliationType): View
    {
        return view('admin::affiliation-types.form', $this->formData($affiliationType));
    }

    public function update(Request $request, AffiliationType $affiliationType): RedirectResponse
    {
        $data = $request->validate($this->rules(), [], $this->attributes());

        $affiliationType->update($data + ['has_term' => $request->boolean('has_term')]);
        $this->syncRoles($request, $affiliationType);
        $this->set_log('change', "Sıfat türü düzenlendi: {$affiliationType->name}");

        return redirect()->route('admin.affiliation-types')->with('success-status', 'Sıfat güncellendi.');
    }

    public function destroy(AffiliationType $affiliationType): RedirectResponse
    {
        if ($affiliationType->is_system || $affiliationType->affiliations()->exists()) {
            return back()->with('danger-status', 'Sistem sıfatları ve kişilere verilmiş sıfatlar silinemez.');
        }

        $affiliationType->delete();
        $this->set_log('delete', "Sıfat türü silindi: {$affiliationType->name}");

        return back()->with('success-status', 'Sıfat silindi.');
    }

    private function formData(AffiliationType $type): array
    {
        return [
            'type' => $type,
            'roles' => Role::orderBy('name')->get(),
            'selectedRoles' => $type->exists ? $type->roles()->pluck('roles.id')->all() : [],
            'canManageRoles' => Auth::user()->hasPermission('roles.manage'),
        ];
    }

    private function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort' => ['required', 'integer', 'min:0', 'max:10000'],
            'roles' => ['array'],
            'roles.*' => ['integer', Rule::exists('roles', 'id')],
        ];
    }

    private function attributes(): array
    {
        return ['key' => 'Anahtar', 'name' => 'Ad', 'description' => 'Açıklama', 'sort' => 'Sıra', 'roles' => 'Roller'];
    }

    /**
     * The role template is changed only by those who manage roles; the owner
     * role only by owners.
     */
    private function syncRoles(Request $request, AffiliationType $type): void
    {
        if (! Auth::user()->hasPermission('roles.manage')) {
            return;
        }

        $owner = Role::findByKey(Role::OWNER)->id;
        $roles = collect($request->input('roles', []))->map(fn ($id) => (int) $id);

        if (! Auth::user()->isOwner()) {
            $hadOwner = $type->roles()->where('roles.id', $owner)->exists();
            $roles = $roles->reject(fn ($id) => $id === $owner);
            if ($hadOwner) {
                $roles->push($owner);
            }
        }

        $type->roles()->sync($roles->unique()->all());
    }
}
