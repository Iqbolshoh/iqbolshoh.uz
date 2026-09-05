@extends('layouts.dashboard')

@section('title', 'Dashboard')
@section('header_title', 'Dashboard')

@section('header_actions')
<a href="{{ url('/') }}" target="_blank" rel="noopener" class="btn-secondary">
    <x-lucide-external-link class="w-4 h-4" />
    Open the site
</a>
@endsection

@section('content')
@php
    use App\Models\ActivityEntry;
    use App\Models\Plan;
    use App\Models\Transaction;
    use Illuminate\Support\Js;
    use Illuminate\Support\Str;

    // Section key → sidebar label and icon, so the tiles link straight to the
    // page that edits them.
    $sections = [
        'projects'      => ['Projects', 'folder-git-2'],
        'services'      => ['Services', 'briefcase'],
        'tech-stacks'   => ['Technologies', 'layers'],
        'stats'         => ['Stats', 'bar-chart-3'],
        'highlights'    => ['Highlights', 'sparkles'],
        'journeys'      => ['Journey', 'milestone'],
        'beyonds'       => ['Beyond code', 'heart-handshake'],
        'process-steps' => ['Process', 'list-checks'],
    ];
@endphp

<div class="mb-8">
    <h2 class="text-2xl font-bold text-white tracking-tight">Hello, {{ $user->name }}</h2>
    <p class="text-sm text-[var(--text-muted)] mt-1">Manage every piece of iqbolshoh.uz content from here.</p>
</div>

{{-- Today, from Plan --}}
@php
    $today = $plan['today'];
    $month = $plan['month'];
@endphp

<div class="grid gap-4 grid-cols-2 lg:grid-cols-4 mb-6">
    @foreach([
        ['Today', $today['total'] . ' plans', 'list-checks', null, route('admin.plans.index', ['date' => now()->toDateString()])],
        ['Completed', $today['completed'], 'check-check', '#22C55E', null],
        ['Completion', $today['raw_rate'] . '%', 'percent', null, null],
        ['This month', $month['raw_rate'] . '%', 'trending-up', '#0EA5E9', route('admin.analytics.monthly')],
    ] as [$label, $value, $icon, $color, $href])
    <a @if($href) href="{{ $href }}" @endif class="card p-5 {{ $href ? 'card-hover' : '' }}">
        <div class="flex items-center justify-between">
            <p class="text-[11px] font-bold uppercase tracking-wider text-[var(--text-muted)]">{{ $label }}</p>
            <x-dynamic-component :component="'lucide-' . $icon" class="w-4 h-4 text-[var(--text-muted)]" />
        </div>
        <p class="text-2xl font-bold font-mono mt-2" style="color: {{ $color ?? 'var(--text-primary)' }};">{{ $value }}</p>
    </a>
    @endforeach
</div>

<div class="grid gap-5 lg:grid-cols-3 mb-6">
    <div class="card p-6 lg:col-span-2">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-[var(--text-muted)]">Last 14 days</h3>
            <a href="{{ route('admin.analytics.monthly') }}" class="text-xs font-semibold text-[var(--accent-hover)] hover:text-[var(--accent-alt)] transition-colors">Analytics →</a>
        </div>
        <div class="h-48"><canvas id="dashboardTrend"></canvas></div>
        <p class="text-xs text-[var(--text-muted)] mt-3">
            ⏱ Planned {{ Plan::humanMinutes($month['planned_minutes']) }}
            · Actual {{ Plan::humanMinutes($month['actual_minutes']) }} this month
        </p>
    </div>

    <div class="card p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold uppercase tracking-wider text-[var(--text-muted)]">Coming up</h3>
            <a href="{{ route('admin.plans.index') }}" class="text-xs font-semibold text-[var(--accent-hover)] hover:text-[var(--accent-alt)] transition-colors">All →</a>
        </div>

        @forelse($plan['upcoming'] as $upcoming)
        <a href="{{ route('admin.plans.show', $upcoming) }}"
            class="flex items-center gap-3 py-2.5 border-b border-[var(--border-subtle)] last:border-0 group">
            <span class="font-mono text-sm font-bold text-white flex-shrink-0">{{ Str::substr($upcoming->start_time, 0, 5) }}</span>
            <span class="min-w-0 flex-1">
                <span class="block text-sm text-[var(--text-secondary)] group-hover:text-white transition-colors truncate">{{ $upcoming->title }}</span>
                <span class="block text-[11px] text-[var(--text-muted)]">{{ $upcoming->date->format('D, j M') }}</span>
            </span>
            @if($upcoming->goal)
            <span class="w-2 h-2 rounded-full flex-shrink-0" style="background: {{ $upcoming->goal->color ?? '#8B95A5' }};"></span>
            @endif
        </a>
        @empty
        <p class="text-sm text-[var(--text-muted)] py-6 text-center">Nothing scheduled.</p>
        @endforelse
    </div>
</div>

{{-- Money and time, beside the plans: the panel is opened to ask how it is
     going, and that question has three halves. --}}
<div class="grid gap-5 lg:grid-cols-2 mb-6">

    <div class="card p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-sm font-bold uppercase tracking-wider text-[var(--text-muted)]">Money · {{ now()->format('F') }}</h3>
            <a href="{{ route('admin.finance.index') }}" class="text-xs font-semibold text-[var(--accent-hover)] hover:text-[var(--accent-alt)] transition-colors">Details →</a>
        </div>

        <div class="grid grid-cols-3 gap-3 mb-5">
            @foreach([
                ['Today', Transaction::money($money['today']['expense']), null],
                ['Spent', Transaction::money($money['month']['expense']), '#EF4444'],
                ['Received', Transaction::money($money['month']['income']), '#22C55E'],
            ] as [$label, $value, $color])
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-[var(--text-muted)]">{{ $label }}</p>
                <p class="text-base font-bold tracking-tight mt-1" style="color: {{ $color ?? 'var(--text-primary)' }};">{{ $value }}</p>
            </div>
            @endforeach
        </div>

        @if($money['budget']['budget'] !== null)
        @php $used = min(100, round(($money['month']['expense']) / max(1, $money['budget']['budget']) * 100, 1)); @endphp
        <div class="mb-5">
            <div class="flex items-center justify-between text-xs mb-1.5">
                <span class="text-[var(--text-muted)]">
                    {{ Transaction::money($money['budget']['budget']) }} budget
                </span>
                <span class="font-mono font-bold" style="color: {{ $used >= 100 ? '#EF4444' : ($used >= 80 ? '#F59E0B' : '#22C55E') }};">{{ $used }}%</span>
            </div>
            <div class="h-2 rounded-full overflow-hidden" style="background: rgba(255,255,255,0.07);">
                <div class="h-full rounded-full" style="width: {{ $used }}%; background: {{ $used >= 100 ? '#EF4444' : ($used >= 80 ? '#F59E0B' : '#22C55E') }};"></div>
            </div>
        </div>
        @endif

        @forelse($money['top'] as $row)
        <div class="flex items-center justify-between gap-3 py-2 border-b border-[var(--border-subtle)] last:border-0">
            <span class="flex items-center gap-2 min-w-0">
                <span class="w-2 h-2 rounded-full flex-shrink-0" style="background: {{ $row['category']?->color ?? '#8B95A5' }};"></span>
                <span class="text-sm text-[var(--text-secondary)] truncate">{{ $row['category']?->label() ?? 'Uncategorised' }}</span>
            </span>
            <span class="font-mono text-xs font-semibold text-white flex-shrink-0">{{ Transaction::money($row['total']) }}</span>
        </div>
        @empty
        <p class="text-sm text-[var(--text-muted)] py-6 text-center">Nothing recorded this month.</p>
        @endforelse
    </div>

    <div class="card p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-sm font-bold uppercase tracking-wider text-[var(--text-muted)]">Time · {{ now()->format('F') }}</h3>
            <a href="{{ route('admin.activities.index') }}" class="text-xs font-semibold text-[var(--accent-hover)] hover:text-[var(--accent-alt)] transition-colors">Details →</a>
        </div>

        <div class="grid grid-cols-3 gap-3 mb-5">
            @foreach([
                ['Today', ActivityEntry::duration($time['today']['minutes']), null],
                ['This month', ActivityEntry::duration($time['month']['minutes']), null],
                ['Accounted', $time['month']['covered'] . '%', $time['month']['covered'] >= 70 ? '#22C55E' : '#F59E0B'],
            ] as [$label, $value, $color])
            <div>
                <p class="text-[10px] font-bold uppercase tracking-wider text-[var(--text-muted)]">{{ $label }}</p>
                <p class="text-base font-bold tracking-tight mt-1" style="color: {{ $color ?? 'var(--text-primary)' }};">{{ $value }}</p>
            </div>
            @endforeach
        </div>

        @if($time['good'] + $time['bad'] > 0)
        @php $logged = max(1, $time['good'] + $time['bad']); @endphp
        <div class="mb-5">
            <div class="flex h-2 rounded-full overflow-hidden mb-1.5">
                <div style="width: {{ round($time['good'] / $logged * 100) }}%; background: #22C55E;"></div>
                <div style="width: {{ round($time['bad'] / $logged * 100) }}%; background: #F59E0B;"></div>
            </div>
            <p class="text-xs text-[var(--text-muted)]">
                Useful {{ ActivityEntry::duration($time['good']) }} · wasted {{ ActivityEntry::duration($time['bad']) }}
            </p>
        </div>
        @endif

        @forelse($time['top'] as $row)
        <div class="flex items-center justify-between gap-3 py-2 border-b border-[var(--border-subtle)] last:border-0">
            <span class="flex items-center gap-2 min-w-0">
                <span class="w-2 h-2 rounded-full flex-shrink-0" style="background: {{ $row['category']?->color ?? '#8B95A5' }};"></span>
                <span class="text-sm text-[var(--text-secondary)] truncate">{{ $row['category']?->label() ?? 'Uncategorised' }}</span>
            </span>
            <span class="font-mono text-xs font-semibold text-white flex-shrink-0">{{ ActivityEntry::duration($row['minutes']) }}</span>
        </div>
        @empty
        <p class="text-sm text-[var(--text-muted)] py-6 text-center">Nothing logged this month.</p>
        @endforelse
    </div>
</div>

{{-- Inbox --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-6">
    @foreach([
        'contact' => ['Contact messages', 'mail'],
        'orders'  => ['Service orders', 'shopping-bag'],
    ] as $type => [$label, $icon])
    <a href="{{ route('admin.messages.index', $type) }}" class="card card-hover p-6 flex items-center gap-5">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-[var(--accent-soft)] border border-[var(--accent-border)] flex-shrink-0">
            <x-dynamic-component :component="'lucide-' . $icon" class="w-5 h-5 text-[var(--accent-hover)]" />
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-semibold text-[var(--text-secondary)]">{{ $label }}</p>
            <p class="text-3xl font-extrabold text-white leading-tight mt-0.5 font-mono">{{ $inbox[$type]['total'] }}</p>
        </div>
        @if($inbox[$type]['unread'] > 0)
        <span class="badge badge-accent flex-shrink-0">{{ $inbox[$type]['unread'] }} new</span>
        @endif
    </a>
    @endforeach
</div>

{{-- Content sections --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    @foreach($sections as $key => [$label, $icon])
    <a href="{{ route('admin.' . $key . '.index') }}" class="card card-hover p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: rgba(255,255,255,0.04); border: 1px solid var(--border-subtle);">
                <x-dynamic-component :component="'lucide-' . $icon" class="w-4 h-4 text-[var(--text-secondary)]" />
            </div>
            <span class="text-2xl font-extrabold text-white font-mono leading-none">{{ $counts[$key] }}</span>
        </div>
        <p class="text-sm font-semibold text-[var(--text-secondary)] truncate">{{ $label }}</p>
    </a>
    @endforeach
</div>

{{-- Latest submissions --}}
<div class="card overflow-hidden">
    <div class="px-6 py-4 border-b border-[var(--border-subtle)] flex items-center justify-between gap-4">
        <h3 class="text-sm font-bold text-white">Latest enquiries</h3>
        <a href="{{ route('admin.messages.index', 'contact') }}" class="text-xs font-semibold text-[var(--accent-hover)] hover:text-[var(--accent-alt)] transition-colors">
            All
        </a>
    </div>

    <div class="divide-y divide-[var(--border-subtle)]">
        @forelse($recent as $entry)
        <a href="{{ route('admin.messages.show', [$entry['type'], $entry['id']]) }}"
            class="flex items-start gap-4 px-6 py-4 hover:bg-white/[0.02] transition-colors">

            <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-sm flex-shrink-0 text-white"
                style="background: linear-gradient(135deg, var(--accent-hover), var(--accent-alt));">
                {{ mb_strtoupper(mb_substr($entry['name'], 0, 1)) }}
            </div>

            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2 flex-wrap">
                    <p class="text-sm font-semibold text-white truncate">{{ $entry['name'] }}</p>
                    <span class="badge" style="background: rgba(255,255,255,0.05); color: var(--text-secondary);">
                        {{ $entry['type'] === 'contact' ? 'Message' : 'Order' }}
                    </span>
                    @if($entry['unread'])
                    <span class="badge badge-accent">New</span>
                    @endif
                </div>
                <p class="text-xs text-[var(--text-muted)] mt-1 truncate">{{ Str::limit($entry['summary'], 90) }}</p>
            </div>

            <span class="text-[0.68rem] text-[var(--text-muted)] font-mono whitespace-nowrap flex-shrink-0">
                {{ $entry['date']->format('d.m.Y') }}
            </span>
        </a>
        @empty
        <div class="px-6 py-14 text-center">
            <div class="flex flex-col items-center gap-3">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background: rgba(255,255,255,0.04); border: 1px solid var(--border-subtle);">
                    <x-lucide-inbox class="w-6 h-6 text-[var(--text-muted)]" />
                </div>
                <p class="text-sm font-semibold text-[var(--text-secondary)]">No enquiries yet</p>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    (function () {
        const trend = {!! Js::from($plan['trend']) !!};
        const grid = 'rgba(148, 163, 184, 0.12)';

        Chart.defaults.color = getComputedStyle(document.documentElement)
            .getPropertyValue('--text-muted').trim() || '#8B95A5';
        Chart.defaults.font.family = "'Plus Jakarta Sans', system-ui, sans-serif";

        new Chart(document.getElementById('dashboardTrend'), {
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
                    x: { grid: { display: false }, ticks: { maxTicksLimit: 7 } },
                },
            },
        });
    })();
</script>
@endpush
