@extends('layouts.dashboard')

@php
    use App\Models\Plan;
@endphp

@section('title', 'Calendar')
@section('breadcrumb', 'Plan')
@section('header_title', $month->format('F Y'))

@push('styles')
<style>
    /* The grid owns its own borders so the outer card keeps clean corners: a
       per-cell border-right leaves a hairline sticking out of the last column. */
    .cal-grid {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        gap: 1px;
        background: var(--border-subtle);
        border-radius: var(--radius-md);
        overflow: hidden;
        border: 1px solid var(--border-subtle);
    }

    .cal-head {
        padding: .55rem .25rem;
        text-align: center;
        font-size: .625rem;
        font-weight: 800;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--text-muted);
        background: var(--bg-surface);
    }

    .cal-day {
        position: relative;
        display: flex;
        flex-direction: column;
        gap: .35rem;
        min-height: 4.25rem;
        padding: .45rem .4rem;
        background: var(--bg-raised);
        transition: background .15s;
    }

    .cal-day:hover {
        background: var(--bg-overlay);
    }

    .cal-day.is-outside {
        opacity: .38;
    }

    .cal-num {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 1.35rem;
        height: 1.35rem;
        border-radius: .45rem;
        font-family: 'JetBrains Mono', ui-monospace, monospace;
        font-size: .7rem;
        color: var(--text-muted);
    }

    .cal-day.is-today .cal-num {
        color: #fff;
        font-weight: 700;
        background: linear-gradient(135deg, var(--accent-hover), var(--accent-alt));
    }

    .cal-rate {
        font-family: 'JetBrains Mono', ui-monospace, monospace;
        font-size: .625rem;
        font-weight: 700;
    }

    .cal-bar {
        height: 3px;
        border-radius: 99px;
        background: rgba(148, 163, 184, .18);
        overflow: hidden;
        margin-top: auto;
    }

    .cal-bar span {
        display: block;
        height: 100%;
        border-radius: 99px;
    }

    .cal-meta {
        font-size: .625rem;
        color: var(--text-muted);
        line-height: 1.3;
    }

    @media (min-width: 640px) {
        .cal-day {
            min-height: 6.5rem;
            padding: .6rem;
            gap: .45rem;
        }

        .cal-head {
            padding: .7rem .5rem;
            font-size: .68rem;
        }

        .cal-meta {
            font-size: .7rem;
        }
    }
</style>
@endpush

@section('content')
<div class="space-y-5">

    {{-- Month navigation and the month's own totals --}}
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.calendar.index', ['month' => $month->subMonth()->toDateString()]) }}"
                class="btn-secondary !px-3 !py-2" aria-label="Previous month">
                <x-lucide-chevron-left class="w-4 h-4" />
            </a>
            <a href="{{ route('admin.calendar.index') }}" class="btn-secondary !py-2 text-sm">Today</a>
            <a href="{{ route('admin.calendar.index', ['month' => $month->addMonth()->toDateString()]) }}"
                class="btn-secondary !px-3 !py-2" aria-label="Next month">
                <x-lucide-chevron-right class="w-4 h-4" />
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3">
            @foreach([
                ['Plans', $summary['total'], null],
                ['Completed', $summary['completed'], '#22C55E'],
                ['Rate', $summary['raw_rate'] . '%', null],
                ['Planned', Plan::humanMinutes($summary['planned_minutes']), null],
            ] as [$label, $value, $color])
            <div class="card px-3 py-2">
                <p class="text-[10px] font-bold uppercase tracking-wider text-[var(--text-muted)]">{{ $label }}</p>
                <p class="text-base font-bold font-mono mt-0.5" style="color: {{ $color ?? 'var(--text-primary)' }};">{{ $value }}</p>
            </div>
            @endforeach
        </div>
    </div>

    <div class="cal-grid">
        @foreach(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $weekday)
        <div class="cal-head">
            <span class="hidden sm:inline">{{ $weekday }}</span>
            <span class="sm:hidden">{{ substr($weekday, 0, 1) }}</span>
        </div>
        @endforeach

        @foreach($days as $day)
        @php
            // The tint is the month's heat map: a glance should say which weeks
            // went well before a single number is read.
            $tint = $day['total'] === 0
                ? null
                : ($day['rate'] >= 80 ? '#22C55E' : ($day['rate'] >= 50 ? '#F59E0B' : '#EF4444'));
        @endphp
        <a href="{{ route('admin.plans.index', ['date' => $day['date']->toDateString()]) }}"
            class="cal-day {{ $day['in_month'] ? '' : 'is-outside' }} {{ $day['is_today'] ? 'is-today' : '' }}"
            title="{{ $day['date']->format('l, j F') }}{{ $day['total'] ? ' — ' . $day['completed'] . '/' . $day['total'] . ' done' : '' }}">

            <div class="flex items-center justify-between gap-1">
                <span class="cal-num">{{ $day['date']->day }}</span>
                @if($tint)
                <span class="cal-rate" style="color: {{ $tint }};">{{ (int) $day['rate'] }}%</span>
                @endif
            </div>

            @if($day['total'] > 0)
            <p class="cal-meta">
                <span class="text-[var(--text-secondary)] font-semibold">{{ $day['completed'] }}</span>/{{ $day['total'] }}
                <span class="hidden sm:inline"> · {{ Plan::humanMinutes($day['minutes']) }}</span>
            </p>
            <span class="cal-bar"><span style="width: {{ $day['rate'] }}%; background: {{ $tint }};"></span></span>
            @endif
        </a>
        @endforeach
    </div>

    <div class="flex flex-wrap items-center gap-x-5 gap-y-2 text-xs text-[var(--text-muted)]">
        <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm" style="background: #22C55E;"></span>80% and up</span>
        <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm" style="background: #F59E0B;"></span>50–79%</span>
        <span class="inline-flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-sm" style="background: #EF4444;"></span>below 50%</span>
        <span class="ml-auto">Tap a day to open its plans</span>
    </div>
</div>
@endsection
