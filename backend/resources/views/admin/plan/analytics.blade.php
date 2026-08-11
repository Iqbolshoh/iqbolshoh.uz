@extends('layouts.dashboard')

@php
    use App\Models\Plan;
    use Illuminate\Support\Js;

    $tabs = [
        'daily' => ['Daily', route('admin.analytics.daily')],
        'weekly' => ['Weekly', route('admin.analytics.weekly')],
        'monthly' => ['Monthly', route('admin.analytics.monthly')],
    ];
@endphp

@section('title', 'Analytics')
@section('breadcrumb', 'Plan')
@section('header_title', 'Analytics')

@section('content')
<div class="space-y-6">

    {{-- Period switch --}}
    <div class="flex items-center gap-1.5 p-1 rounded-[var(--radius-md)] bg-[var(--bg-surface)] border border-[var(--border-subtle)] w-fit">
        @foreach($tabs as $key => [$label, $url])
        <a href="{{ $url }}"
            class="px-4 py-1.5 rounded-lg text-sm font-semibold transition-colors {{ $period === $key ? 'text-white' : 'text-[var(--text-muted)] hover:text-[var(--text-secondary)]' }}"
            @if($period === $key) style="background: linear-gradient(135deg, var(--accent-hover), var(--accent-alt));" @endif>
            {{ $label }}
        </a>
        @endforeach
    </div>

    <p class="text-sm text-[var(--text-muted)]">
        {{ $from->format('j M Y') }}@if(! $from->isSameDay($to)) — {{ $to->format('j M Y') }}@endif
    </p>

    {{-- Headline numbers --}}
    <div class="grid gap-4 grid-cols-2 lg:grid-cols-4">
        @foreach([
            ['Plans', $summary['total'], 'list-checks', null],
            ['Completed', $summary['completed'], 'check-check', '#22C55E'],
            ['Raw rate', $summary['raw_rate'] . '%', 'percent', null],
            ['True rate', $summary['true_rate'] . '%', 'target', '#0EA5E9'],
        ] as [$label, $value, $icon, $color])
        <div class="card p-5">
            <div class="flex items-center justify-between">
                <p class="text-[11px] font-bold uppercase tracking-wider text-[var(--text-muted)]">{{ $label }}</p>
                <x-dynamic-component :component="'lucide-' . $icon" class="w-4 h-4 text-[var(--text-muted)]" />
            </div>
            <p class="text-2xl font-bold mt-2 font-mono" style="color: {{ $color ?? 'white' }};">{{ $value }}</p>
        </div>
        @endforeach
    </div>

    {{-- Status split --}}
    <div class="card p-6">
        <h3 class="text-sm font-bold uppercase tracking-wider text-[var(--text-muted)] mb-4">Where plans ended up</h3>
        <div class="grid gap-3 grid-cols-2 sm:grid-cols-3 lg:grid-cols-5">
            @foreach([
                ['Completed', $summary['completed'], '#22C55E'],
                ['Failed', $summary['failed'], '#EF4444'],
                ['Postponed', $summary['postponed'], '#F59E0B'],
                ['Interrupted', $summary['interrupted'], '#6366F1'],
                ['No response', $summary['no_response'], '#A855F7'],
            ] as [$label, $count, $color])
            <div class="rounded-[var(--radius-md)] p-3 border" style="border-color: {{ $color }}33; background: {{ $color }}12;">
                <p class="text-xl font-bold font-mono" style="color: {{ $color }};">{{ $count }}</p>
                <p class="text-[11px] text-[var(--text-muted)] mt-0.5">{{ $label }}</p>
            </div>
            @endforeach
        </div>

        @if($summary['interrupted'] > 0)
        <p class="text-xs text-[var(--text-muted)] mt-4">
            {{ $summary['interrupted'] }} plans were lost to interruptions rather than to you, which is the gap
            between the raw rate ({{ $summary['raw_rate'] }}%) and the true rate ({{ $summary['true_rate'] }}%).
        </p>
        @endif
    </div>

    {{-- Time spent --}}
    <div class="grid gap-4 lg:grid-cols-2">
        <div class="card p-6">
            <h3 class="text-sm font-bold uppercase tracking-wider text-[var(--text-muted)] mb-4">Completion trend</h3>
            <div class="h-56"><canvas id="trendChart"></canvas></div>
        </div>

        <div class="card p-6">
            <h3 class="text-sm font-bold uppercase tracking-wider text-[var(--text-muted)] mb-4">Planned against actual (hours)</h3>
            <div class="h-56"><canvas id="hoursChart"></canvas></div>
        </div>
    </div>

    {{-- Segments --}}
    <div class="grid gap-4 lg:grid-cols-2">
        <div class="card p-6">
            <h3 class="text-sm font-bold uppercase tracking-wider text-[var(--text-muted)] mb-4">Time of day</h3>
            @include('admin.plan.partials.rate-bars', ['rows' => $hourBands])
        </div>

        <div class="card p-6">
            <h3 class="text-sm font-bold uppercase tracking-wider text-[var(--text-muted)] mb-4">Weekday</h3>
            @include('admin.plan.partials.rate-bars', ['rows' => $weekdays])

            @if($extremes['best'])
            <p class="text-xs text-[var(--text-muted)] mt-4">
                🔥 Best: <span class="text-white font-semibold">{{ $extremes['best']['label'] }}</span> ·
                ⚠️ Weakest: <span class="text-white font-semibold">{{ $extremes['weakest']['label'] }}</span>
            </p>
            @endif
        </div>

        <div class="card p-6">
            <h3 class="text-sm font-bold uppercase tracking-wider text-[var(--text-muted)] mb-4">By goal</h3>
            @if($goals === [])
            <p class="text-sm text-[var(--text-muted)]">No goal-linked plans in this period.</p>
            @else
            <div class="space-y-3">
                @foreach($goals as $goal)
                <div>
                    <div class="flex items-center justify-between text-xs mb-1.5">
                        <span class="inline-flex items-center gap-2 text-[var(--text-secondary)]">
                            <span class="w-2 h-2 rounded-full" style="background: {{ $goal['color'] ?? '#8B95A5' }};"></span>
                            {{ $goal['title'] }}
                        </span>
                        <span class="font-mono font-bold text-white">{{ $goal['rate'] }}%</span>
                    </div>
                    <div class="h-1.5 rounded-full overflow-hidden" style="background: rgba(255,255,255,0.07);">
                        <div class="h-full rounded-full" style="width: {{ $goal['rate'] }}%; background: {{ $goal['color'] ?? 'var(--accent-hover)' }};"></div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

        <div class="card p-6">
            <h3 class="text-sm font-bold uppercase tracking-wider text-[var(--text-muted)] mb-4">Postponement behaviour</h3>
            @include('admin.plan.partials.rate-bars', ['rows' => $postponement])
            <p class="text-xs text-[var(--text-muted)] mt-4">
                How often a plan still gets done after being pushed. A steep drop is the signal to reschedule
                rather than keep nudging.
            </p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    (function () {
        const trend = {!! Js::from($trend) !!};
        const hours = {!! Js::from($plannedVsActual) !!};

        // Read the panel's own tokens so the charts follow the light/dark switch
        // instead of carrying a second, hardcoded palette.
        const styles = getComputedStyle(document.documentElement);
        const ink = styles.getPropertyValue('--text-muted').trim() || '#8B95A5';
        const grid = 'rgba(148, 163, 184, 0.12)';

        Chart.defaults.color = ink;
        Chart.defaults.font.family = "'Plus Jakarta Sans', system-ui, sans-serif";

        new Chart(document.getElementById('trendChart'), {
            type: 'line',
            data: {
                labels: trend.map((point) => point.label),
                datasets: [{
                    label: 'Completion %',
                    data: trend.map((point) => point.rate),
                    borderColor: '#6366F1',
                    backgroundColor: 'rgba(99, 102, 241, 0.14)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.35,
                    pointRadius: 0,
                    pointHoverRadius: 4,
                }],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { min: 0, max: 100, grid: { color: grid }, ticks: { callback: (value) => value + '%' } },
                    x: { grid: { display: false }, ticks: { maxTicksLimit: 8 } },
                },
            },
        });

        new Chart(document.getElementById('hoursChart'), {
            type: 'bar',
            data: {
                labels: hours.map((day) => day.label),
                datasets: [
                    { label: 'Planned', data: hours.map((day) => day.planned), backgroundColor: 'rgba(99, 102, 241, 0.55)', borderRadius: 4 },
                    { label: 'Actual', data: hours.map((day) => day.actual), backgroundColor: 'rgba(34, 197, 94, 0.65)', borderRadius: 4 },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, padding: 16 } } },
                scales: {
                    y: { beginAtZero: true, grid: { color: grid } },
                    x: { grid: { display: false } },
                },
            },
        });
    })();
</script>
@endpush
