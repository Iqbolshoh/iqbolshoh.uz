@extends('layouts.dashboard')

@section('title', 'Goals')
@section('breadcrumb', 'Plan')
@section('header_title', 'Goals')

@section('content')
<div x-data="{ deleteModalOpen: false, deleteUrl: '', deleteName: '' }">

    @include('admin.plan.partials.delete-modal', ['what' => 'goal'])

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div class="min-w-0">
            <p class="text-sm text-[var(--text-muted)]">What each month is for. Daily plans hang off these.</p>
        </div>

        <div class="flex items-center gap-3 flex-wrap">
            <form method="GET" class="flex items-center gap-2">
                <select name="month" onchange="this.form.submit()" class="input cursor-pointer !w-auto !py-2 text-sm">
                    @foreach($months as $value => $label)
                    <option value="{{ $value }}" @selected($month->toDateString() === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </form>

            @can('goals.create')
            <a href="{{ route('admin.goals.create') }}" class="btn-primary">
                <x-lucide-plus class="w-4 h-4" />
                New goal
            </a>
            @endcan
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @forelse($goals as $goal)
        @php $progress = $goal->progress(); @endphp
        <div class="card p-5 flex flex-col gap-4">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background: {{ $goal->color ?? '#8B95A5' }};"></span>
                        <h3 class="font-bold text-white truncate">{{ $goal->title }}</h3>
                    </div>
                    @if($goal->target)
                    <p class="text-xs text-[var(--text-muted)] mt-1">Target: {{ $goal->target }}</p>
                    @endif
                </div>

                <div class="flex items-center gap-1 flex-shrink-0">
                    @can('goals.edit')
                    <a href="{{ route('admin.goals.edit', $goal) }}" title="Edit"
                        class="p-2 rounded-lg text-[var(--text-muted)] hover:text-[var(--accent-hover)] hover:bg-[var(--accent-soft)] transition-colors">
                        <x-lucide-pencil class="w-4 h-4" />
                    </a>
                    @endcan
                    @can('goals.delete')
                    <button type="button" title="Delete"
                        @click="deleteUrl = @js(route('admin.goals.destroy', $goal)); deleteName = @js($goal->title); deleteModalOpen = true"
                        class="p-2 rounded-lg text-[var(--text-muted)] hover:text-[var(--accent)] hover:bg-[var(--accent-soft)] transition-colors">
                        <x-lucide-trash-2 class="w-4 h-4" />
                    </button>
                    @endcan
                </div>
            </div>

            @if($goal->description)
            <p class="text-sm text-[var(--text-secondary)] line-clamp-2">{{ $goal->description }}</p>
            @endif

            <div>
                <div class="flex items-center justify-between text-xs mb-1.5">
                    <span class="text-[var(--text-muted)]">Progress</span>
                    <span class="font-mono font-bold text-white">{{ $progress }}%</span>
                </div>
                <div class="h-2 rounded-full overflow-hidden" style="background: rgba(255,255,255,0.07);">
                    <div class="h-full rounded-full transition-all" style="width: {{ $progress }}%; background: {{ $goal->color ?? 'var(--accent-hover)' }};"></div>
                </div>
            </div>

            <div class="flex items-center justify-between pt-3 border-t border-[var(--border-subtle)]">
                <div class="flex items-center gap-2">
                    <x-status-badge :status="$goal->status" />
                    <x-status-badge :status="$goal->priority" />
                </div>
                <a href="{{ route('admin.plans.index', ['goal' => $goal->id]) }}"
                    class="text-xs font-semibold text-[var(--accent-hover)] hover:text-[var(--accent-alt)] transition-colors">
                    {{ $goal->plans_count }} plans →
                </a>
            </div>
        </div>
        @empty
        <div class="card px-6 py-16 text-center sm:col-span-2 xl:col-span-3">
            <div class="flex flex-col items-center gap-3">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background: rgba(255,255,255,0.04); border: 1px solid var(--border-subtle);">
                    <x-lucide-target class="w-6 h-6 text-[var(--text-muted)]" />
                </div>
                <p class="text-sm font-semibold text-[var(--text-secondary)]">No goals for {{ $month->format('F Y') }}</p>
                @can('goals.create')
                <a href="{{ route('admin.goals.create') }}" class="btn-primary !text-xs !py-2 !px-4 mt-1">
                    <x-lucide-plus class="w-3.5 h-3.5" />
                    New goal
                </a>
                @endcan
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
