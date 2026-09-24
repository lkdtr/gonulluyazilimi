<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Modules\Permissions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class RoleController extends Controller
{
    public function __construct(private Permissions $permissions)
    {
    }

    public function index(): View
    {
        return view('admin::roles.index', [
            'roles' => Role::with('affiliationTypes')->withCount('users')->orderByDesc('is_system')->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin::roles.form', ['role' => new Role(), 'groups' => $this->permissions->grouped(), 'selected' => []]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate($this->rules() + [
            'key' => ['required', 'string', 'max:50', 'regex:/^[a-z0-9]+(-[a-z0-9]+)*$/', 'unique:roles,key'],
        ], [], $this->attributes());

        $role = Role::create($data);
        $role->syncPermissions($data['permissions'] ?? []);
        $this->set_log('create', "Rol eklendi: {$role->name}");

        return redirect()->route('admin.roles')->with('success-status', 'Rol eklendi.');
    }

    public function edit(Role $role): View
    {
        return view('admin::roles.form', ['role' => $role, 'groups' => $this->permissions->grouped(), 'selected' => $role->permissions()]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $data = $request->validate($this->rules(), [], $this->attributes());

        $role->update($data);
        // The owner role holds every permission regardless of the list.
        if (! $role->isOwner()) {
            $role->syncPermissions($data['permissions'] ?? []);
        }
        $this->set_log('change', "Rol düzenlendi: {$role->name}");

        return redirect()->route('admin.roles')->with('success-status', 'Rol güncellendi.');
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->is_system) {
            return back()->with('danger-status', 'Sistem rolleri silinemez.');
        }

        $role->delete();
        $this->set_log('delete', "Rol silindi: {$role->name}");

        return back()->with('success-status', 'Rol silindi.');
    }

    private function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'permissions' => ['array'],
            'permissions.*' => ['string', Rule::in($this->permissions->keys())],
        ];
    }

    private function attributes(): array
    {
        return ['key' => 'Anahtar', 'name' => 'Ad', 'description' => 'Açıklama', 'permissions' => 'Yetkiler'];
    }
}
