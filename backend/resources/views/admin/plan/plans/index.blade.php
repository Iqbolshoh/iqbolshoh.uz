@extends('layouts.dashboard')

@php
    use App\Enums\PlanStatus;
    use App\Enums\Priority;
    use App\Models\Plan;
    use Illuminate\Support\Str;
@endphp

@section('title', 'Daily plans')
@section('breadcrumb', 'Plan')
@section('header_title', 'Daily plans')

@section('content')
<div x-data="{ deleteModalOpen: false, deleteUrl: '', deleteName: '' }">

    @include('admin.plan.partials.delete-modal', ['what' => 'plan'])

    {{-- Filters --}}
    <form method="GET" class="card p-4 mb-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
        <div>
            <label for="filter-date" class="block text-[11px] font-bold uppercase tracking-wider text-[var(--text-muted)] mb-1.5">Date</label>
            <input type="date" id="filter-date" name="date" value="{{ $filters['date'] }}" class="input !py-2 text-sm">
        </div>

        <div>
            <label for="filter-status" class="block text-[11px] font-bold uppercase tracking-wider text-[var(--text-muted)] mb-1.5">Status</label>
            <select id="filter-status" name="status" class="input !py-2 text-sm cursor-pointer">
                <option value="">All</option>
                @foreach(PlanStatus::cases() as $case)
                <option value="{{ $case->value }}" @selected($filters['status'] === $case->value)>{{ $case->label() }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="filter-goal" class="block text-[11px] font-bold uppercase tracking-wider text-[var(--text-muted)] mb-1.5">Goal</label>
            <select id="filter-goal" name="goal" class="input !py-2 text-sm cursor-pointer">
                <option value="">All</option>
                @foreach($goals as $id => $label)
                <option value="{{ $id }}" @selected((string) $filters['goal'] === (string) $id)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label for="filter-priority" class="block text-[11px] font-bold uppercase tracking-wider text-[var(--text-muted)] mb-1.5">Priority</label>
            <select id="filter-priority" name="priority" class="input !py-2 text-sm cursor-pointer">
                <option value="">All</option>
                @foreach(Priority::cases() as $case)
                <option value="{{ $case->value }}" @selected($filters['priority'] === $case->value)>{{ $case->label() }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex items-end gap-2">
            <button type="submit" class="btn-secondary flex-1 !py-2 text-sm">
                <x-lucide-filter class="w-4 h-4" />Filter
            </button>
            <a href="{{ route('admin.plans.index') }}" class="btn-ghost !py-2 text-sm" title="Clear">
                <x-lucide-x class="w-4 h-4" />
            </a>
        </div>
    </form>

    <div class="flex items-center justify-between gap-4 mb-5">
        <p class="text-sm text-[var(--text-muted)]">{{ $plans->total() }} plans</p>
        @can('plans.create')
        <a href="{{ route('admin.plans.create') }}" class="btn-primary">
            <x-lucide-plus class="w-4 h-4" />
            New plan
        </a>
        @endcan
    </div>

    <div class="space-y-2">
        @forelse($plans as $plan)
        {{-- The colour rail is the goal's, so a long list stays scannable by
             project without reading a single label. --}}
        <div class="card p-4 border-l-[3px]" style="border-left-color: {{ $plan->goal?->color ?? 'transparent' }};">
            <div class="flex flex-col lg:flex-row lg:items-center gap-3 lg:gap-4">

                <div class="flex items-baseline gap-2.5 lg:w-36 lg:flex-shrink-0">
                    <span class="font-mono text-lg font-bold text-white leading-none">{{ Str::substr($plan->start_time, 0, 5) }}</span>
                    <span class="text-xs text-[var(--text-muted)] whitespace-nowrap">{{ $plan->date->format('D, M j') }}</span>
                    <span class="text-xs font-mono text-[var(--text-muted)] lg:hidden">· {{ Plan::humanMinutes($plan->planned_minutes) }}</span>
                </div>

                <div class="min-w-0 flex-1">
                    <a href="{{ route('admin.plans.show', $plan) }}" class="font-semibold text-white hover:text-[var(--accent-hover)] transition-colors break-words">
                        {{ $plan->title }}
                    </a>
                    <div class="flex flex-wrap items-center gap-x-2 gap-y-1 mt-1 text-xs text-[var(--text-muted)]">
                        @if($plan->goal)
                        <span class="inline-flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full flex-shrink-0" style="background: {{ $plan->goal->color ?? '#8B95A5' }};"></span>
                            {{ $plan->goal->title }}
                        </span>
                        @endif
                        <span class="hidden lg:inline">· {{ Plan::humanMinutes($plan->planned_minutes) }} planned</span>
                        @if($plan->postpone_count > 0)
                        <span>· pushed {{ $plan->postpone_count }}×</span>
                        @endif
                        @if($plan->actual_minutes)
                        <span>· took {{ Plan::humanMinutes($plan->actual_minutes) }}</span>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-1.5 flex-wrap lg:flex-nowrap lg:justify-end">
                    <x-status-badge :status="$plan->status" />

                    @can('plans.edit')
                    @unless($plan->status->isClosed())
                    <form method="POST" action="{{ route('admin.plans.act', [$plan, 'complete']) }}" class="inline">
                        @csrf
                        <button type="submit" title="Mark completed"
                            class="p-2 rounded-lg text-[var(--text-muted)] hover:text-green-400 hover:bg-green-500/10 transition-colors cursor-pointer">
                            <x-lucide-check class="w-4 h-4" />
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.plans.act', [$plan, 'postpone']) }}" class="inline">
                        @csrf
                        <input type="hidden" name="minutes" value="30">
                        <button type="submit" title="Postpone 30 minutes"
                            class="p-2 rounded-lg text-[var(--text-muted)] hover:text-amber-400 hover:bg-amber-500/10 transition-colors cursor-pointer">
                            <x-lucide-clock class="w-4 h-4" />
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.plans.act', [$plan, 'fail']) }}" class="inline">
                        @csrf
                        <button type="submit" title="Mark failed"
                            class="p-2 rounded-lg text-[var(--text-muted)] hover:text-red-400 hover:bg-red-500/10 transition-colors cursor-pointer">
                            <x-lucide-x class="w-4 h-4" />
                        </button>
                    </form>
                    @endunless

                    <a href="{{ route('admin.plans.edit', $plan) }}" title="Edit"
                        class="p-2 rounded-lg text-[var(--text-muted)] hover:text-[var(--accent-hover)] hover:bg-[var(--accent-soft)] transition-colors">
                        <x-lucide-pencil class="w-4 h-4" />
                    </a>
                    @endcan

                    @can('plans.delete')
                    <button type="button" title="Delete"
                        @click="deleteUrl = @js(route('admin.plans.destroy', $plan)); deleteName = @js($plan->title); deleteModalOpen = true"
                        class="p-2 rounded-lg text-[var(--text-muted)] hover:text-[var(--accent)] hover:bg-[var(--accent-soft)] transition-colors">
                        <x-lucide-trash-2 class="w-4 h-4" />
                    </button>
                    @endcan
                </div>
            </div>
        </div>
        @empty
        <div class="card px-6 py-16 text-center">
            <div class="flex flex-col items-center gap-3">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background: rgba(255,255,255,0.04); border: 1px solid var(--border-subtle);">
                    <x-lucide-list-checks class="w-6 h-6 text-[var(--text-muted)]" />
                </div>
                <p class="text-sm font-semibold text-[var(--text-secondary)]">No plans match these filters</p>
                @can('plans.create')
                <a href="{{ route('admin.plans.create') }}" class="btn-primary !text-xs !py-2 !px-4 mt-1">
                    <x-lucide-plus class="w-3.5 h-3.5" />New plan
                </a>
                @endcan
            </div>
        </div>
        @endforelse
    </div>

    @if($plans->hasPages())
    <div class="mt-6">{{ $plans->links() }}</div>
    @endif
</div>
@endsection
