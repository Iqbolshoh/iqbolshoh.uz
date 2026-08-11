@extends('layouts.dashboard')

@section('title', 'Users')
@section('breadcrumb', 'Users')
@section('header_title', 'Users')

@section('content')
@php
    $sort = request('sort', 'id');
    $direction = request('direction', 'asc');
    $sortUrl = fn(string $col) => request()->fullUrlWithQuery([
        'sort' => $col,
        'direction' => ($sort === $col && $direction === 'asc') ? 'desc' : 'asc',
        'page' => 1,
    ]);
@endphp
<div x-data="{ deleteModalOpen: false, deleteUrl: '', deleteName: '' }">

    <div x-show="deleteModalOpen" x-cloak class="fixed inset-0 z-[60]" role="dialog" aria-modal="true" style="display:none;">
        <div x-show="deleteModalOpen"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            @click="deleteModalOpen = false"
            class="fixed inset-0 bg-black/70 backdrop-blur-sm cursor-pointer"></div>

        <div class="flex min-h-screen items-center justify-center p-4 relative z-10">
            <div x-show="deleteModalOpen"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                @click.away="deleteModalOpen = false"
                class="w-full max-w-md rounded-[var(--radius-lg)] bg-[var(--bg-raised)] border border-[var(--border-strong)] shadow-2xl shadow-black/60 p-8">

                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[var(--accent-soft)] border border-[var(--accent-border)]">
                    <x-lucide-trash-2 class="h-6 w-6 text-[var(--accent)]" />
                </div>

                <div class="mt-5 text-center">
                    <h3 class="text-xl font-bold text-white tracking-tight">Delete user</h3>
                    <p class="mt-2 text-sm text-[var(--text-secondary)] leading-relaxed">
                        Are you sure you want to delete
                        <span class="font-semibold text-white" x-text='"\"" + deleteName + "\""'></span>?
                        This cannot be undone.
                    </p>
                </div>

                <div class="mt-7 flex flex-col sm:flex-row gap-3">
                    <button type="button" @click="deleteModalOpen = false" class="btn-secondary flex-1">
                        Cancel
                    </button>
                    <form :action="deleteUrl" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-[0.625rem] rounded-[var(--radius-md)] text-sm font-semibold text-white bg-[var(--accent)] hover:bg-[var(--accent-hover)] transition-colors shadow-lg shadow-[var(--accent-glow)] cursor-pointer">
                            <x-lucide-trash-2 class="w-4 h-4" />
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <form method="GET" action="{{ request()->url() }}" class="flex items-center gap-2">
            @if(request('sort'))<input type="hidden" name="sort" value="{{ request('sort') }}">@endif
            @if(request('direction'))<input type="hidden" name="direction" value="{{ request('direction') }}">@endif
            <div class="relative">
                <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-[var(--text-muted)] pointer-events-none" />
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search users…"
                    class="pl-11 pr-4 py-2 text-sm rounded-[var(--radius-md)] bg-[var(--bg-surface)] border border-[var(--border-subtle)] text-white placeholder-[var(--text-muted)] focus:outline-none focus:border-[var(--accent)] w-56 transition-colors">
            </div>
            @if(request('search'))
            <a href="{{ request()->url() }}" class="p-2 rounded-lg text-[var(--text-muted)] hover:text-white hover:bg-white/5 transition-colors" title="Clear">
                <x-lucide-x class="w-4 h-4" />
            </a>
            @endif
        </form>

        @can('users.create')
        <a href="{{ route('users.create') }}" class="btn-primary self-start sm:self-auto">
            <x-lucide-user-plus class="w-4 h-4" />
            New user
        </a>
        @endcan
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto scroll-area">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-[var(--border-subtle)]" style="background: rgba(255,255,255,0.01);">
                        <th class="hidden sm:table-cell px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] w-16">
                            <a href="{{ $sortUrl('id') }}" class="inline-flex items-center gap-1 hover:text-white transition-colors {{ $sort === 'id' ? 'text-white' : '' }}">#
                                @if($sort === 'id') <x-lucide-chevron-up class="w-3 h-3 {{ $direction === 'desc' ? 'rotate-180' : '' }}" /> @else <x-lucide-chevrons-up-down class="w-3 h-3 opacity-40" /> @endif
                            </a>
                        </th>
                        <th class="px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-[var(--text-muted)]">
                            <a href="{{ $sortUrl('name') }}" class="inline-flex items-center gap-1 hover:text-white transition-colors {{ $sort === 'name' ? 'text-white' : '' }}">User
                                @if($sort === 'name') <x-lucide-chevron-up class="w-3 h-3 {{ $direction === 'desc' ? 'rotate-180' : '' }}" /> @else <x-lucide-chevrons-up-down class="w-3 h-3 opacity-40" /> @endif
                            </a>
                        </th>
                        <th class="hidden lg:table-cell px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-[var(--text-muted)]">
                            <a href="{{ $sortUrl('email') }}" class="inline-flex items-center gap-1 hover:text-white transition-colors {{ $sort === 'email' ? 'text-white' : '' }}">Email
                                @if($sort === 'email') <x-lucide-chevron-up class="w-3 h-3 {{ $direction === 'desc' ? 'rotate-180' : '' }}" /> @else <x-lucide-chevrons-up-down class="w-3 h-3 opacity-40" /> @endif
                            </a>
                        </th>
                        <th class="px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-[var(--text-muted)]">Role</th>
                        <th class="px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border-subtle)] text-sm">
                    @forelse($users as $user)
                    @php
                    $avatarColors = [
                    'bg-blue-500/20 text-blue-400',
                    'bg-indigo-500/20 text-indigo-400',
                    'bg-violet-500/20 text-violet-400',
                    'bg-sky-500/20 text-sky-400',
                    'bg-blue-600/20 text-blue-300',
                    ];
                    $avatarClass = $avatarColors[$user->id % count($avatarColors)];
                    $roleName = $user->getRoleNames()->first() ?? 'No role';
                    $roleStyle = match($roleName) {
                    'superadmin' => 'bg-[var(--accent-soft)] text-[var(--accent)] border border-[var(--accent-border)]',
                    default => 'bg-blue-500/10 text-blue-400 border border-blue-500/20',
                    };
                    @endphp
                    <tr class="transition-colors hover:bg-white/[0.02]">
                        <td class="hidden sm:table-cell px-6 py-4 text-[var(--text-muted)] font-mono text-xs">{{ $user->id }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-sm flex-shrink-0 {{ $avatarClass }}">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-white truncate">{{ $user->name }}</p>
                                    <p class="lg:hidden text-xs text-[var(--text-muted)] truncate">{{ $user->email }}</p>
                                    @if($user->id === 1)
                                    <p class="text-[0.65rem] text-[var(--text-muted)]">Primary account</p>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="hidden lg:table-cell px-6 py-4 text-[var(--text-secondary)]">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold uppercase tracking-wide {{ $roleStyle }}">
                                {{ $roleName === 'superadmin' ? 'Super Admin' : ucfirst(str_replace(['-', '_'], ' ', $roleName)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($user->id !== 1)
                            <div class="inline-flex items-center gap-1">
                                @can('users.edit')
                                <a href="{{ route('users.edit', $user) }}"
                                    class="p-2 rounded-lg text-[var(--text-muted)] hover:text-[var(--accent-hover)] hover:bg-[var(--accent-soft)] transition-colors"
                                    title="Edit">
                                    <x-lucide-pencil class="w-4 h-4" />
                                </a>
                                @endcan
                                @can('users.delete')
                                @if(auth()->id() !== $user->id)
                                <button type="button"
                                    @click="deleteUrl = @js(route('users.destroy', $user)); deleteName = @js($user->name); deleteModalOpen = true"
                                    class="p-2 rounded-lg text-[var(--text-muted)] hover:text-[var(--accent)] hover:bg-[var(--accent-soft)] transition-colors"
                                    title="Delete">
                                    <x-lucide-trash-2 class="w-4 h-4" />
                                </button>
                                @endif
                                @endcan
                            </div>
                            @else
                            <span class="text-xs text-[var(--text-muted)] italic px-2">Protected</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background: rgba(255,255,255,0.04); border: 1px solid var(--border-subtle);">
                                    <x-lucide-users class="w-6 h-6 text-[var(--text-muted)]" />
                                </div>
                                <p class="text-sm font-semibold text-[var(--text-secondary)]">No users found</p>
                                <p class="text-xs text-[var(--text-muted)]">Add the first user</p>
                                @can('users.create')
                                <a href="{{ route('users.create') }}" class="btn-primary !text-xs !py-2 !px-4 mt-1">
                                    <x-lucide-user-plus class="w-3.5 h-3.5" />
                                    New user
                                </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-[var(--border-subtle)]">
            {{ $users->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
