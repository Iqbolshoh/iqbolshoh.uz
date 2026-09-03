@extends('layouts.dashboard')

@php
    use App\Models\Transaction;
    $money = fn (int $amount): string => Transaction::money($amount);
@endphp

@section('title', 'Categories')
@section('breadcrumb', 'Finance')
@section('header_title', 'Categories')

@section('content')
<div x-data="{ deleteModalOpen: false, deleteUrl: '', deleteName: '' }" class="space-y-8">

    @include('admin.partials.delete-modal', ['what' => 'category'])

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <p class="text-sm text-[var(--text-muted)]">
            The buckets, their ceilings for {{ $month->format('F Y') }}, and the words the bot recognises them by.
        </p>
        <div class="flex items-center gap-3">
            @can('finance-categories.create')
            <form action="{{ route('admin.finance-categories.restore') }}" method="POST">
                @csrf
                <button type="submit" class="btn-secondary" title="Add any starter category this account is missing">
                    <x-lucide-rotate-ccw class="w-4 h-4" />
                    Restore defaults
                </button>
            </form>
            <a href="{{ route('admin.finance-categories.create') }}" class="btn-primary">
                <x-lucide-plus class="w-4 h-4" />
                New category
            </a>
            @endcan
        </div>
    </div>

    @foreach(['expense' => 'Expense categories', 'income' => 'Income categories'] as $kind => $heading)
    <div>
        <h3 class="text-sm font-bold uppercase tracking-wider text-[var(--text-muted)] mb-4">{{ $heading }}</h3>

        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            @forelse($categories[$kind] ?? [] as $category)
            @php
                $used = $spent[$category->id]['total'] ?? 0;
                $share = $category->monthly_limit > 0 ? min(100, round($used / $category->monthly_limit * 100, 1)) : null;
                $over = $category->monthly_limit > 0 && $used > $category->monthly_limit;
            @endphp
            <div class="card p-5 flex flex-col gap-4 {{ $category->is_active ? '' : 'opacity-60' }}">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background: {{ $category->color ?? '#8B95A5' }};"></span>
                            <h4 class="font-bold text-white truncate">{{ $category->label() }}</h4>
                        </div>
                        <p class="text-xs text-[var(--text-muted)] mt-1">
                            {{ $category->transactions_count }} entries
                            @unless($category->is_active)
                            · <span class="font-semibold" style="color: #F59E0B;">inactive</span>
                            @endunless
                        </p>
                    </div>

                    <div class="flex items-center gap-1 flex-shrink-0">
                        @can('finance-categories.edit')
                        <a href="{{ route('admin.finance-categories.edit', $category) }}" title="Edit"
                            class="p-2 rounded-lg text-[var(--text-muted)] hover:text-[var(--accent-hover)] hover:bg-[var(--accent-soft)] transition-colors">
                            <x-lucide-pencil class="w-4 h-4" />
                        </a>
                        @endcan
                        @can('finance-categories.delete')
                        <button type="button" title="Delete"
                            @click="deleteUrl = @js(route('admin.finance-categories.destroy', $category)); deleteName = @js($category->name); deleteModalOpen = true"
                            class="p-2 rounded-lg text-[var(--text-muted)] hover:text-[var(--accent)] hover:bg-[var(--accent-soft)] transition-colors">
                            <x-lucide-trash-2 class="w-4 h-4" />
                        </button>
                        @endcan
                    </div>
                </div>

                @if($category->monthly_limit)
                <div>
                    <div class="flex items-center justify-between text-xs mb-1.5">
                        <span class="text-[var(--text-muted)]">{{ $money($used) }} of {{ $money($category->monthly_limit) }}</span>
                        <span class="font-mono font-bold" style="color: {{ $over ? '#EF4444' : ($share >= 80 ? '#F59E0B' : '#22C55E') }};">
                            {{ $share }}%
                        </span>
                    </div>
                    <div class="h-2 rounded-full overflow-hidden" style="background: rgba(255,255,255,0.07);">
                        <div class="h-full rounded-full transition-all"
                            style="width: {{ $share }}%; background: {{ $over ? '#EF4444' : ($category->color ?? 'var(--accent-hover)') }};"></div>
                    </div>
                </div>
                @else
                <p class="text-xs text-[var(--text-muted)]">
                    No ceiling · {{ $used > 0 ? $money($used) . ' this month' : 'nothing this month' }}
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
                <p class="text-sm text-[var(--text-muted)]">No {{ $kind }} categories yet.</p>
            </div>
            @endforelse
        </div>
    </div>
    @endforeach
</div>
@endsection
