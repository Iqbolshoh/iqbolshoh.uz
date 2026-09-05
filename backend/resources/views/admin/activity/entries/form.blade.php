@extends('layouts.dashboard')

@php
    $isEdit = $method === 'PUT';
@endphp

@section('title', $isEdit ? 'Edit time entry' : 'New time entry')
@section('breadcrumb', 'Time')
@section('header_title', $isEdit ? 'Edit time entry' : 'New time entry')

@section('content')
<div class="max-w-2xl mx-auto">

    <a href="{{ route('admin.activities-entries.index') }}"
        class="inline-flex items-center gap-1.5 text-sm font-medium text-[var(--text-muted)] hover:text-white transition-colors mb-6">
        <x-lucide-arrow-left class="w-4 h-4" />
        Back to the time log
    </a>

    <form action="{{ $action }}" method="POST" novalidate>
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="card p-6 sm:p-8 space-y-6">

            <div class="pb-6 border-b border-[var(--border-subtle)] flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-white tracking-tight">
                        {{ $isEdit ? 'Time entry' : 'New time entry' }}
                    </h2>
                    <p class="text-sm text-[var(--text-muted)] mt-1">
                        A length and a day. There is no start and end time on purpose — these are written down after
                        the fact, and an invented start would only raise questions the data cannot answer.
                    </p>
                </div>
                <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-[var(--accent-soft)] border border-[var(--accent-border)] flex-shrink-0">
                    <x-lucide-hourglass class="w-5 h-5 text-[var(--accent-hover)]" />
                </div>
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="category_id" class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">Activity</label>
                    <select id="category_id" name="category_id" class="input cursor-pointer @error('category_id') is-invalid @enderror">
                        <option value="">Uncategorised</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}"
                            @selected((string) old('category_id', $entry->category_id) === (string) $category->id)>
                            {{ $category->label() }}
                        </option>
                        @endforeach
                    </select>
                    @error('category_id')<p class="text-[var(--accent)] text-xs mt-2">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="minutes" class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">
                        Minutes <span class="text-[var(--accent)]">*</span>
                    </label>
                    <input type="text" inputmode="numeric" id="minutes" name="minutes"
                        value="{{ old('minutes', $entry->minutes) }}"
                        class="input font-mono @error('minutes') is-invalid @enderror" placeholder="480">
                    <p class="text-xs text-[var(--text-muted)] mt-2">480 is eight hours. A day is the ceiling.</p>
                    @error('minutes')<p class="text-[var(--accent)] text-xs mt-2">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="date" class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">
                        Date <span class="text-[var(--accent)]">*</span>
                    </label>
                    <input type="date" id="date" name="date"
                        value="{{ old('date', $entry->date instanceof \Carbon\CarbonInterface ? $entry->date->toDateString() : $entry->date) }}"
                        class="input @error('date') is-invalid @enderror">
                    @error('date')<p class="text-[var(--accent)] text-xs mt-2">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label for="note" class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">Note</label>
                <input type="text" id="note" name="note" value="{{ old('note', $entry->note) }}"
                    class="input @error('note') is-invalid @enderror" placeholder="What exactly, if it matters">
                @error('note')<p class="text-[var(--accent)] text-xs mt-2">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-[var(--border-subtle)]">
                <a href="{{ route('admin.activities-entries.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">
                    <x-lucide-save class="w-4 h-4" />
                    Save
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
