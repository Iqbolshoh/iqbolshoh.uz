@extends('layouts.dashboard')

@section('title', 'Site settings')
@section('breadcrumb', 'Settings')
@section('header_title', 'Site settings')

@section('content')
@php
    use App\Support\SiteContent;

    $locales = SiteContent::LOCALES;
    $primary = array_key_first($locales);

    $values = ['name' => $name, 'location' => $location];
@endphp

<div class="max-w-4xl mx-auto">
    <form action="{{ route('admin.settings.update') }}" method="POST" novalidate x-data="{ locale: '{{ $primary }}' }">
        @csrf
        @method('PUT')

        {{-- Identity --}}
        <div class="card p-6 sm:p-8 mb-6">
            <div class="mb-7 pb-6 border-b border-[var(--border-subtle)] flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-white tracking-tight">Personal details</h2>
                    <p class="text-sm text-[var(--text-muted)] mt-1">Used across every page of the site</p>
                </div>
                <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-[var(--accent-soft)] border border-[var(--accent-border)] flex-shrink-0">
                    <x-lucide-user-round class="w-5 h-5 text-[var(--accent-hover)]" />
                </div>
            </div>

            <div class="flex items-center gap-1.5 p-1 mb-6 rounded-[var(--radius-md)] bg-[var(--bg-surface)] border border-[var(--border-subtle)] w-fit flex-wrap">
                @foreach($locales as $code => $localeName)
                <button type="button" @click="locale = '{{ $code }}'"
                    class="px-3.5 py-1.5 rounded-lg text-sm font-semibold transition-colors cursor-pointer"
                    :class="locale === '{{ $code }}' ? 'text-white shadow-md' : 'text-[var(--text-muted)] hover:text-[var(--text-secondary)]'"
                    :style="locale === '{{ $code }}' ? 'background: linear-gradient(135deg, var(--accent-hover), var(--accent-alt));' : ''">
                    {{ $localeName }}@if($code === $primary)<span class="text-[0.6rem] opacity-70">*</span>@endif
                </button>
                @endforeach
            </div>

            <div class="space-y-6">
                @foreach(['name' => 'To\'liq ism', 'location' => 'Manzil'] as $field => $label)
                <div>
                    <label class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">
                        {{ $label }} <span class="text-[var(--accent)]">*</span>
                    </label>
                    @foreach($locales as $code => $localeName)
                    <div x-show="locale === '{{ $code }}'" @if($code !== $primary) style="display: none;" @endif>
                        <input type="text" name="{{ $field }}[{{ $code }}]"
                            value="{{ old($field . '.' . $code, $values[$field][$code] ?? '') }}"
                            class="input @error($field . '.' . $code) is-invalid @enderror"
                            placeholder="{{ $label }} — {{ $localeName }}">
                        @error($field . '.' . $code)
                        <p class="text-[var(--accent)] text-xs mt-2 flex items-center gap-1">
                            <x-lucide-alert-circle class="w-3.5 h-3.5 flex-shrink-0" />{{ $message }}
                        </p>
                        @enderror
                    </div>
                    @endforeach
                </div>
                @endforeach
            </div>
        </div>

        {{-- Contacts and social links --}}
        <div class="card p-6 sm:p-8">
            <div class="mb-7 pb-6 border-b border-[var(--border-subtle)] flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-white tracking-tight">Contact and social links</h2>
                    <p class="text-sm text-[var(--text-muted)] mt-1">
                        Link is where it points, label is what the visitor reads
                    </p>
                </div>
                <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-[var(--accent-soft)] border border-[var(--accent-border)] flex-shrink-0">
                    <x-lucide-share-2 class="w-5 h-5 text-[var(--accent-hover)]" />
                </div>
            </div>

            <div class="space-y-6">
                @foreach($networks as $network => $meta)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">
                            {{ $meta['label'] }} — havola <span class="text-[var(--accent)]">*</span>
                        </label>
                        <input type="text" name="social[{{ $network }}][link]"
                            value="{{ old("social.{$network}.link", $social[$network]['link'] ?? '') }}"
                            class="input @error("social.{$network}.link") is-invalid @enderror"
                            placeholder="{{ $meta['placeholder'] }}">
                        @error("social.{$network}.link")
                        <p class="text-[var(--accent)] text-xs mt-2 flex items-center gap-1">
                            <x-lucide-alert-circle class="w-3.5 h-3.5 flex-shrink-0" />{{ $message }}
                        </p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">
                            {{ $meta['label'] }} — matn <span class="text-[var(--accent)]">*</span>
                        </label>
                        <input type="text" name="social[{{ $network }}][label]"
                            value="{{ old("social.{$network}.label", $social[$network]['label'] ?? '') }}"
                            class="input @error("social.{$network}.label") is-invalid @enderror"
                            placeholder="{{ $meta['label'] }}">
                        @error("social.{$network}.label")
                        <p class="text-[var(--accent)] text-xs mt-2 flex items-center gap-1">
                            <x-lucide-alert-circle class="w-3.5 h-3.5 flex-shrink-0" />{{ $message }}
                        </p>
                        @enderror
                    </div>
                </div>
                @endforeach
            </div>

            <div class="flex items-center justify-end gap-3 pt-7 mt-8 border-t border-[var(--border-subtle)]">
                <button type="submit" class="btn-primary">
                    <x-lucide-save class="w-4 h-4" />
                    Save settings
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
