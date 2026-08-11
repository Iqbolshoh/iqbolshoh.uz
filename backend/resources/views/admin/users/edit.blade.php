@extends('layouts.dashboard')

@section('title', 'Edit user')
@section('breadcrumb', 'Users')
@section('header_title', 'Edit user')

@section('content')
<div class="max-w-3xl mx-auto">

    <a href="{{ route('users.index') }}"
       class="inline-flex items-center gap-1.5 text-sm font-medium text-[var(--text-muted)] hover:text-white transition-colors mb-6">
        <x-lucide-arrow-left class="w-4 h-4" />
        Usersga qaytish
    </a>

    <div class="card p-6 sm:p-8">
        <div class="mb-8 pb-6 border-b border-[var(--border-subtle)] flex items-center gap-4">
            <div class="flex-1">
                <h2 class="text-xl font-bold text-white tracking-tight">Edit user</h2>
                <p class="text-sm text-[var(--text-muted)] mt-1">
                    Tahrirlanayotgan: <span class="font-semibold text-[var(--text-secondary)]">{{ $user->name }}</span>
                </p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-[var(--accent-soft)] text-[var(--accent-hover)] border border-[var(--accent-border)]">
                ID {{ $user->id }}
            </span>
        </div>

        <form action="{{ route('users.update', $user) }}" method="POST" novalidate>
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="name" class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">
                        Full name <span class="text-[var(--accent)]">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}"
                           class="input @error('name') is-invalid @enderror"
                           required autocomplete="name">
                    @error('name')
                        <p class="text-[var(--accent)] text-xs mt-2 flex items-center gap-1">
                            <x-lucide-alert-circle class="w-3.5 h-3.5 flex-shrink-0" />{{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">
                        Email manzil <span class="text-[var(--accent)]">*</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}"
                           class="input @error('email') is-invalid @enderror"
                           required autocomplete="email">
                    @error('email')
                        <p class="text-[var(--accent)] text-xs mt-2 flex items-center gap-1">
                            <x-lucide-alert-circle class="w-3.5 h-3.5 flex-shrink-0" />{{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 pb-6 border-b border-[var(--border-subtle)]">
                <div x-data="{ show: false }">
                    <label for="password" class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">
                        New password <span class="text-[var(--text-muted)] font-normal">(optional)</span>
                    </label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'" id="password" name="password"
                               class="input pr-11 @error('password') is-invalid @enderror"
                               placeholder="Leave blank to keep the current one" autocomplete="new-password">
                        <button type="button" @click="show = !show"
                                class="absolute right-3 top-1/2 -translate-y-1/2 p-1 rounded-md text-[var(--text-muted)] hover:text-[var(--text-secondary)] transition-colors">
                            <x-lucide-eye-off x-show="show" class="w-4 h-4" />
                            <x-lucide-eye x-show="!show" class="w-4 h-4" />
                        </button>
                    </div>
                    @error('password')
                        <p class="text-[var(--accent)] text-xs mt-2 flex items-center gap-1">
                            <x-lucide-alert-circle class="w-3.5 h-3.5 flex-shrink-0" />{{ $message }}
                        </p>
                    @enderror
                </div>

                <div x-data="{ show: false }">
                    <label for="password_confirmation" class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">
                        Confirm new password
                    </label>
                    <div class="relative">
                        <input :type="show ? 'text' : 'password'" id="password_confirmation" name="password_confirmation"
                               class="input pr-11" placeholder="Repeat the new password" autocomplete="new-password">
                        <button type="button" @click="show = !show"
                                class="absolute right-3 top-1/2 -translate-y-1/2 p-1 rounded-md text-[var(--text-muted)] hover:text-[var(--text-secondary)] transition-colors">
                            <x-lucide-eye-off x-show="show" class="w-4 h-4" />
                            <x-lucide-eye x-show="!show" class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            </div>

            <div class="mb-8">
                <label for="role" class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">
                    Role <span class="text-[var(--accent)]">*</span>
                </label>
                <select name="role" id="role"
                        class="input cursor-pointer @error('role') is-invalid @enderror" required>
                    <option value="" disabled>Choose a role</option>
                    @foreach($roles as $role)
                    <option value="{{ $role->name }}"
                            {{ old('role', $userRole) === $role->name ? 'selected' : '' }}>
                        {{ $role->name === 'superadmin' ? 'Super Admin' : ucfirst(str_replace(['-', '_'], ' ', $role->name)) }}
                    </option>
                    @endforeach
                </select>
                @error('role')
                    <p class="text-[var(--accent)] text-xs mt-2 flex items-center gap-1">
                        <x-lucide-alert-circle class="w-3.5 h-3.5 flex-shrink-0" />{{ $message }}
                    </p>
                @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-[var(--border-subtle)]">
                <a href="{{ route('users.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">
                    <x-lucide-save class="w-4 h-4" />
                    O'zgarishlarni saqlash
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
