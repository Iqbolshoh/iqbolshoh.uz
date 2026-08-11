@extends('layouts.dashboard')

@section('title', 'Edit role')
@section('breadcrumb', 'Roles')
@section('header_title', 'Edit role')

@section('content')
<div class="max-w-4xl mx-auto">

    <a href="{{ route('roles.index') }}"
       class="inline-flex items-center gap-1.5 text-sm font-medium text-[var(--text-muted)] hover:text-white transition-colors mb-6">
        <x-lucide-arrow-left class="w-4 h-4" />
        Rolesga qaytish
    </a>

    <div class="card p-6 sm:p-8">
        <div class="mb-8 pb-6 border-b border-[var(--border-subtle)] flex items-center gap-4">
            <div class="flex-1">
                <h2 class="text-xl font-bold text-white tracking-tight">Edit role</h2>
                <p class="text-sm text-[var(--text-muted)] mt-1">
                    Tahrirlanayotgan: <span class="font-semibold text-[var(--text-secondary)]">{{ ucfirst(str_replace(['-', '_'], ' ', $role->name)) }}</span>
                </p>
            </div>
            <span class="inline-flex items-center px-3 py-1 rounded-lg text-xs font-bold bg-[var(--accent-soft)] text-[var(--accent-hover)] border border-[var(--accent-border)]">
                ID {{ $role->id }}
            </span>
        </div>

        <form action="{{ route('roles.update', $role) }}" method="POST" novalidate>
            @csrf
            @method('PUT')

            <div class="mb-8">
                <label for="name" class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">
                    Rol nomi <span class="text-[var(--accent)]">*</span>
                </label>
                <input type="text"
                       id="name"
                       name="name"
                       value="{{ old('name', $role->name) }}"
                       class="input @error('name') is-invalid @enderror"
                       placeholder="e.g. Editor, Support"
                       pattern="[A-Za-z0-9_\-]+"
                       maxlength="64"
                       required
                       autocomplete="off">
                @error('name')
                    <p class="text-[var(--accent)] text-xs mt-2 flex items-center gap-1">
                        <x-lucide-alert-circle class="w-3.5 h-3.5 flex-shrink-0" />
                        {{ $message }}
                    </p>
                @else
                    <p class="text-[var(--text-muted)] text-xs mt-2">
                        Faqat harflar, raqamlar, pastki chiziq va tire. Masalan: Editor, Support_Agent
                    </p>
                @enderror
            </div>

            <div class="mb-8" x-data="permissionsManager()">
                <div class="flex items-center justify-between mb-4">
                    <label class="block text-sm font-semibold text-[var(--text-secondary)]">
                        Ruxsatlar <span class="text-[var(--accent)]">*</span>
                    </label>
                    <button type="button" @click="toggleAll"
                            class="btn-ghost !text-xs !py-1.5 !px-3">
                        <span x-text="allSelected() ? 'Barchasini olib tashlash' : 'Barchasini tanlash'"></span>
                    </button>
                </div>

                @error('permissions')
                    <p class="text-[var(--accent)] text-xs mb-4 flex items-center gap-1">
                        <x-lucide-alert-circle class="w-3.5 h-3.5 flex-shrink-0" />
                        {{ $message }}
                    </p>
                @enderror

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($groupedPermissions as $groupName => $perms)
                    <div class="rounded-[var(--radius-md)] p-4 border border-[var(--border-subtle)]"
                         style="background: var(--bg-surface);">
                        <div class="flex items-center justify-between mb-3 pb-2.5 border-b border-[var(--border-subtle)]">
                            <h3 class="text-xs font-bold uppercase tracking-widest text-[var(--text-muted)]">
                                {{ ucfirst($groupName) }}
                            </h3>
                            <label class="flex items-center gap-1.5 cursor-pointer group">
                                @php
                                    $groupAll = $perms->every(fn($p) => in_array($p->name, old('permissions', $rolePermissions)));
                                @endphp
                                <input type="checkbox"
                                       class="group-checkbox h-3.5 w-3.5 rounded cursor-pointer appearance-none border border-[var(--border-strong)] bg-[var(--bg-raised)] checked:bg-[var(--accent)] checked:border-[var(--accent)] transition-colors"
                                       @if($groupAll) checked @endif
                                       @change="toggleGroup($event, '{{ $groupName }}')">
                                <span class="text-[0.68rem] font-medium text-[var(--text-muted)] group-hover:text-[var(--text-secondary)] transition-colors">All</span>
                            </label>
                        </div>

                        <div class="space-y-2.5">
                            @foreach($perms as $permission)
                            @php
                                $isChecked = is_array(old('permissions'))
                                    ? in_array($permission->name, old('permissions'))
                                    : in_array($permission->name, $rolePermissions);
                            @endphp
                            <label class="flex items-center gap-2.5 cursor-pointer group/item">
                                <div class="relative flex-shrink-0">
                                    <input type="checkbox"
                                           name="permissions[]"
                                           value="{{ $permission->name }}"
                                           data-group="{{ $groupName }}"
                                           class="perm-check h-4 w-4 rounded cursor-pointer appearance-none border border-[var(--border-strong)] bg-[var(--bg-raised)] checked:bg-[var(--accent)] checked:border-[var(--accent)] transition-colors peer"
                                           @if($isChecked) checked @endif>
                                    <svg class="absolute inset-0 m-auto w-2.5 h-2.5 text-white pointer-events-none opacity-0 peer-checked:opacity-100" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <span class="text-sm text-[var(--text-secondary)] group-hover/item:text-white transition-colors">
                                    {{ explode('.', $permission->name)[1] ?? $permission->name }}
                                </span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-[var(--border-subtle)]">
                <a href="{{ route('roles.index') }}" class="btn-secondary">
                    Cancel
                </a>
                <button type="submit" class="btn-primary">
                    <x-lucide-save class="w-4 h-4" />
                    O'zgarishlarni saqlash
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function permissionsManager() {
    return {
        allSelected() {
            const boxes = document.querySelectorAll('input.perm-check');
            return boxes.length > 0 && [...boxes].every(b => b.checked);
        },
        toggleAll() {
            const target = !this.allSelected();
            document.querySelectorAll('input.perm-check').forEach(b => b.checked = target);
            document.querySelectorAll('.group-checkbox').forEach(b => b.checked = target);
        },
        toggleGroup(event, group) {
            const checked = event.target.checked;
            document.querySelectorAll(`input.perm-check[data-group="${group}"]`).forEach(b => b.checked = checked);
        },
    };
}
</script>
@endpush
@endsection
