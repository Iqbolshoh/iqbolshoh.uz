@extends('layouts.dashboard')

@section('title', 'Budget & alerts')
@section('breadcrumb', 'Finance')
@section('header_title', 'Budget & alerts')

@section('content')
<div class="max-w-3xl mx-auto">

    <form action="{{ route('admin.finance-settings.update') }}" method="POST" novalidate>
        @csrf
        @method('PUT')

        <div class="card p-6 sm:p-8 space-y-6">

            <div class="pb-6 border-b border-[var(--border-subtle)] flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-white tracking-tight">Your rules for your money</h2>
                    <p class="text-sm text-[var(--text-muted)] mt-1">The bot enforces these, in whichever language you last spoke to it.</p>
                </div>
                <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-[var(--accent-soft)] border border-[var(--accent-border)] flex-shrink-0">
                    <x-lucide-sliders-horizontal class="w-5 h-5 text-[var(--accent-hover)]" />
                </div>
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="monthly_budget" class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">
                        Monthly ceiling, so'm
                    </label>
                    <input type="text" inputmode="numeric" id="monthly_budget" name="monthly_budget"
                        value="{{ old('monthly_budget', $settings->monthly_budget) }}"
                        class="input font-mono @error('monthly_budget') is-invalid @enderror"
                        placeholder="Leave empty for none">
                    <p class="text-xs text-[var(--text-muted)] mt-2">Across every expense category.</p>
                    @error('monthly_budget')<p class="text-[var(--accent)] text-xs mt-2">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="warn_at_percent" class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">
                        Warn at <span class="text-[var(--accent)]">*</span>
                    </label>
                    <div class="relative">
                        <input type="number" id="warn_at_percent" name="warn_at_percent" min="1" max="200"
                            value="{{ old('warn_at_percent', $settings->warn_at_percent) }}"
                            class="input font-mono pr-10 @error('warn_at_percent') is-invalid @enderror">
                        <span class="absolute right-4 top-1/2 -translate-y-1/2 text-sm text-[var(--text-muted)] pointer-events-none">%</span>
                    </div>
                    <p class="text-xs text-[var(--text-muted)] mt-2">
                        The warning fires once, on the entry that crosses the line — not on every one after it.
                    </p>
                    @error('warn_at_percent')<p class="text-[var(--accent)] text-xs mt-2">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="pt-6 border-t border-[var(--border-subtle)] space-y-5">
                <h3 class="font-bold text-white">What the bot sends</h3>

                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-white">Evening prompt</p>
                        <p class="text-xs text-[var(--text-muted)] mt-1">
                            Asks what the day cost — but only on a day with nothing logged yet.
                        </p>
                    </div>
                    <label class="inline-flex items-center gap-3 cursor-pointer flex-shrink-0">
                        <input type="checkbox" name="daily_prompt" value="1" class="sr-only peer"
                            @checked(old('daily_prompt', $settings->daily_prompt))>
                        <span class="relative w-11 h-6 rounded-full bg-white/10 peer-checked:bg-[var(--accent)] transition-colors after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:w-5 after:h-5 after:rounded-full after:bg-white after:transition-transform peer-checked:after:translate-x-5"></span>
                    </label>
                </div>

                <div>
                    <label for="prompt_time" class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">Prompt time</label>
                    <input type="time" id="prompt_time" name="prompt_time"
                        value="{{ old('prompt_time', substr((string) $settings->prompt_time, 0, 5)) }}"
                        class="input max-w-[10rem] @error('prompt_time') is-invalid @enderror">
                    <p class="text-xs text-[var(--text-muted)] mt-2">On your own clock ({{ auth()->user()->timezone }}).</p>
                    @error('prompt_time')<p class="text-[var(--accent)] text-xs mt-2">{{ $message }}</p>@enderror
                </div>

                @foreach([
                    ['weekly_report', 'Weekly summary', 'Every Monday morning, last week in one message.'],
                    ['monthly_report', 'Monthly report', 'On the 1st: the month that closed, against its budget.'],
                ] as [$field, $label, $hint])
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-white">{{ $label }}</p>
                        <p class="text-xs text-[var(--text-muted)] mt-1">{{ $hint }}</p>
                    </div>
                    <label class="inline-flex items-center gap-3 cursor-pointer flex-shrink-0">
                        <input type="checkbox" name="{{ $field }}" value="1" class="sr-only peer"
                            @checked(old($field, $settings->{$field}))>
                        <span class="relative w-11 h-6 rounded-full bg-white/10 peer-checked:bg-[var(--accent)] transition-colors after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:w-5 after:h-5 after:rounded-full after:bg-white after:transition-transform peer-checked:after:translate-x-5"></span>
                    </label>
                </div>
                @endforeach
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-[var(--border-subtle)]">
                <button type="submit" class="btn-primary">
                    <x-lucide-save class="w-4 h-4" />
                    Save
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
