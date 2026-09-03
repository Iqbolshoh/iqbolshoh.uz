@extends('layouts.dashboard')

@php
    $isEdit = $method === 'PUT';
    $currentKind = old('kind', $transaction->kind?->value ?? 'expense');
@endphp

@section('title', $isEdit ? 'Edit transaction' : 'New transaction')
@section('breadcrumb', 'Finance')
@section('header_title', $isEdit ? 'Edit transaction' : 'New transaction')

@section('content')
<div class="max-w-3xl mx-auto">

    <a href="{{ route('admin.transactions.index') }}"
        class="inline-flex items-center gap-1.5 text-sm font-medium text-[var(--text-muted)] hover:text-white transition-colors mb-6">
        <x-lucide-arrow-left class="w-4 h-4" />
        Back to transactions
    </a>

    {{-- `kind` lives in Alpine so the category list can follow it: an expense
         must never be filed under Salary, and the panel should make that
         impossible rather than validate it after the fact. --}}
    <form action="{{ $action }}" method="POST" novalidate
        x-data="{
            kind: @js($currentKind),
            categoryId: @js((string) old('category_id', $transaction->category_id ?? '')),
            all: @js($categories->map(fn ($category) => [
                'id' => (string) $category->id,
                'kind' => $category->kind->value,
                'label' => $category->label(),
            ])->values()),
            get visible() { return this.all.filter(category => category.kind === this.kind) },
        }"
        x-effect="if (categoryId && !visible.some(category => category.id === categoryId)) categoryId = ''">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="card p-6 sm:p-8 space-y-6">

            <div class="pb-6 border-b border-[var(--border-subtle)] flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-white tracking-tight">{{ $isEdit ? 'Transaction' : 'New transaction' }}</h2>
                    <p class="text-sm text-[var(--text-muted)] mt-1">Amounts are whole so'm.</p>
                </div>
                <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-[var(--accent-soft)] border border-[var(--accent-border)] flex-shrink-0">
                    <x-lucide-wallet class="w-5 h-5 text-[var(--accent-hover)]" />
                </div>
            </div>

            <div>
                <span class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">Direction</span>
                <div class="grid grid-cols-2 gap-3">
                    @foreach($kinds as $kindOption)
                    <label class="cursor-pointer">
                        <input type="radio" name="kind" value="{{ $kindOption->value }}" class="sr-only"
                            x-model="kind" @checked($currentKind === $kindOption->value)>
                        <span class="flex items-center justify-center gap-2 px-4 py-3 rounded-[var(--radius-md)] border text-sm font-semibold transition-colors"
                            :class="kind === @js($kindOption->value)
                                ? 'border-transparent text-white'
                                : 'border-[var(--border-subtle)] text-[var(--text-muted)] hover:text-white'"
                            :style="kind === @js($kindOption->value) ? 'background: {{ $kindOption->color() }}22; border-color: {{ $kindOption->color() }};' : ''">
                            {{ $kindOption->label() }}
                        </span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="amount" class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">
                        Amount, so'm <span class="text-[var(--accent)]">*</span>
                    </label>
                    {{-- type=text, not number: a number input only accepts the
                         browser's own decimal separator, and a comma-locale
                         keyboard cannot type into it at all. --}}
                    <input type="text" inputmode="numeric" id="amount" name="amount"
                        value="{{ old('amount', $transaction->amount) }}"
                        class="input font-mono @error('amount') is-invalid @enderror" placeholder="25000">
                    @error('amount')<p class="text-[var(--accent)] text-xs mt-2">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="category_id" class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">Category</label>
                    {{-- Rebuilt from the Alpine list rather than hidden with
                         x-show: a hidden <option> stays selected in several
                         browsers, which is how an expense ends up filed under
                         Salary. Switching direction clears a choice that no
                         longer belongs. --}}
                    <select id="category_id" name="category_id" x-model="categoryId"
                        class="input cursor-pointer @error('category_id') is-invalid @enderror">
                        <option value="">Uncategorised</option>
                        <template x-for="category in visible" :key="category.id">
                            <option :value="category.id" x-text="category.label"></option>
                        </template>
                    </select>
                    @error('category_id')<p class="text-[var(--accent)] text-xs mt-2">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="date" class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">
                        Date <span class="text-[var(--accent)]">*</span>
                    </label>
                    <input type="date" id="date" name="date"
                        value="{{ old('date', $transaction->date?->format('Y-m-d')) }}"
                        class="input @error('date') is-invalid @enderror">
                    @error('date')<p class="text-[var(--accent)] text-xs mt-2">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="time" class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">Time</label>
                    <input type="time" id="time" name="time"
                        value="{{ old('time', substr((string) $transaction->time, 0, 5)) }}"
                        class="input @error('time') is-invalid @enderror">
                    @error('time')<p class="text-[var(--accent)] text-xs mt-2">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <span class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">Method</span>
                <div class="flex flex-wrap gap-2">
                    @foreach($methods as $methodOption)
                    <label class="cursor-pointer">
                        <input type="radio" name="method" value="{{ $methodOption->value }}" class="sr-only peer"
                            @checked(old('method', $transaction->method?->value ?? 'cash') === $methodOption->value)>
                        <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-[var(--radius-md)] border border-[var(--border-subtle)] text-sm text-[var(--text-muted)] transition-colors peer-checked:border-[var(--accent-border)] peer-checked:bg-[var(--accent-soft)] peer-checked:text-white hover:text-white">
                            {{ $methodOption->icon() }} {{ $methodOption->label() }}
                        </span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label for="note" class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">Note</label>
                <input type="text" id="note" name="note" value="{{ old('note', $transaction->note) }}"
                    class="input @error('note') is-invalid @enderror" placeholder="What it was for">
                @error('note')<p class="text-[var(--accent)] text-xs mt-2">{{ $message }}</p>@enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-[var(--border-subtle)]">
                <a href="{{ route('admin.transactions.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">
                    <x-lucide-save class="w-4 h-4" />
                    Save
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
