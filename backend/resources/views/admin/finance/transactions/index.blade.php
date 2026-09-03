@extends('layouts.dashboard')

@php
    use App\Models\Transaction;
    $money = fn (int $amount): string => Transaction::money($amount);
@endphp

@section('title', 'Transactions')
@section('breadcrumb', 'Finance')
@section('header_title', 'Transactions')

@section('content')
<div x-data="{ deleteModalOpen: false, deleteUrl: '', deleteName: '' }" class="space-y-6">

    @include('admin.partials.delete-modal', ['what' => 'transaction'])

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <p class="text-sm text-[var(--text-muted)]">
            {{ $from->format('d.m.Y') }} — {{ $to->format('d.m.Y') }} · {{ $count }} entries
        </p>
        <div class="flex items-center gap-3">
            @can('transactions.view')
            <a href="{{ route('admin.transactions.export', request()->query()) }}" class="btn-secondary">
                <x-lucide-download class="w-4 h-4" />
                CSV
            </a>
            @endcan
            @can('transactions.create')
            <a href="{{ route('admin.transactions.create') }}" class="btn-primary">
                <x-lucide-plus class="w-4 h-4" />
                Add
            </a>
            @endcan
        </div>
    </div>

    {{-- Totals for the whole filtered set, not just the page on screen. --}}
    <div class="grid gap-4 sm:grid-cols-3">
        @foreach([['Income', $income, '#22C55E'], ['Expense', $expense, '#EF4444'], ['Balance', $balance, $balance >= 0 ? '#22C55E' : '#EF4444']] as [$label, $value, $color])
        <div class="card p-4">
            <p class="text-xs font-semibold uppercase tracking-wider text-[var(--text-muted)]">{{ $label }}</p>
            <p class="mt-1.5 text-xl font-bold tracking-tight" style="color: {{ $color }};">{{ $money(abs($value)) }}</p>
        </div>
        @endforeach
    </div>

    {{-- Filters --}}
    <form method="GET" class="card p-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-6 items-end">
        <div class="lg:col-span-2">
            <label for="q" class="block text-xs font-semibold text-[var(--text-muted)] mb-1.5">Search notes</label>
            <input type="search" id="q" name="q" value="{{ $filters['q'] }}" class="input !py-2 text-sm" placeholder="taksi, server…">
        </div>

        <div>
            <label for="period" class="block text-xs font-semibold text-[var(--text-muted)] mb-1.5">Period</label>
            <select id="period" name="period" class="input !py-2 text-sm cursor-pointer">
                @foreach(['today' => 'Today', 'week' => 'This week', 'month' => 'This month', 'year' => 'This year', 'all' => 'Everything'] as $value => $label)
                <option value="{{ $value }}" @selected($filters['period'] === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="kind" class="block text-xs font-semibold text-[var(--text-muted)] mb-1.5">Kind</label>
            <select id="kind" name="kind" class="input !py-2 text-sm cursor-pointer">
                <option value="">Both</option>
                @foreach($kinds as $kind)
                <option value="{{ $kind->value }}" @selected($filters['kind'] === $kind->value)>{{ $kind->label() }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="category" class="block text-xs font-semibold text-[var(--text-muted)] mb-1.5">Category</label>
            <select id="category" name="category" class="input !py-2 text-sm cursor-pointer">
                <option value="">All</option>
                @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected((string) $filters['category'] === (string) $category->id)>
                    {{ $category->label() }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="btn-primary flex-1 !py-2 !text-sm">Apply</button>
            <a href="{{ route('admin.transactions.index') }}" class="btn-secondary !py-2 !text-sm" title="Reset">
                <x-lucide-rotate-ccw class="w-4 h-4" />
            </a>
        </div>
    </form>

    {{-- Ledger --}}
    <div class="card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-[var(--border-subtle)] text-left">
                        <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider text-[var(--text-muted)]">#</th>
                        <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider text-[var(--text-muted)]">When</th>
                        <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider text-[var(--text-muted)]">Category</th>
                        <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider text-[var(--text-muted)]">Note</th>
                        <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider text-[var(--text-muted)]">Method</th>
                        <th class="px-5 py-3 font-semibold text-xs uppercase tracking-wider text-[var(--text-muted)] text-right">Amount</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $item)
                    <tr class="border-b border-[var(--border-subtle)] last:border-0 hover:bg-white/[0.02] transition-colors">
                        {{-- A running number, not the id: rolled-back inserts burn
                             ids and turn the column into noise. --}}
                        <td class="px-5 py-3 font-mono text-xs text-[var(--text-muted)]">
                            {{ $transactions->firstItem() + $loop->index }}
                        </td>
                        <td class="px-5 py-3 whitespace-nowrap text-[var(--text-secondary)]">{{ $item->occurredLabel() }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full flex-shrink-0"
                                    style="background: {{ $item->category?->color ?? '#8B95A5' }};"></span>
                                <span class="text-white">{{ $item->category?->label() ?? 'Uncategorised' }}</span>
                            </span>
                        </td>
                        <td class="px-5 py-3 text-[var(--text-muted)] max-w-[16rem] truncate">{{ $item->note ?? '—' }}</td>
                        <td class="px-5 py-3 whitespace-nowrap text-[var(--text-muted)]">
                            {{ $item->method->icon() }} {{ $item->method->label() }}
                            @if($item->source === \App\Enums\TransactionSource::Telegram)
                            <span class="ml-1 text-xs" title="Added from Telegram">✈️</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right font-mono whitespace-nowrap" style="color: {{ $item->kind->color() }};">
                            {{ $item->formattedAmount() }}
                        </td>
                        <td class="px-5 py-3">
                            <div class="flex items-center justify-end gap-1">
                                @can('transactions.edit')
                                <a href="{{ route('admin.transactions.edit', $item) }}" title="Edit"
                                    class="p-2 rounded-lg text-[var(--text-muted)] hover:text-[var(--accent-hover)] hover:bg-[var(--accent-soft)] transition-colors">
                                    <x-lucide-pencil class="w-4 h-4" />
                                </a>
                                @endcan
                                @can('transactions.delete')
                                <button type="button" title="Delete"
                                    @click="deleteUrl = @js(route('admin.transactions.destroy', $item)); deleteName = @js(($item->category?->name ?? 'Uncategorised') . ' — ' . $money($item->amount)); deleteModalOpen = true"
                                    class="p-2 rounded-lg text-[var(--text-muted)] hover:text-[var(--accent)] hover:bg-[var(--accent-soft)] transition-colors">
                                    <x-lucide-trash-2 class="w-4 h-4" />
                                </button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-5 py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl flex items-center justify-center"
                                    style="background: rgba(255,255,255,0.04); border: 1px solid var(--border-subtle);">
                                    <x-lucide-wallet class="w-6 h-6 text-[var(--text-muted)]" />
                                </div>
                                <p class="text-sm font-semibold text-[var(--text-secondary)]">Nothing in this window</p>
                                <p class="text-xs text-[var(--text-muted)]">Widen the period, or add the first entry.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($transactions->hasPages())
    <div>{{ $transactions->links() }}</div>
    @endif
</div>
@endsection
