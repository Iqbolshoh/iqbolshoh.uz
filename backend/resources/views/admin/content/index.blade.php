{{--
    Listing shared by every content section. The section controller supplies
    $columns, so this file never needs to know what it is showing.
--}}
@extends('layouts.dashboard')

@section('title', $labels['plural'])
@section('breadcrumb', 'Site content')
@section('header_title', $labels['plural'])

@section('content')
@php
    use App\Support\SiteContent;
    use App\Support\SiteIcons;
    use Illuminate\Support\Str;

    $primary = array_key_first(SiteContent::LOCALES);

    // Translatable values are stored as {"uz":…,"en":…}; the table shows the
    // primary language and leaves the rest to the edit form.
    $plain = function ($value) use ($primary) {
        if (is_array($value)) {
            return $value[$primary] ?? reset($value) ?: '';
        }
        return $value;
    };

    // Name shown in the delete confirmation: the first textual column, since
    // that is what the editor recognises the row by.
    $titleColumn = collect($columns)->firstWhere(fn($column) => in_array($column['type'], ['trans', 'text', 'strong']))
        ?? $columns[0];

    $rowName = fn($item) => Str::limit((string) $plain($item->{$titleColumn['value']}), 60);
@endphp

<div x-data="{ deleteModalOpen: false, deleteUrl: '', deleteName: '' }">

    {{-- Delete confirmation --}}
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
                    <h3 class="text-xl font-bold text-white tracking-tight">Delete {{ mb_strtolower($labels['singular']) }}</h3>
                    <p class="mt-2 text-sm text-[var(--text-secondary)] leading-relaxed">
                        Are you sure you want to delete
                        <span class="font-semibold text-white" x-text='"\"" + deleteName + "\""'></span>?
                        This cannot be undone.
                    </p>
                </div>

                <div class="mt-7 flex flex-col sm:flex-row gap-3">
                    <button type="button" @click="deleteModalOpen = false" class="btn-secondary flex-1">Cancel</button>
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

    {{-- Toolbar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div class="min-w-0">
            @if(!empty($labels['hint']))
            <p class="text-sm text-[var(--text-muted)]">{{ $labels['hint'] }}</p>
            @endif
        </div>

        <div class="flex items-center gap-3 flex-wrap">
            <form method="GET" action="{{ request()->url() }}" class="flex items-center gap-2">
                <div class="relative">
                    <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-[var(--text-muted)] pointer-events-none" />
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Search…"
                        class="pl-11 pr-4 py-2 text-sm rounded-[var(--radius-md)] bg-[var(--bg-surface)] border border-[var(--border-subtle)] text-white placeholder-[var(--text-muted)] focus:outline-none focus:border-[var(--accent)] w-56 transition-colors">
                </div>
                @if(request('search'))
                <a href="{{ request()->url() }}" class="p-2 rounded-lg text-[var(--text-muted)] hover:text-white hover:bg-white/5 transition-colors" title="Clear">
                    <x-lucide-x class="w-4 h-4" />
                </a>
                @endif
            </form>

            @can($key . '.create')
            <a href="{{ route('admin.' . $key . '.create') }}" class="btn-primary">
                <x-lucide-plus class="w-4 h-4" />
                New {{ mb_strtolower($labels['singular']) }}
            </a>
            @endcan
        </div>
    </div>

    {{-- Stacked cards below md: a nine-column table cannot be read on a phone,
         and horizontal scrolling hides the row actions off-screen. --}}
    <div class="md:hidden space-y-3">
        @forelse($items as $item)
        <div class="card p-4">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0 flex-1 space-y-2.5">
                    @foreach($columns as $column)
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-[var(--text-muted)] w-20 flex-shrink-0">{{ $column['label'] }}</span>
                        <div class="min-w-0">
                            @include('admin.content.cell', ['column' => $column, 'value' => $item->{$column['value']}])
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="flex flex-col gap-1 flex-shrink-0">
                    @can($key . '.edit')
                    <a href="{{ route('admin.' . $key . '.edit', $item->id) }}"
                        class="p-2 rounded-lg text-[var(--text-muted)] hover:text-[var(--accent-hover)] hover:bg-[var(--accent-soft)] transition-colors" title="Edit">
                        <x-lucide-pencil class="w-4 h-4" />
                    </a>
                    @endcan
                    @can($key . '.delete')
                    <button type="button"
                        @click="deleteUrl = @js(route('admin.' . $key . '.destroy', $item->id)); deleteName = @js($rowName($item)); deleteModalOpen = true"
                        class="p-2 rounded-lg text-[var(--text-muted)] hover:text-[var(--accent)] hover:bg-[var(--accent-soft)] transition-colors" title="Delete">
                        <x-lucide-trash-2 class="w-4 h-4" />
                    </button>
                    @endcan
                </div>
            </div>
        </div>
        @empty
        <div class="card px-6 py-12 text-center">
            <div class="flex flex-col items-center gap-3">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background: rgba(255,255,255,0.04); border: 1px solid var(--border-subtle);">
                    <x-dynamic-component :component="'lucide-' . $labels['icon']" class="w-6 h-6 text-[var(--text-muted)]" />
                </div>
                <p class="text-sm font-semibold text-[var(--text-secondary)]">No {{ mb_strtolower($labels['plural']) }} yet</p>
                @can($key . '.create')
                <a href="{{ route('admin.' . $key . '.create') }}" class="btn-primary !text-xs !py-2 !px-4 mt-1">
                    <x-lucide-plus class="w-3.5 h-3.5" />
                    New {{ mb_strtolower($labels['singular']) }}
                </a>
                @endcan
            </div>
        </div>
        @endforelse
    </div>

    <div class="card overflow-hidden hidden md:block">
        <div class="overflow-x-auto scroll-area">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-[var(--border-subtle)]" style="background: rgba(255,255,255,0.01);">
                        <th class="px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] w-16">#</th>
                        @foreach($columns as $column)
                        <th class="px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-[var(--text-muted)]">{{ $column['label'] }}</th>
                        @endforeach
                        <th class="px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border-subtle)] text-sm">
                    @forelse($items as $item)
                    <tr class="transition-colors hover:bg-white/[0.02]">
                        <td class="px-6 py-4 text-[var(--text-muted)] font-mono text-xs">{{ $item->sort_order }}</td>

                        @foreach($columns as $column)
                        <td class="px-6 py-4 align-middle">
                            @include('admin.content.cell', ['column' => $column, 'value' => $item->{$column['value']}])
                        </td>
                        @endforeach

                        <td class="px-6 py-4 text-right">
                            <div class="inline-flex items-center gap-1">
                                @can($key . '.edit')
                                <a href="{{ route('admin.' . $key . '.edit', $item->id) }}"
                                    class="p-2 rounded-lg text-[var(--text-muted)] hover:text-[var(--accent-hover)] hover:bg-[var(--accent-soft)] transition-colors" title="Edit">
                                    <x-lucide-pencil class="w-4 h-4" />
                                </a>
                                @endcan
                                @can($key . '.delete')
                                <button type="button"
                                    @click="deleteUrl = @js(route('admin.' . $key . '.destroy', $item->id)); deleteName = @js($rowName($item)); deleteModalOpen = true"
                                    class="p-2 rounded-lg text-[var(--text-muted)] hover:text-[var(--accent)] hover:bg-[var(--accent-soft)] transition-colors" title="Delete">
                                    <x-lucide-trash-2 class="w-4 h-4" />
                                </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ count($columns) + 2 }}" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background: rgba(255,255,255,0.04); border: 1px solid var(--border-subtle);">
                                    <x-dynamic-component :component="'lucide-' . $labels['icon']" class="w-6 h-6 text-[var(--text-muted)]" />
                                </div>
                                <p class="text-sm font-semibold text-[var(--text-secondary)]">No {{ mb_strtolower($labels['plural']) }} yet</p>
                                @can($key . '.create')
                                <a href="{{ route('admin.' . $key . '.create') }}" class="btn-primary !text-xs !py-2 !px-4 mt-1">
                                    <x-lucide-plus class="w-3.5 h-3.5" />
                                    New {{ mb_strtolower($labels['singular']) }}
                                </a>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
        <div class="px-6 py-4 border-t border-[var(--border-subtle)]">
            {{ $items->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
