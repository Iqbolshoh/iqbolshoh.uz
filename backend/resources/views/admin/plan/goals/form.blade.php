@extends('layouts.dashboard')

@php
    use App\Enums\GoalStatus;
    use App\Enums\Priority;

    $isEdit = $method === 'PUT';
    $palette = ['#0EA5E9', '#8B5CF6', '#F59E0B', '#22C55E', '#EC4899', '#EF4444', '#14B8A6', '#6366F1'];
@endphp

@section('title', $isEdit ? 'Edit goal' : 'New goal')
@section('breadcrumb', 'Plan')
@section('header_title', $isEdit ? 'Edit goal' : 'New goal')

@section('content')
<div class="max-w-3xl mx-auto">

    <a href="{{ route('admin.goals.index') }}"
        class="inline-flex items-center gap-1.5 text-sm font-medium text-[var(--text-muted)] hover:text-white transition-colors mb-6">
        <x-lucide-arrow-left class="w-4 h-4" />
        Back to goals
    </a>

    <form action="{{ $action }}" method="POST" novalidate>
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="card p-6 sm:p-8 space-y-6">

            <div class="pb-6 border-b border-[var(--border-subtle)] flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-white tracking-tight">{{ $isEdit ? 'Goal details' : 'New goal' }}</h2>
                    <p class="text-sm text-[var(--text-muted)] mt-1">One month, one outcome worth measuring.</p>
                </div>
                <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-[var(--accent-soft)] border border-[var(--accent-border)] flex-shrink-0">
                    <x-lucide-target class="w-5 h-5 text-[var(--accent-hover)]" />
                </div>
            </div>

            <div>
                <label for="title" class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">
                    Title <span class="text-[var(--accent)]">*</span>
                </label>
                <input type="text" id="title" name="title" value="{{ old('title', $goal->title) }}"
                    class="input @error('title') border-[var(--accent)] @enderror" placeholder="English">
                @error('title')
                <p class="text-[var(--accent)] text-xs mt-2">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">Description</label>
                <textarea id="description" name="description" rows="3" class="input"
                    placeholder="What this month is actually for.">{{ old('description', $goal->description) }}</textarea>
            </div>

            <div class="grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="month" class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">
                        Month <span class="text-[var(--accent)]">*</span>
                    </label>
                    <input type="date" id="month" name="month"
                        value="{{ old('month', optional($goal->month)->toDateString()) }}" class="input">
                    <p class="text-xs text-[var(--text-muted)] mt-2">Any day in the month; it is stored as the 1st.</p>
                </div>

                <div>
                    <label for="target" class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">Target</label>
                    <input type="text" id="target" name="target" value="{{ old('target', $goal->target) }}"
                        class="input" placeholder="30 hours">
                </div>

                <div>
                    <label for="priority" class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">Priority</label>
                    <select id="priority" name="priority" class="input cursor-pointer">
                        @foreach(Priority::cases() as $case)
                        <option value="{{ $case->value }}" @selected(old('priority', $goal->priority?->value ?? 'medium') === $case->value)>{{ $case->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">Status</label>
                    <select id="status" name="status" class="input cursor-pointer">
                        @foreach(GoalStatus::cases() as $case)
                        <option value="{{ $case->value }}" @selected(old('status', $goal->status?->value ?? 'active') === $case->value)>{{ $case->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div x-data="{ color: @js(old('color', $goal->color ?? $palette[0])) }">
                <label class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">Colour</label>
                <div class="flex flex-wrap gap-2">
                    @foreach($palette as $swatch)
                    <button type="button" @click="color = @js($swatch)"
                        class="w-9 h-9 rounded-xl border-2 transition-transform cursor-pointer"
                        :class="color === @js($swatch) ? 'scale-110' : 'border-transparent'"
                        :style="color === @js($swatch) ? 'border-color: {{ $swatch }};' : ''"
                        style="background: {{ $swatch }}33;">
                        <span class="block w-full h-full rounded-lg" style="background: {{ $swatch }};"></span>
                    </button>
                    @endforeach
                </div>
                <input type="hidden" name="color" :value="color">
            </div>

            <div>
                <label for="sort_order" class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">Sort order</label>
                <input type="number" id="sort_order" name="sort_order" min="0"
                    value="{{ old('sort_order', $goal->sort_order ?? 0) }}" class="input max-w-[10rem]">
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-[var(--border-subtle)]">
                <a href="{{ route('admin.goals.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">
                    <x-lucide-save class="w-4 h-4" />
                    Save
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
