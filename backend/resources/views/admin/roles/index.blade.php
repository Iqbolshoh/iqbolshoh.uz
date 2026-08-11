@extends('layouts.dashboard')

@section('title', 'Roles')
@section('breadcrumb', 'Roles')
@section('header_title', 'Roles')

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
                    <h3 class="text-xl font-bold text-white tracking-tight">Delete role</h3>
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
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search roles…"
                    class="pl-11 pr-4 py-2 text-sm rounded-[var(--radius-md)] bg-[var(--bg-surface)] border border-[var(--border-subtle)] text-white placeholder-[var(--text-muted)] focus:outline-none focus:border-[var(--accent)] w-56 transition-colors">
            </div>
            @if(request('search'))
            <a href="{{ request()->url() }}" class="p-2 rounded-lg text-[var(--text-muted)] hover:text-white hover:bg-white/5 transition-colors" title="Clear">
                <x-lucide-x class="w-4 h-4" />
            </a>
            @endif
        </form>

        @can('roles.create')
        <a href="{{ route('roles.create') }}" class="btn-primary self-start sm:self-auto">
            <x-lucide-plus class="w-4 h-4" />
            New role
        </a>
        @endcan
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto scroll-area">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-[var(--border-subtle)]" style="background: rgba(255,255,255,0.01);">
                        <th class="px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] w-16">
                            <a href="{{ $sortUrl('id') }}" class="inline-flex items-center gap-1 hover:text-white transition-colors {{ $sort === 'id' ? 'text-white' : '' }}">#
                                @if($sort === 'id') <x-lucide-chevron-up class="w-3 h-3 {{ $direction === 'desc' ? 'rotate-180' : '' }}" /> @else <x-lucide-chevrons-up-down class="w-3 h-3 opacity-40" /> @endif
                            </a>
                        </th>
                        <th class="px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-[var(--text-muted)]">
                            <a href="{{ $sortUrl('name') }}" class="inline-flex items-center gap-1 hover:text-white transition-colors {{ $sort === 'name' ? 'text-white' : '' }}">Rol nomi
                                @if($sort === 'name') <x-lucide-chevron-up class="w-3 h-3 {{ $direction === 'desc' ? 'rotate-180' : '' }}" /> @else <x-lucide-chevrons-up-down class="w-3 h-3 opacity-40" /> @endif
                            </a>
                        </th>
                        <th class="px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-[var(--text-muted)]">Permissions</th>
                        <th class="px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border-subtle)] text-sm">
                    @forelse($roles as $role)
                    <tr class="transition-colors hover:bg-white/[0.02]">
                        <td class="px-6 py-4 text-[var(--text-muted)] font-mono text-xs">{{ $role->id }}</td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-lg text-sm font-semibold whitespace-nowrap
                                @if($role->name === 'superadmin') bg-[var(--accent-soft)] text-[var(--accent)] border border-[var(--accent-border)]
                                @else bg-blue-500/10 text-blue-400 border border-blue-500/20
                                @endif">
                                @if($role->name === 'superadmin')
                                <x-lucide-shield class="w-3.5 h-3.5" />
                                @endif
                                {{ $role->name === 'superadmin' ? 'Super Admin' : ucfirst(str_replace(['-', '_'], ' ', $role->name)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($role->permissions->isEmpty())
                            <span class="text-[var(--text-muted)] text-xs italic">No permissions</span>
                            @else
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($role->permissions as $perm)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium whitespace-nowrap"
                                    style="background: rgba(255,255,255,0.05); color: var(--text-secondary); border: 1px solid var(--border-subtle);">
                                    {{ $perm->name }}
                                </span>
                                @endforeach
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            @if($role->name !== 'superadmin')
                            <div class="inline-flex items-center gap-1">
                                @can('roles.edit')
                                <a href="{{ route('roles.edit', $role) }}"
                                    class="p-2 rounded-lg text-[var(--text-muted)] hover:text-[var(--accent-hover)] hover:bg-[var(--accent-soft)] transition-colors"
                                    title="Edit">
                                    <x-lucide-pencil class="w-4 h-4" />
                                </a>
                                @endcan
                                @can('roles.delete')
                                <button type="button"
                                    @click="deleteUrl = @js(route('roles.destroy', $role)); deleteName = @js($role->name); deleteModalOpen = true"
                                    class="p-2 rounded-lg text-[var(--text-muted)] hover:text-[var(--accent)] hover:bg-[var(--accent-soft)] transition-colors"
                                    title="Delete">
                                    <x-lucide-trash-2 class="w-4 h-4" />
                                </button>
                                @endcan
                            </div>
                            @else
                            <span class="text-xs text-[var(--text-muted)] italic px-2 whitespace-nowrap">Protected</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background: rgba(255,255,255,0.04); border: 1px solid var(--border-subtle);">
                                    <x-lucide-shield-off class="w-6 h-6 text-[var(--text-muted)]" />
                                </div>
                                <p class="text-sm font-semibold text-[var(--text-secondary)]">No roles found</p>
                                <p class="text-xs text-[var(--text-muted)]">Create the first role</p>
                                @can('roles.create')
                                <a href="{{ route('roles.create') }}" class="btn-primary !text-xs !py-2 !px-4 mt-1">
                                    <x-lucide-plus class="w-3.5 h-3.5" />
                                    New role
                                </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($roles->hasPages())
        <div class="px-6 py-4 border-t border-[var(--border-subtle)]">
            {{ $roles->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
