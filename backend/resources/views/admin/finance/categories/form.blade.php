@extends('layouts.dashboard')

@php
    $isEdit = $method === 'PUT';
    $palette = ['#F97316', '#0EA5E9', '#8B5CF6', '#22C55E', '#EF4444', '#EC4899', '#F59E0B', '#14B8A6'];
@endphp

@section('title', $isEdit ? 'Edit category' : 'New category')
@section('breadcrumb', 'Finance')
@section('header_title', $isEdit ? 'Edit category' : 'New category')

@section('content')
<div class="max-w-3xl mx-auto">

    <a href="{{ route('admin.finance-categories.index') }}"
        class="inline-flex items-center gap-1.5 text-sm font-medium text-[var(--text-muted)] hover:text-white transition-colors mb-6">
        <x-lucide-arrow-left class="w-4 h-4" />
        Back to categories
    </a>

    <form action="{{ $action }}" method="POST" novalidate
        x-data="{ color: @js(old('color', $category->color ?? '#0EA5E9')) }">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="card p-6 sm:p-8 space-y-6">

            <div class="pb-6 border-b border-[var(--border-subtle)] flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-white tracking-tight">{{ $isEdit ? 'Category' : 'New category' }}</h2>
                    <p class="text-sm text-[var(--text-muted)] mt-1">
                        @if($isEdit && $category->key)
                            A starter category. Renaming it here also renames it in the bot.
                        @else
                            Its name is what the bot shows, in every language.
                        @endif
                    </p>
                </div>
                <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-[var(--accent-soft)] border border-[var(--accent-border)] flex-shrink-0">
                    <x-lucide-tags class="w-5 h-5 text-[var(--accent-hover)]" />
                </div>
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="kind" class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">
                        Direction <span class="text-[var(--accent)]">*</span>
                    </label>
                    <select id="kind" name="kind" class="input cursor-pointer @error('kind') is-invalid @enderror">
                        @foreach($kinds as $kindOption)
                        <option value="{{ $kindOption->value }}"
                            @selected(old('kind', $category->kind?->value) === $kindOption->value)>
                            {{ $kindOption->label() }}
                        </option>
                        @endforeach
                    </select>
                    @error('kind')<p class="text-[var(--accent)] text-xs mt-2">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="name" class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">
                        Name <span class="text-[var(--accent)]">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $category->name) }}"
                        class="input @error('name') is-invalid @enderror" placeholder="Food">
                    @error('name')<p class="text-[var(--accent)] text-xs mt-2">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="icon" class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">Icon</label>
                    <input type="text" id="icon" name="icon" value="{{ old('icon', $category->icon) }}"
                        class="input @error('icon') is-invalid @enderror" placeholder="🍽" maxlength="16">
                    @error('icon')<p class="text-[var(--accent)] text-xs mt-2">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="monthly_limit" class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">
                        Monthly ceiling, so'm
                    </label>
                    <input type="text" inputmode="numeric" id="monthly_limit" name="monthly_limit"
                        value="{{ old('monthly_limit', $category->monthly_limit) }}"
                        class="input font-mono @error('monthly_limit') is-invalid @enderror" placeholder="Leave empty for none">
                    @error('monthly_limit')<p class="text-[var(--accent)] text-xs mt-2">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label for="keywords" class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">
                    Words the bot recognises
                </label>
                <textarea id="keywords" name="keywords" rows="3"
                    class="input @error('keywords') is-invalid @enderror"
                    placeholder="ovqat, obed, еда, food">{{ old('keywords', $category->keywords) }}</textarea>
                <p class="text-xs text-[var(--text-muted)] mt-2">
                    Comma separated, any language. The bot adds a word here by itself whenever you correct one of its guesses.
                </p>
                @error('keywords')<p class="text-[var(--accent)] text-xs mt-2">{{ $message }}</p>@enderror
            </div>

            <div>
                <span class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">Colour</span>
                <div class="flex flex-wrap gap-2">
                    @foreach($palette as $swatch)
                    <button type="button" @click="color = @js($swatch)"
                        class="w-9 h-9 rounded-xl p-1 transition-transform hover:scale-110"
                        :class="color === @js($swatch) ? 'ring-2 ring-white/70' : ''"
                        style="background: {{ $swatch }}33;">
                        <span class="block w-full h-full rounded-lg" style="background: {{ $swatch }};"></span>
                    </button>
                    @endforeach
                </div>
                <input type="hidden" name="color" :value="color">
            </div>

            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label for="sort_order" class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">Sort order</label>
                    <input type="number" id="sort_order" name="sort_order" min="0"
                        value="{{ old('sort_order', $category->sort_order ?? 0) }}" class="input max-w-[10rem]">
                </div>

                <div class="flex items-end">
                    <label class="inline-flex items-center gap-3 cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                            @checked(old('is_active', $category->is_active ?? true))>
                        <span class="relative w-11 h-6 rounded-full bg-white/10 peer-checked:bg-[var(--accent)] transition-colors after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:w-5 after:h-5 after:rounded-full after:bg-white after:transition-transform peer-checked:after:translate-x-5"></span>
                        <span class="text-sm font-semibold text-[var(--text-secondary)]">Active</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-6 border-t border-[var(--border-subtle)]">
                <a href="{{ route('admin.finance-categories.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">
                    <x-lucide-save class="w-4 h-4" />
                    Save
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
