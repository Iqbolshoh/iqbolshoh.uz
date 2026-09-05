@extends('layouts.dashboard')

@php
    use App\Models\ActivityEntry;
    $hours = fn (int $minutes): string => ActivityEntry::duration($minutes);
@endphp

@section('title', 'Activities')
@section('breadcrumb', 'Time')
@section('header_title', 'Activities')

@section('content')
<div x-data="{ deleteModalOpen: false, deleteUrl: '', deleteName: '' }" class="space-y-8">

    @include('admin.partials.delete-modal', ['what' => 'activity'])

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <p class="text-sm text-[var(--text-muted)]">
            The ways a day gets spent, their daily targets for {{ $month->format('F Y') }}, and the words the bot
            recognises them by.
        </p>
        <div class="flex items-center gap-3">
            @can('activities-categories.create')
            <form action="{{ route('admin.activities-categories.restore') }}" method="POST">
                @csrf
                <button type="submit" class="btn-secondary" title="Add any starter activity this account is missing">
                    <x-lucide-rotate-ccw class="w-4 h-4" />
                    Restore defaults
                </button>
            </form>
            <a href="{{ route('admin.activities-categories.create') }}" class="btn-primary">
                <x-lucide-plus class="w-4 h-4" />
                New activity
            </a>
            @endcan
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @forelse($categories as $category)
        @php
            $spentMinutes = $spent[$category->id]['minutes'] ?? 0;
            $target = $category->daily_target_minutes ? $category->daily_target_minutes * $month->daysInMonth : null;
            $share = $target > 0 ? min(100, round($spentMinutes / $target * 100, 1)) : null;
        @endphp
        <div class="card p-5 flex flex-col gap-4 {{ $category->is_active ? '' : 'opacity-60' }}">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background: {{ $category->color ?? '#8B95A5' }};"></span>
                        <h4 class="font-bold text-white truncate">{{ $category->label() }}</h4>
                    </div>
                    <p class="text-xs text-[var(--text-muted)] mt-1">
                        {{ $category->entries_count }} entries
                        · <span class="font-semibold" style="color: {{ $category->is_good ? '#22C55E' : '#F59E0B' }};">
                            {{ $category->is_good ? 'useful' : 'wasted' }}
                        </span>
                        @unless($category->is_active)
                        · <span class="font-semibold" style="color: #F59E0B;">inactive</span>
                        @endunless
                    </p>
                </div>

                <div class="flex items-center gap-1 flex-shrink-0">
                    @can('activities-categories.edit')
                    <a href="{{ route('admin.activities-categories.edit', $category) }}" title="Edit"
                        class="p-2 rounded-lg text-[var(--text-muted)] hover:text-[var(--accent-hover)] hover:bg-[var(--accent-soft)] transition-colors">
                        <x-lucide-pencil class="w-4 h-4" />
                    </a>
                    @endcan
                    @can('activities-categories.delete')
                    <button type="button" title="Delete"
                        @click="deleteUrl = @js(route('admin.activities-categories.destroy', $category)); deleteName = @js($category->name); deleteModalOpen = true"
                        class="p-2 rounded-lg text-[var(--text-muted)] hover:text-[var(--accent)] hover:bg-[var(--accent-soft)] transition-colors">
                        <x-lucide-trash-2 class="w-4 h-4" />
                    </button>
                    @endcan
                </div>
            </div>

            @if($target)
            <div>
                <div class="flex items-center justify-between text-xs mb-1.5">
                    <span class="text-[var(--text-muted)]">{{ $hours($spentMinutes) }} of {{ $hours($target) }}</span>
                    <span class="font-mono font-bold" style="color: {{ $share >= 100 ? '#22C55E' : ($share >= 50 ? '#F59E0B' : 'var(--text-muted)') }};">
                        {{ $share }}%
                    </span>
                </div>
                <div class="h-2 rounded-full overflow-hidden" style="background: rgba(255,255,255,0.07);">
                    <div class="h-full rounded-full transition-all"
                        style="width: {{ $share }}%; background: {{ $category->color ?? 'var(--accent-hover)' }};"></div>
                </div>
                <p class="text-[10px] text-[var(--text-muted)] mt-1.5">
                    {{ $hours($category->daily_target_minutes) }} a day × {{ $month->daysInMonth }} days
                </p>
            </div>
            @else
            <p class="text-xs text-[var(--text-muted)]">
                No target · {{ $spentMinutes > 0 ? $hours($spentMinutes) . ' this month' : 'nothing this month' }}
            </p>
            @endif

            @if($category->keywords)
            <div class="pt-3 border-t border-[var(--border-subtle)]">
                <p class="text-[10px] uppercase tracking-wider font-semibold text-[var(--text-muted)] mb-1.5">Bot recognises</p>
                <p class="text-xs text-[var(--text-secondary)] leading-relaxed break-words">
                    {{ \Illuminate\Support\Str::limit($category->keywords, 120) }}
                </p>
            </div>
            @endif
        </div>
        @empty
        <div class="card px-6 py-12 text-center sm:col-span-2 xl:col-span-3">
            <p class="text-sm text-[var(--text-muted)]">No activities yet.</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
