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

@push('styles')
<style>
    /* A row is laid out as a grid, not a flex chain: the time and the action
       cluster get fixed tracks, so the title column can never grow into them
       however long a plan's name is. */
    .plan-row {
        display: grid;
        grid-template-columns: 1fr;
        gap: .75rem;
        padding: .9rem 1rem;
        background: var(--bg-raised);
        border: 1px solid var(--border-subtle);
        /* The layout's own `.card` rule is plain CSS loaded after Tailwind, so a
           utility like `border-l-[3px]` loses to it. Declaring the rail here
           keeps it visible. */
        border-left: 3px solid var(--rail, transparent);
        border-radius: var(--radius-lg);
        transition: border-color .2s, background .2s;
    }

    .plan-row:hover {
        background: var(--bg-overlay);
    }

    .plan-when {
        display: flex;
        align-items: baseline;
        gap: .55rem;
    }

    .plan-time {
        font-family: 'JetBrains Mono', ui-monospace, monospace;
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--text-primary);
        line-height: 1.2;
    }

    .plan-date {
        font-size: .75rem;
        color: var(--text-muted);
        white-space: nowrap;
    }

    .plan-body {
        min-width: 0;
    }

    .plan-title {
        display: block;
        font-size: .9375rem;
        font-weight: 600;
        color: var(--text-primary);
        line-height: 1.4;
        overflow-wrap: anywhere;
        transition: color .2s;
    }

    .plan-title:hover {
        color: var(--accent-hover);
    }

    .plan-meta {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: .25rem .75rem;
        margin-top: .3rem;
        font-size: .75rem;
        color: var(--text-muted);
    }

    /* Separators are drawn rather than typed, so no stray "·" is left dangling
       when a row happens to have only one meta item. */
    .plan-meta > span + span::before {
        content: "·";
        margin-right: .5rem;
        opacity: .55;
    }

    .plan-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: .75rem;
        padding-top: .7rem;
        border-top: 1px solid var(--border-subtle);
    }

    .plan-tools {
        display: inline-flex;
        align-items: center;
        gap: .15rem;
    }

    .plan-tool {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 2.1rem;
        height: 2.1rem;
        border-radius: var(--radius-md);
        color: var(--text-muted);
        cursor: pointer;
        transition: color .15s, background .15s;
    }

    .plan-tool:hover {
        color: var(--text-primary);
        background: rgba(148, 163, 184, .12);
    }

    .plan-tool.is-done:hover {
        color: #22C55E;
        background: rgba(34, 197, 94, .12);
    }

    .plan-tool.is-later:hover {
        color: #F59E0B;
        background: rgba(245, 158, 11, .12);
    }

    .plan-tool.is-fail:hover {
        color: #EF4444;
        background: rgba(239, 68, 68, .12);
    }

    /* From lg the row becomes three tracks. The action column is sized to its
       content so the buttons never crowd the title, and the divider that
       separated them on mobile is dropped. */
    @media (min-width: 1024px) {
        .plan-row {
            grid-template-columns: 9.5rem minmax(0, 1fr) auto;
            align-items: center;
            gap: 1.25rem;
        }

        .plan-when {
            flex-direction: column;
            align-items: flex-start;
            gap: .1rem;
        }

        .plan-actions {
            justify-content: flex-end;
            padding-top: 0;
            border-top: 0;
        }
    }
</style>
@endpush

@section('content')
<div x-data="{ deleteModalOpen: false, deleteUrl: '', deleteName: '' }">

    @include('admin.partials.delete-modal', ['what' => 'plan'])

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
        {{-- The rail carries the goal's colour, so a long list stays scannable
             by project without reading a single label. --}}
        <div class="plan-row" style="--rail: {{ $plan->goal?->color ?? 'transparent' }};">

            <div class="plan-when">
                <span class="plan-time">{{ Str::substr($plan->start_time, 0, 5) }}</span>
                <span class="plan-date">{{ $plan->date->format('D, M j') }}</span>
            </div>

            <div class="plan-body">
                <a href="{{ route('admin.plans.show', $plan) }}" class="plan-title">{{ $plan->title }}</a>
                <div class="plan-meta">
                    @if($plan->goal)
                    <span class="inline-flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full flex-shrink-0" style="background: {{ $plan->goal->color ?? '#8B95A5' }};"></span>
                        {{ $plan->goal->title }}
                    </span>
                    @endif
                    <span>{{ Plan::humanMinutes($plan->planned_minutes) }} planned</span>
                    @if($plan->postpone_count > 0)
                    <span>pushed {{ $plan->postpone_count }}×</span>
                    @endif
                    @if($plan->actual_minutes)
                    <span>took {{ Plan::humanMinutes($plan->actual_minutes) }}</span>
                    @endif
                </div>
            </div>

            <div class="plan-actions">
                <x-status-badge :status="$plan->status" />

                <span class="plan-tools">
                    @can('plans.edit')
                    @unless($plan->status->isClosed())
                    <form method="POST" action="{{ route('admin.plans.act', [$plan, 'complete']) }}">
                        @csrf
                        <button type="submit" title="Mark completed" class="plan-tool is-done">
                            <x-lucide-check class="w-4 h-4" />
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.plans.act', [$plan, 'postpone']) }}">
                        @csrf
                        <input type="hidden" name="minutes" value="30">
                        <button type="submit" title="Postpone 30 minutes" class="plan-tool is-later">
                            <x-lucide-clock class="w-4 h-4" />
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.plans.act', [$plan, 'fail']) }}">
                        @csrf
                        <button type="submit" title="Mark failed" class="plan-tool is-fail">
                            <x-lucide-x class="w-4 h-4" />
                        </button>
                    </form>
                    @endunless

                    <a href="{{ route('admin.plans.edit', $plan) }}" title="Edit" class="plan-tool">
                        <x-lucide-pencil class="w-4 h-4" />
                    </a>
                    @endcan

                    @can('plans.delete')
                    <button type="button" title="Delete" class="plan-tool is-fail"
                        @click="deleteUrl = @js(route('admin.plans.destroy', $plan)); deleteName = @js($plan->title); deleteModalOpen = true">
                        <x-lucide-trash-2 class="w-4 h-4" />
                    </button>
                    @endcan
                </span>
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
