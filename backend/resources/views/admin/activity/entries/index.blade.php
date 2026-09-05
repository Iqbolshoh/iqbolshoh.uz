@extends('layouts.dashboard')

@php
    use App\Models\ActivityEntry;
    $hours = fn (int $minutes): string => ActivityEntry::duration($minutes);
@endphp

@section('title', 'Time log')
@section('breadcrumb', 'Time')
@section('header_title', 'Time log')

@section('content')
<div x-data="{ deleteModalOpen: false, deleteUrl: '', deleteName: '' }" class="space-y-6">

    @include('admin.partials.delete-modal', ['what' => 'time entry'])

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <p class="text-sm text-[var(--text-muted)]">
            Every stretch of the day, newest first. The only place an entry the bot took from a status can be
            corrected — the bot itself will not touch those.
        </p>
        @can('activities-entries.create')
        <a href="{{ route('admin.activities-entries.create') }}" class="btn-primary">
            <x-lucide-plus class="w-4 h-4" />
            Add
        </a>
        @endcan
    </div>

    {{-- The total is of everything the filter matches, not of the page on screen. --}}
    <div class="card p-4">
        <p class="text-xs font-semibold uppercase tracking-wider text-[var(--text-muted)]">Total for this filter</p>
        <p class="mt-1.5 text-xl font-bold tracking-tight text-white">{{ $hours($total) }}</p>
    </div>

    <form method="GET" class="card p-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-5 items-end">
        <div>
            <label for="category" class="block text-xs font-semibold text-[var(--text-muted)] mb-1.5">Activity</label>
            <select id="category" name="category" class="input !py-2 text-sm cursor-pointer">
                <option value="">All</option>
                @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected((string) $filters['category'] === (string) $category->id)>
                    {{ $category->label() }}
                </option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="source" class="block text-xs font-semibold text-[var(--text-muted)] mb-1.5">Written by</label>
            <select id="source" name="source" class="input !py-2 text-sm cursor-pointer">
                <option value="">Anything</option>
                @foreach($sources as $source)
                <option value="{{ $source->value }}" @selected($filters['source'] === $source->value)>{{ $source->label() }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="from" class="block text-xs font-semibold text-[var(--text-muted)] mb-1.5">From</label>
            <input type="date" id="from" name="from" value="{{ $filters['from'] }}" class="input !py-2 text-sm">
        </div>

        <div>
            <label for="to" class="block text-xs font-semibold text-[var(--text-muted)] mb-1.5">To</label>
            <input type="date" id="to" name="to" value="{{ $filters['to'] }}" class="input !py-2 text-sm">
        </div>

        <div class="flex items-center gap-2">
            <button type="submit" class="btn-primary flex-1">
                <x-lucide-filter class="w-4 h-4" />
                Filter
            </button>
            <a href="{{ route('admin.activities-entries.index') }}" class="btn-secondary" title="Clear">
                <x-lucide-x class="w-4 h-4" />
            </a>
        </div>
    </form>

    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--border-subtle)] text-left">
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-[var(--text-muted)]">#</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-[var(--text-muted)]">Date</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-[var(--text-muted)]">Activity</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] text-right">Length</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-[var(--text-muted)]">Note</th>
                        <th class="px-4 py-3 text-xs font-bold uppercase tracking-wider text-[var(--text-muted)]"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entries as $index => $entry)
                    <tr class="border-b border-[var(--border-subtle)] last:border-0 hover:bg-white/[0.02] transition-colors">
                        {{-- A running row number, never the id: rolled-back test
                             transactions eat ids and turn "#30789" into noise. --}}
                        <td class="px-4 py-3 font-mono text-xs text-[var(--text-muted)]">
                            {{ $entries->firstItem() + $index }}
                        </td>
                        <td class="px-4 py-3 font-mono text-xs whitespace-nowrap text-[var(--text-secondary)]">
                            {{ $entry->date->format('d.m.Y') }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-white">{{ $entry->category?->label() ?? 'Uncategorised' }}</span>
                            @if($entry->source === \App\Enums\ActivitySource::Status)
                            <span class="ml-1.5 text-[10px] uppercase tracking-wider font-semibold text-[var(--text-muted)]">
                                from status
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-mono font-semibold whitespace-nowrap text-white">
                            {{ $entry->formattedDuration() }}
                        </td>
                        <td class="px-4 py-3 text-[var(--text-muted)] max-w-xs truncate">{{ $entry->note }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1">
                                @can('activities-entries.edit')
                                <a href="{{ route('admin.activities-entries.edit', $entry) }}" title="Edit"
                                    class="p-2 rounded-lg text-[var(--text-muted)] hover:text-[var(--accent-hover)] hover:bg-[var(--accent-soft)] transition-colors">
                                    <x-lucide-pencil class="w-4 h-4" />
                                </a>
                                @endcan
                                @can('activities-entries.delete')
                                <button type="button" title="Delete"
                                    @click="deleteUrl = @js(route('admin.activities-entries.destroy', $entry)); deleteName = @js($entry->formattedDuration() . ' · ' . ($entry->category?->name ?? 'Uncategorised')); deleteModalOpen = true"
                                    class="p-2 rounded-lg text-[var(--text-muted)] hover:text-[var(--accent)] hover:bg-[var(--accent-soft)] transition-colors">
                                    <x-lucide-trash-2 class="w-4 h-4" />
                                </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-sm text-[var(--text-muted)]">
                            Nothing matches this filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $entries->links() }}
</div>
@endsection
