@extends('layouts.dashboard')

@php
    use App\Models\ActivityEntry;
    $hours = fn (int $minutes): string => ActivityEntry::duration($minutes);
@endphp

@section('title', 'Time overview')
@section('breadcrumb', 'Time')
@section('header_title', 'Time overview')

@section('header_actions')
<form method="GET" class="flex items-center gap-2">
    <select name="month" onchange="this.form.submit()" class="input !py-2 text-sm cursor-pointer">
        @foreach($months as $value => $label)
        <option value="{{ $value }}" @selected($month->toDateString() === $value)>{{ $label }}</option>
        @endforeach
    </select>
</form>
@endsection

@section('content')
<div class="space-y-6">

    {{-- Coverage first: it is the number that decides whether the rest of the
         page can be trusted. Twelve hours of work against fourteen logged is a
         different fact from twelve against a full month. --}}
    <div class="grid gap-4 grid-cols-2 lg:grid-cols-4">
        @foreach([
            ['Today', $hours($todaySummary['minutes']), 'sun', null],
            ['This week', $hours($weekSummary['minutes']), 'calendar-days', null],
            [$month->format('F'), $hours($summary['minutes']), 'calendar-range', null],
            ['Accounted for', $summary['covered'] . '%', 'gauge', $summary['covered'] >= 70 ? '#22C55E' : '#F59E0B'],
        ] as [$label, $value, $icon, $color])
        <div class="card p-5">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-bold uppercase tracking-wider text-[var(--text-muted)]">{{ $label }}</p>
                <x-dynamic-component :component="'lucide-' . $icon" class="w-4 h-4 text-[var(--text-muted)]" />
            </div>
            <p class="text-2xl font-bold tracking-tight mt-2" style="color: {{ $color ?? 'var(--text-primary)' }};">{{ $value }}</p>
        </div>
        @endforeach
    </div>

    @if($summary['count'] === 0)
    <div class="card px-6 py-16 text-center">
        <x-lucide-hourglass class="w-8 h-8 mx-auto text-[var(--text-muted)]" />
        <p class="text-sm text-[var(--text-muted)] mt-3">Nothing logged in {{ $month->format('F Y') }}.</p>
        <p class="text-xs text-[var(--text-muted)] mt-1">
            Write <code class="font-mono">8 soat uxladim</code> to the bot, or add an entry by hand.
        </p>
    </div>
    @else

    <div class="grid gap-6 lg:grid-cols-3">

        {{-- Where it went --}}
        <div class="card p-6 lg:col-span-2">
            <h3 class="font-bold text-white mb-1">Where the time went</h3>
            <p class="text-xs text-[var(--text-muted)] mb-5">
                Shares are of the {{ $hours($summary['minutes']) }} logged, not of the month —
                {{ $summary['covered'] }}% of the month carries an entry at all.
            </p>

            <div class="space-y-3">
                @foreach($byCategory as $row)
                <div>
                    <div class="flex items-center justify-between text-sm mb-1.5">
                        <span class="text-white truncate">{{ $row['category']?->label() ?? 'Uncategorised' }}</span>
                        <span class="font-mono text-xs text-[var(--text-muted)] flex-shrink-0 ml-3">
                            {{ $hours($row['minutes']) }} · {{ $row['share'] }}%
                        </span>
                    </div>
                    <div class="h-2 rounded-full overflow-hidden" style="background: rgba(255,255,255,0.07);">
                        <div class="h-full rounded-full"
                            style="width: {{ max(1, $row['share']) }}%; background: {{ $row['category']?->color ?? '#8B95A5' }};"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="space-y-6">
            {{-- Useful against wasted: the one judgement the page makes, and
                 only because the activities carry it themselves. --}}
            <div class="card p-6">
                <h3 class="font-bold text-white mb-4">Well spent?</h3>
                @php $totalLogged = max(1, $good + $bad); @endphp
                <div class="flex h-3 rounded-full overflow-hidden mb-4">
                    <div style="width: {{ round($good / $totalLogged * 100) }}%; background: #22C55E;"></div>
                    <div style="width: {{ round($bad / $totalLogged * 100) }}%; background: #F59E0B;"></div>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2 text-[var(--text-secondary)]">
                            <span class="w-2.5 h-2.5 rounded-full" style="background: #22C55E;"></span> Useful
                        </span>
                        <span class="font-mono font-semibold text-white">{{ $hours($good) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="flex items-center gap-2 text-[var(--text-secondary)]">
                            <span class="w-2.5 h-2.5 rounded-full" style="background: #F59E0B;"></span> Wasted
                        </span>
                        <span class="font-mono font-semibold text-white">{{ $hours($bad) }}</span>
                    </div>
                </div>
            </div>

            <div class="card p-6">
                <h3 class="font-bold text-white mb-1">Per day</h3>
                <p class="text-xs text-[var(--text-muted)] mb-3">Averaged over {{ $summary['days'] }} days</p>
                <p class="text-2xl font-bold tracking-tight text-white">{{ $hours($summary['average']) }}</p>
            </div>
        </div>
    </div>

    @php $targets = $targets->filter(fn (array $row): bool => $row['target'] > 0); @endphp
    @if($targets->isNotEmpty())
    <div class="card p-6">
        <h3 class="font-bold text-white mb-1">Against the targets</h3>
        <p class="text-xs text-[var(--text-muted)] mb-5">
            Each target is per day, multiplied out for the {{ $summary['days'] }} days of this period.
        </p>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @foreach($targets as $row)
            <div>
                <div class="flex items-center justify-between text-sm mb-1.5">
                    <span class="text-white truncate">{{ $row['category']->label() }}</span>
                    <span class="font-mono text-xs flex-shrink-0 ml-3"
                        style="color: {{ $row['share'] >= 100 ? '#22C55E' : ($row['share'] >= 50 ? '#F59E0B' : 'var(--text-muted)') }};">
                        {{ $row['share'] }}%
                    </span>
                </div>
                <div class="h-2 rounded-full overflow-hidden" style="background: rgba(255,255,255,0.07);">
                    <div class="h-full rounded-full"
                        style="width: {{ min(100, $row['share']) }}%; background: {{ $row['category']->color ?? 'var(--accent-hover)' }};"></div>
                </div>
                <p class="text-[10px] text-[var(--text-muted)] mt-1.5">
                    {{ $hours($row['minutes']) }} of {{ $hours($row['target']) }}
                </p>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <div class="card p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-bold text-white">Latest entries</h3>
            <a href="{{ route('admin.activities-entries.index') }}" class="text-xs font-semibold text-[var(--accent-hover)] hover:underline">
                Open the log
            </a>
        </div>

        <div class="space-y-3">
            @foreach($recent as $entry)
            <div class="flex items-center justify-between gap-4">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-white truncate">
                        {{ $entry->category?->label() ?? 'Uncategorised' }}
                    </p>
                    <p class="text-xs text-[var(--text-muted)]">
                        {{ $entry->date->format('d.m.Y') }}
                        @if($entry->note) · {{ \Illuminate\Support\Str::limit($entry->note, 40) }} @endif
                        @if($entry->source === \App\Enums\ActivitySource::Status) · from status @endif
                    </p>
                </div>
                <span class="font-mono text-sm font-semibold text-white flex-shrink-0">{{ $entry->formattedDuration() }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
