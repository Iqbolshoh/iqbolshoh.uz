<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_unless($user->hasPermissionTo('roles.view'), 403);

        $search    = $request->input('search');
        $sort      = in_array($request->input('sort'), ['id', 'name']) ? $request->input('sort') : 'id';
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';

        $roles = Role::with('permissions')
            ->when($search, fn($q) => $q->where('name', 'like', "%{$search}%"))
            ->orderBy($sort, $direction)
            ->paginate(10)
            ->withQueryString();

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_unless($user->hasPermissionTo('roles.create'), 403);

        $groupedPermissions = Permission::orderBy('name')->get()
            ->reject(fn(Permission $p) => str_contains($p->name, 'manage.all'))
            ->groupBy(fn(Permission $p) => explode('.', $p->name)[0]);

        return view('admin.roles.create', compact('groupedPermissions'));
    }

    public function store(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_unless($user->hasPermissionTo('roles.create'), 403);

        $data = $request->validate([
            'name'          => ['required', 'string', 'max:64', 'regex:/^[A-Za-z0-9_-]+$/', Rule::unique('roles', 'name')],
            'permissions'   => ['required', 'array', 'min:1'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ], [
            'name.required'        => 'Role name is required.',
            'name.max'             => 'Role name must not exceed 64 characters.',
            'name.regex'           => 'Role name may only contain letters, numbers, underscores, and dashes.',
            'name.unique'          => 'A role with this name already exists.',
            'permissions.required' => 'Please assign at least one permission.',
            'permissions.min'      => 'Please assign at least one permission.',
        ]);

        if ($this->isProtectedRoleName($data['name'])) {
            return back()->withInput()->with('error', "The role name \"{$data['name']}\" is reserved.");
        }

        $role = Role::create([
            'name'       => $data['name'],
            'guard_name' => 'web',
        ]);
        $role->syncPermissions($data['permissions']);

        return redirect()->route('roles.edit', $role)
            ->with('success', "Role \"{$role->name}\" created successfully.");
    }

    public function edit(Role $role)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_unless($user->hasPermissionTo('roles.edit'), 403);

        if ($this->isProtectedRole($role)) {
            return redirect()->route('roles.index')
                ->with('error', "The \"{$role->name}\" role is protected and cannot be modified.");
        }

        $groupedPermissions = Permission::orderBy('name')->get()
            ->reject(fn(Permission $p) => str_contains($p->name, 'manage.all'))
            ->groupBy(fn(Permission $p) => explode('.', $p->name)[0]);

        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('admin.roles.edit', compact('role', 'groupedPermissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_unless($user->hasPermissionTo('roles.edit'), 403);

        if ($this->isProtectedRole($role)) {
            return redirect()->route('roles.index')
                ->with('error', "The \"{$role->name}\" role is protected and cannot be modified.");
        }

        $data = $request->validate([
            'name'          => ['required', 'string', 'max:64', 'regex:/^[A-Za-z0-9_-]+$/', Rule::unique('roles', 'name')->ignore($role)],
            'permissions'   => ['required', 'array', 'min:1'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ], [
            'name.required'        => 'Role name is required.',
            'name.unique'          => 'A role with this name already exists.',
            'permissions.required' => 'Please assign at least one permission.',
        ]);

        if ($this->isProtectedRoleName($data['name'])) {
            return back()->withInput()->with('error', "The role name \"{$data['name']}\" is reserved.");
        }

        $role->update([
            'name' => $data['name'],
        ]);
        $role->syncPermissions($data['permissions']);

        return redirect()->route('roles.edit', $role)
            ->with('success', "Role \"{$role->name}\" updated successfully.");
    }

    public function destroy(Role $role)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        abort_unless($user->hasPermissionTo('roles.delete'), 403);

        if ($this->isProtectedRole($role)) {
            return redirect()->route('roles.index')
                ->with('error', "The \"{$role->name}\" role is protected and cannot be deleted.");
        }

        $roleName = $role->name;
        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', "Role \"{$roleName}\" deleted.");
    }

    private function isProtectedRole(Role $role): bool
    {
        return strtolower($role->name) === 'superadmin';
    }

    private function isProtectedRoleName(string $name): bool
    {
        return strtolower($name) === 'superadmin';
    }
}
