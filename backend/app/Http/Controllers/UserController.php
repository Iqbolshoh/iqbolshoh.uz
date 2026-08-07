<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $authUser */
        $authUser = Auth::user();
        abort_unless($authUser->hasPermissionTo('users.view'), 403);

        $search    = $request->input('search');
        $sort      = in_array($request->input('sort'), ['id', 'name', 'email']) ? $request->input('sort') : 'id';
        $direction = $request->input('direction') === 'desc' ? 'desc' : 'asc';

        $users = User::with('roles')
            ->when($search, fn($q) => $q->where(fn($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
            ))
            ->orderBy($sort, $direction)
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        /** @var User $authUser */
        $authUser = Auth::user();
        abort_unless($authUser->hasPermissionTo('users.create'), 403);

        $roles = Role::orderBy('name')->get();

        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        /** @var User $authUser */
        $authUser = Auth::user();
        abort_unless($authUser->hasPermissionTo('users.create'), 403);

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role'     => ['required', 'string', 'exists:roles,name'],
        ], [
            'name.required'      => 'Full name is required.',
            'email.required'     => 'Email address is required.',
            'email.email'        => 'Please enter a valid email address.',
            'email.unique'       => 'This email address is already registered.',
            'password.required'  => 'Password is required.',
            'password.min'       => 'Password must be at least 8 characters.',
            'password.confirmed' => 'Password confirmation does not match.',
            'role.required'      => 'Please assign a role to this user.',
            'role.exists'        => 'The selected role does not exist.',
        ]);

        $newUser = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $newUser->assignRole($data['role']);

        return redirect()->route('users.edit', $newUser)
            ->with('success', "User \"{$data['name']}\" created successfully.");
    }

    public function edit(User $user)
    {
        /** @var User $authUser */
        $authUser = Auth::user();
        abort_unless($authUser->hasPermissionTo('users.edit'), 403);

        if ($user->hasRole('superadmin')) {
            return redirect()->route('users.index')
                ->with('error', 'The Super Admin account cannot be modified.');
        }

        $roles    = Role::orderBy('name')->get();
        $userRole = $user->roles->first()?->name;

        return view('admin.users.edit', compact('user', 'roles', 'userRole'));
    }

    public function update(Request $request, User $user)
    {
        /** @var User $authUser */
        $authUser = Auth::user();
        abort_unless($authUser->hasPermissionTo('users.edit'), 403);

        if ($user->hasRole('superadmin')) {
            return redirect()->route('users.index')
                ->with('error', 'The Super Admin account cannot be modified.');
        }

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'role'     => ['required', 'string', 'exists:roles,name'],
        ]);

        $updates = [
            'name'  => $data['name'],
            'email' => $data['email'],
        ];

        if ($request->filled('password')) {
            $updates['password'] = Hash::make($data['password']);
        }

        $user->update($updates);
        $user->syncRoles([$data['role']]);

        return redirect()->route('users.edit', $user)
            ->with('success', "User \"{$data['name']}\" updated successfully.");
    }

    public function destroy(User $user)
    {
        /** @var User $authUser */
        $authUser = Auth::user();
        abort_unless($authUser->hasPermissionTo('users.delete'), 403);

        if ($user->hasRole('superadmin')) {
            return redirect()->route('users.index')
                ->with('error', 'The Super Admin account cannot be deleted.');
        }

        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot delete your own account.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', "User \"{$name}\" deleted.");
    }
}
