@extends('layouts.dashboard')

@php
    use App\Models\Transaction;

    $money = fn (int $amount): string => Transaction::money($amount);

    // Read the ceiling as a traffic light: green while there is room, amber
    // once the month is running hot, red once it is gone.
    $budgetTone = match (true) {
        $budget['budget'] === null => 'neutral',
        $budget['over'] => 'danger',
        ($budget['used'] ?? 0) >= 80 => 'warn',
        default => 'good',
    };
    $toneColor = ['good' => '#22C55E', 'warn' => '#F59E0B', 'danger' => '#EF4444', 'neutral' => '#8B95A5'][$budgetTone];
@endphp

@section('title', 'Finance')
@section('breadcrumb', 'Finance')
@section('header_title', 'Finance')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <p class="text-sm text-[var(--text-muted)]">
            {{ $month->format('F Y') }} — what came in, what went out, and whether it holds.
        </p>

        <div class="flex items-center gap-3 flex-wrap">
            <form method="GET">
                <select name="month" onchange="this.form.submit()" class="input cursor-pointer !w-auto !py-2 text-sm">
                    @foreach($months as $value => $label)
                    <option value="{{ $value }}" @selected($month->toDateString() === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </form>
            @can('transactions.create')
            <a href="{{ route('admin.transactions.create') }}" class="btn-primary">
                <x-lucide-plus class="w-4 h-4" />
                Add transaction
            </a>
            @endcan
        </div>
    </div>

    {{-- Anything already over its ceiling is said first, before the numbers
         that explain it. --}}
    @if($breaches->isNotEmpty())
    <div class="card p-5 border-l-4" style="border-left-color: #F59E0B;">
        <div class="flex items-start gap-3">
            <x-lucide-triangle-alert class="w-5 h-5 flex-shrink-0 mt-0.5" style="color: #F59E0B;" />
            <div class="min-w-0">
                <h3 class="font-bold text-white text-sm">Limits under pressure</h3>
                <ul class="mt-2 space-y-1">
                    @foreach($breaches as $breach)
                    <li class="text-sm text-[var(--text-secondary)]">
                        <span class="font-semibold text-white">{{ $breach['category']->label() }}</span>
                        — {{ $money($breach['total']) }} of {{ $money($breach['limit']) }}
                        <span class="font-mono" style="color: {{ $breach['used'] >= 100 ? '#EF4444' : '#F59E0B' }};">
                            ({{ $breach['used'] }}%)
                        </span>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    @endif

    {{-- Headline numbers --}}
    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach([
            ['Income', $summary['income'], 'trending-up', '#22C55E'],
            ['Expense', $summary['expense'], 'trending-down', '#EF4444'],
            ['Balance', $summary['balance'], 'scale', $summary['balance'] >= 0 ? '#22C55E' : '#EF4444'],
            ['Daily average', $summary['daily_average'], 'calendar-days', '#0EA5E9'],
        ] as [$label, $value, $icon, $color])
        <div class="card p-5">
            <div class="flex items-center justify-between gap-3">
                <span class="text-xs font-semibold uppercase tracking-wider text-[var(--text-muted)]">{{ $label }}</span>
                <x-dynamic-component :component="'lucide-' . $icon" class="w-4 h-4" style="color: {{ $color }};" />
            </div>
            <p class="mt-3 text-2xl font-bold tracking-tight" style="color: {{ $color }};">
                {{ $money(abs($value)) }}
            </p>
            @if($label === 'Daily average')
            <p class="mt-1 text-xs text-[var(--text-muted)]">over {{ $summary['days'] }} days · {{ $summary['count'] }} entries</p>
            @endif
        </div>
        @endforeach
    </div>

    {{-- Budget pace --}}
    <div class="card p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="font-bold text-white">Monthly budget</h3>
                @if($budget['budget'] === null)
                <p class="text-sm text-[var(--text-muted)] mt-1">
                    No ceiling set yet.
                    @can('finance-settings.edit')
                    <a href="{{ route('admin.finance-settings.index') }}" class="text-[var(--accent-hover)] hover:underline">Set one</a>
                    and this becomes a warning system rather than a report.
                    @endcan
                </p>
                @else
                <p class="text-sm text-[var(--text-muted)] mt-1">
                    {{ $money($budget['spent']) }} of {{ $money($budget['budget']) }}
                    · {{ $budget['left'] >= 0 ? $money($budget['left']) . ' left' : $money(abs($budget['left'])) . ' over' }}
                </p>
                @endif
            </div>
            @if($budget['budget'] !== null)
            <div class="text-right">
                <p class="text-3xl font-bold font-mono tracking-tight" style="color: {{ $toneColor }};">{{ $budget['used'] }}%</p>
                <p class="text-xs text-[var(--text-muted)] mt-0.5">
                    on pace for {{ $budget['pace'] }}% by month end
                </p>
            </div>
            @endif
        </div>

        @if($budget['budget'] !== null)
        <div class="mt-5 h-2.5 rounded-full overflow-hidden" style="background: rgba(255,255,255,0.07);">
            <div class="h-full rounded-full transition-all"
                style="width: {{ min(100, $budget['used']) }}%; background: {{ $toneColor }};"></div>
        </div>
        @endif
    </div>

    {{-- Charts --}}
    <div class="grid gap-4 lg:grid-cols-2">
        <div class="card p-6">
            <h3 class="font-bold text-white mb-4">Day by day</h3>
            <div class="h-56"><canvas id="dailyChart"></canvas></div>
        </div>
        <div class="card p-6">
            <h3 class="font-bold text-white mb-4">Last six months</h3>
            <div class="h-56"><canvas id="monthlyChart"></canvas></div>
        </div>
    </div>

    {{-- Where the money went --}}
    <div class="grid gap-4 lg:grid-cols-2">
        <div class="card p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-white">Where it went</h3>
                <a href="{{ route('admin.transactions.index', ['period' => 'month', 'kind' => 'expense']) }}"
                    class="text-xs font-semibold text-[var(--accent-hover)] hover:text-[var(--accent-alt)]">All expenses →</a>
            </div>

            @forelse($byCategory as $row)
            <div class="py-2.5 {{ ! $loop->last ? 'border-b border-[var(--border-subtle)]' : '' }}">
                <div class="flex items-center justify-between gap-3 text-sm">
                    <span class="font-medium text-white truncate">
                        {{ $row['category']?->label() ?? 'Uncategorised' }}
                    </span>
                    <span class="font-mono text-[var(--text-secondary)] flex-shrink-0">{{ $money($row['total']) }}</span>
                </div>
                <div class="mt-1.5 flex items-center gap-2">
                    <div class="flex-1 h-1.5 rounded-full overflow-hidden" style="background: rgba(255,255,255,0.07);">
                        <div class="h-full rounded-full"
                            style="width: {{ $row['share'] }}%; background: {{ $row['category']?->color ?? '#8B95A5' }};"></div>
                    </div>
                    <span class="text-xs font-mono text-[var(--text-muted)] w-12 text-right">{{ $row['share'] }}%</span>
                </div>
            </div>
            @empty
            <p class="text-sm text-[var(--text-muted)] py-8 text-center">Nothing spent this month yet.</p>
            @endforelse
        </div>

        <div class="space-y-4">
            <div class="card p-6">
                <h3 class="font-bold text-white mb-3">Biggest single expenses</h3>
                @forelse($largest as $item)
                <div class="flex items-center justify-between gap-3 py-2 text-sm {{ ! $loop->last ? 'border-b border-[var(--border-subtle)]' : '' }}">
                    <div class="min-w-0">
                        <p class="font-medium text-white truncate">{{ $item->category?->label() ?? 'Uncategorised' }}</p>
                        <p class="text-xs text-[var(--text-muted)]">{{ $item->occurredLabel() }}{{ $item->note ? ' · ' . $item->note : '' }}</p>
                    </div>
                    <span class="font-mono text-[var(--text-secondary)] flex-shrink-0">{{ $money($item->amount) }}</span>
                </div>
                @empty
                <p class="text-sm text-[var(--text-muted)] py-6 text-center">Nothing yet.</p>
                @endforelse
            </div>

            <div class="card p-6">
                <h3 class="font-bold text-white mb-3">Income by source</h3>
                @forelse($byIncome as $row)
                <div class="flex items-center justify-between gap-3 py-2 text-sm {{ ! $loop->last ? 'border-b border-[var(--border-subtle)]' : '' }}">
                    <span class="font-medium text-white truncate">{{ $row['category']?->label() ?? 'Uncategorised' }}</span>
                    <span class="font-mono" style="color: #22C55E;">{{ $money($row['total']) }}</span>
                </div>
                @empty
                <p class="text-sm text-[var(--text-muted)] py-6 text-center">No income recorded this month.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Recent activity --}}
    <div class="card p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-white">Latest entries</h3>
            <a href="{{ route('admin.transactions.index') }}"
                class="text-xs font-semibold text-[var(--accent-hover)] hover:text-[var(--accent-alt)]">Open the ledger →</a>
        </div>

        @forelse($recent as $item)
        <div class="flex items-center justify-between gap-3 py-2.5 text-sm {{ ! $loop->last ? 'border-b border-[var(--border-subtle)]' : '' }}">
            <div class="flex items-center gap-3 min-w-0">
                <span class="w-2 h-2 rounded-full flex-shrink-0" style="background: {{ $item->kind->color() }};"></span>
                <div class="min-w-0">
                    <p class="font-medium text-white truncate">{{ $item->category?->label() ?? 'Uncategorised' }}</p>
                    <p class="text-xs text-[var(--text-muted)]">
                        {{ $item->occurredLabel() }}
                        · {{ $item->method->icon() }} {{ $item->method->label() }}
                        @if($item->source === \App\Enums\TransactionSource::Telegram) · via Telegram @endif
                    </p>
                </div>
            </div>
            <span class="font-mono flex-shrink-0" style="color: {{ $item->kind->color() }};">{{ $item->formattedAmount() }}</span>
        </div>
        @empty
        <p class="text-sm text-[var(--text-muted)] py-8 text-center">
            Nothing recorded yet. Add the first entry, or just tell the bot "ovqat 25000".
        </p>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof Chart === 'undefined') return;

        // Read the panel's own tokens so the charts follow the light/dark switch
        const styles = getComputedStyle(document.documentElement);
        const ink = styles.getPropertyValue('--text-muted').trim() || '#8B95A5';
        const grid = 'rgba(255,255,255,0.06)';

        Chart.defaults.color = ink;
        Chart.defaults.font.family = "'Plus Jakarta Sans', system-ui, sans-serif";

        const daily = @js($daily->values());
        const dailyLabels = @js($daily->keys()->map(fn ($day) => (int) substr($day, 8, 2)));

        new Chart(document.getElementById('dailyChart'), {
            type: 'bar',
            data: {
                labels: dailyLabels,
                datasets: [
                    { label: 'Expense', data: daily.map(d => d.expense), backgroundColor: '#EF4444' },
                    { label: 'Income', data: daily.map(d => d.income), backgroundColor: '#22C55E' },
                ],
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    x: { grid: { display: false } },
                    y: { grid: { color: grid }, ticks: { callback: v => (v / 1000) + 'k' } },
                },
                plugins: { legend: { labels: { boxWidth: 12 } } },
            },
        });

        const monthly = @js($monthly->values());
        new Chart(document.getElementById('monthlyChart'), {
            type: 'line',
            data: {
                labels: @js($monthly->keys()),
                datasets: [
                    { label: 'Expense', data: monthly.map(m => m.expense), borderColor: '#EF4444', tension: 0.35 },
                    { label: 'Income', data: monthly.map(m => m.income), borderColor: '#22C55E', tension: 0.35 },
                ],
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    x: { grid: { display: false } },
                    y: { grid: { color: grid }, ticks: { callback: v => (v / 1000) + 'k' } },
                },
                plugins: { legend: { labels: { boxWidth: 12 } } },
            },
        });
    });
</script>
@endpush
