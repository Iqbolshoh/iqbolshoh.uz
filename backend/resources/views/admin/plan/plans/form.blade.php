@extends('layouts.dashboard')

@php
    use App\Enums\PlanStatus;
    use App\Enums\Priority;
    use Carbon\CarbonInterface;
    use Illuminate\Support\Str;

    $isEdit = $method === 'PUT';
@endphp

@section('title', $isEdit ? 'Edit plan' : 'New plan')
@section('breadcrumb', 'Plan')
@section('header_title', $isEdit ? 'Edit plan' : 'New plan')

@section('content')
<div class="max-w-3xl mx-auto">

    <a href="{{ route('admin.plans.index') }}"
        class="inline-flex items-center gap-1.5 text-sm font-medium text-[var(--text-muted)] hover:text-white transition-colors mb-6">
        <x-lucide-arrow-left class="w-4 h-4" />
        Back to plans
    </a>

    <form action="{{ $action }}" method="POST" novalidate>
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="card p-6 sm:p-8 space-y-6">

            <div class="pb-6 border-b border-[var(--border-subtle)] flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-white tracking-tight">{{ $isEdit ? 'Plan details' : 'New plan' }}</h2>
                    <p class="text-sm text-[var(--text-muted)] mt-1">One thing, one time, one day.</p>
                </div>
                <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-[var(--accent-soft)] border border-[var(--accent-border)] flex-shrink-0">
                    <x-lucide-list-checks class="w-5 h-5 text-[var(--accent-hover)]" />
                </div>
            </div>

            <div>
                <label for="title" class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">
                    Title <span class="text-[var(--accent)]">*</span>
                </label>
                <input type="text" id="title" name="title" value="{{ old('title', $plan->title) }}"
                    class="input @error('title') is-invalid @enderror" placeholder="Laravel API">
                @error('title')
                <p class="text-[var(--accent)] text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">Description</label>
                <textarea id="description" name="description" rows="3" class="input">{{ old('description', $plan->description) }}</textarea>
            </div>

            <div>
                <label for="goal_id" class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">Goal</label>
                <select id="goal_id" name="goal_id" class="input cursor-pointer">
                    <option value="">— standalone plan —</option>
                    @foreach($goals as $id => $label)
                    <option value="{{ $id }}" @selected((string) old('goal_id', $plan->goal_id) === (string) $id)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid gap-5 sm:grid-cols-3">
                <div>
                    <label for="date" class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">
                        Date <span class="text-[var(--accent)]">*</span>
                    </label>
                    <input type="date" id="date" name="date"
                        value="{{ old('date', $plan->date instanceof CarbonInterface ? $plan->date->toDateString() : $plan->date) }}"
                        class="input @error('date') is-invalid @enderror">
                    @error('date')<p class="text-[var(--accent)] text-xs mt-2">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="start_time" class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">
                        Start <span class="text-[var(--accent)]">*</span>
                    </label>
                    <input type="time" id="start_time" name="start_time"
                        value="{{ old('start_time', Str::substr((string) $plan->start_time, 0, 5)) }}"
                        class="input @error('start_time') is-invalid @enderror">
                    @error('start_time')<p class="text-[var(--accent)] text-xs mt-2">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="planned_minutes" class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">
                        Minutes <span class="text-[var(--accent)]">*</span>
                    </label>
                    <input type="number" id="planned_minutes" name="planned_minutes" min="5" max="1440"
                        value="{{ old('planned_minutes', $plan->planned_minutes ?? 60) }}" class="input">
                </div>
            </div>

            <div class="grid gap-5 sm:grid-cols-3">
                <div>
                    <label for="priority" class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">Priority</label>
                    <select id="priority" name="priority" class="input cursor-pointer">
                        @foreach(Priority::cases() as $case)
                        <option value="{{ $case->value }}" @selected(old('priority', $plan->priority?->value ?? 'medium') === $case->value)>{{ $case->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">Status</label>
                    <select id="status" name="status" class="input cursor-pointer">
                        @foreach(PlanStatus::cases() as $case)
                        <option value="{{ $case->value }}" @selected(old('status', $plan->status?->value ?? 'pending') === $case->value)>{{ $case->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="actual_minutes" class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">Actual minutes</label>
                    <input type="number" id="actual_minutes" name="actual_minutes" min="0" max="1440"
                        value="{{ old('actual_minutes', $plan->actual_minutes) }}" class="input">
                    <p class="text-xs text-[var(--text-muted)] mt-2">Left blank, it is measured on completion.</p>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-[var(--border-subtle)]">
                <a href="{{ route('admin.plans.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">
                    <x-lucide-save class="w-4 h-4" />
                    Save
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
