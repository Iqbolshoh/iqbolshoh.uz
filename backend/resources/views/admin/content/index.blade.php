{{--
    Listing shared by every content section. The section controller supplies
    $columns, so this file never needs to know what it is showing.
--}}
@extends('layouts.dashboard')

@section('title', $labels['plural'])
@section('breadcrumb', 'Sayt kontenti')
@section('header_title', $labels['plural'])

@section('content')
@php
    use App\Support\SiteContent;
    use App\Support\SiteIcons;

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

    $rowName = fn($item) => \Illuminate\Support\Str::limit((string) $plain($item->{$titleColumn['value']}), 60);
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
                    <h3 class="text-xl font-bold text-white tracking-tight">{{ $labels['singular'] }}ni o'chirish</h3>
                    <p class="mt-2 text-sm text-[var(--text-secondary)] leading-relaxed">
                        Siz haqiqatan ham o'chirmoqchimisiz
                        <span class="font-semibold text-white" x-text='"\"" + deleteName + "\""'></span>?
                        Bu amalni qaytarib bo'lmaydi.
                    </p>
                </div>

                <div class="mt-7 flex flex-col sm:flex-row gap-3">
                    <button type="button" @click="deleteModalOpen = false" class="btn-secondary flex-1">Bekor qilish</button>
                    <form :action="deleteUrl" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-[0.625rem] rounded-[var(--radius-md)] text-sm font-semibold text-white bg-[var(--accent)] hover:bg-[var(--accent-hover)] transition-colors shadow-lg shadow-[var(--accent-glow)] cursor-pointer">
                            <x-lucide-trash-2 class="w-4 h-4" />
                            O'chirish
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
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Qidirish..."
                        class="pl-11 pr-4 py-2 text-sm rounded-[var(--radius-md)] bg-[var(--bg-surface)] border border-[var(--border-subtle)] text-white placeholder-[var(--text-muted)] focus:outline-none focus:border-[var(--accent)] w-56 transition-colors">
                </div>
                @if(request('search'))
                <a href="{{ request()->url() }}" class="p-2 rounded-lg text-[var(--text-muted)] hover:text-white hover:bg-white/5 transition-colors" title="Tozalash">
                    <x-lucide-x class="w-4 h-4" />
                </a>
                @endif
            </form>

            @can($key . '.create')
            <a href="{{ route('admin.' . $key . '.create') }}" class="btn-primary">
                <x-lucide-plus class="w-4 h-4" />
                Yangi {{ mb_strtolower($labels['singular']) }}
            </a>
            @endcan
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto scroll-area">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-[var(--border-subtle)]" style="background: rgba(255,255,255,0.01);">
                        <th class="px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] w-16">#</th>
                        @foreach($columns as $column)
                        <th class="px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-[var(--text-muted)]">{{ $column['label'] }}</th>
                        @endforeach
                        <th class="px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] text-right">Amallar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--border-subtle)] text-sm">
                    @forelse($items as $item)
                    <tr class="transition-colors hover:bg-white/[0.02]">
                        <td class="px-6 py-4 text-[var(--text-muted)] font-mono text-xs">{{ $item->sort_order }}</td>

                        @foreach($columns as $column)
                        @php $value = $item->{$column['value']}; @endphp
                        <td class="px-6 py-4 align-middle">
                            @switch($column['type'])
                                @case('image')
                                    @if($value)
                                    <img src="{{ $value }}" alt="" loading="lazy"
                                        class="w-16 h-11 object-cover rounded-lg border border-[var(--border-subtle)] bg-[var(--bg-surface)]">
                                    @else
                                    <div class="w-16 h-11 rounded-lg border border-[var(--border-subtle)] flex items-center justify-center" style="background: rgba(255,255,255,0.03);">
                                        <x-lucide-image class="w-4 h-4 text-[var(--text-muted)]" />
                                    </div>
                                    @endif
                                    @break

                                @case('icon')
                                    <div class="w-9 h-9 rounded-xl flex items-center justify-center bg-[var(--accent-soft)] border border-[var(--accent-border)]">
                                        <x-dynamic-component :component="\App\Support\SiteIcons::component($value)" class="w-4 h-4 text-[var(--accent-hover)]" />
                                    </div>
                                    @break

                                @case('trans')
                                    <p class="text-white font-medium max-w-md truncate">{{ $plain($value) }}</p>
                                    @break

                                @case('badge')
                                    <span class="badge badge-accent">{{ $value }}</span>
                                    @break

                                @case('list')
                                    <div class="flex flex-wrap gap-1 max-w-xs">
                                        @foreach(array_slice((array) $value, 0, 3) as $entry)
                                        <span class="badge" style="background: rgba(255,255,255,0.05); color: var(--text-secondary);">{{ $entry }}</span>
                                        @endforeach
                                        @if(count((array) $value) > 3)
                                        <span class="text-xs text-[var(--text-muted)]">+{{ count((array) $value) - 3 }}</span>
                                        @endif
                                    </div>
                                    @break

                                @case('bool')
                                    @if($value)
                                    <span class="badge badge-success"><x-lucide-check class="w-3 h-3" /> Ha</span>
                                    @else
                                    <span class="text-xs text-[var(--text-muted)]">—</span>
                                    @endif
                                    @break

                                @case('meter')
                                    <div class="flex items-center gap-3 min-w-[9rem]">
                                        <div class="flex-1 h-1.5 rounded-full overflow-hidden" style="background: rgba(255,255,255,0.07);">
                                            <div class="h-full rounded-full" style="width: {{ (int) $value }}%; background: linear-gradient(90deg, var(--accent-hover), var(--accent-alt));"></div>
                                        </div>
                                        <span class="font-mono text-xs text-[var(--text-secondary)]">{{ $value }}%</span>
                                    </div>
                                    @break

                                @case('strong')
                                    <span class="font-mono font-bold text-white">{{ $value }}</span>
                                    @break

                                @default
                                    <span class="text-[var(--text-secondary)]">{{ $value ?: '—' }}</span>
                            @endswitch
                        </td>
                        @endforeach

                        <td class="px-6 py-4 text-right">
                            <div class="inline-flex items-center gap-1">
                                @can($key . '.edit')
                                <a href="{{ route('admin.' . $key . '.edit', $item->id) }}"
                                    class="p-2 rounded-lg text-[var(--text-muted)] hover:text-[var(--accent-hover)] hover:bg-[var(--accent-soft)] transition-colors" title="Tahrirlash">
                                    <x-lucide-pencil class="w-4 h-4" />
                                </a>
                                @endcan
                                @can($key . '.delete')
                                <button type="button"
                                    @click="deleteUrl = @js(route('admin.' . $key . '.destroy', $item->id)); deleteName = @js($rowName($item)); deleteModalOpen = true"
                                    class="p-2 rounded-lg text-[var(--text-muted)] hover:text-[var(--accent)] hover:bg-[var(--accent-soft)] transition-colors" title="O'chirish">
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
                                <p class="text-sm font-semibold text-[var(--text-secondary)]">{{ $labels['plural'] }} topilmadi</p>
                                @can($key . '.create')
                                <a href="{{ route('admin.' . $key . '.create') }}" class="btn-primary !text-xs !py-2 !px-4 mt-1">
                                    <x-lucide-plus class="w-3.5 h-3.5" />
                                    Yangi {{ mb_strtolower($labels['singular']) }}
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
