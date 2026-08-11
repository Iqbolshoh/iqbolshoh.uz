{{--
    Create/edit form shared by every content section, rendered from the
    controller's field specification.
--}}
@extends('layouts.dashboard')

@php
    use App\Support\SiteContent;
    use App\Support\SiteIcons;

    $isEdit  = $method === 'PUT';
    $locales = SiteContent::LOCALES;
    $primary = array_key_first($locales);

    // `old()` wins after a validation error, otherwise the stored value.
    $valueOf = function (array $field) use ($item) {
        $stored = $item->{$field['name']} ?? null;

        if (in_array($field['type'], ['trans', 'trans_list'])) {
            $stored = (array) $stored;

            if ($field['type'] === 'trans_list') {
                $stored = array_map(fn($lines) => implode("\n", (array) $lines), $stored);
            }

            return array_replace($stored, (array) old($field['name'], []));
        }

        if ($field['type'] === 'list') {
            return old($field['name'], implode("\n", (array) $stored));
        }

        return old($field['name'], $stored);
    };
@endphp

@section('title', $isEdit ? $labels['singular'] . ' — tahrirlash' : 'Yangi ' . mb_strtolower($labels['singular']))
@section('breadcrumb', $labels['plural'])
@section('header_title', $isEdit ? $labels['singular'] . 'ni tahrirlash' : 'Yangi ' . mb_strtolower($labels['singular']))

{{-- Plain CSS, like the layout's own components: these forms get edited
     without re-running the asset build, and an unscanned utility class simply
     is not in the compiled stylesheet. --}}
@push('styles')
<style>
    .file-input {
        display: block;
        width: 100%;
        font-size: 0.875rem;
        color: var(--text-secondary);
        cursor: pointer;
    }

    .file-input::file-selector-button {
        margin-right: 1rem;
        padding: 0.5rem 1rem;
        border: 0;
        border-radius: var(--radius-md);
        font-size: 0.875rem;
        font-weight: 600;
        color: #fff;
        background: linear-gradient(135deg, var(--accent-hover), var(--accent-alt));
        cursor: pointer;
    }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto">

    <a href="{{ route('admin.' . $key . '.index') }}"
        class="inline-flex items-center gap-1.5 text-sm font-medium text-[var(--text-muted)] hover:text-white transition-colors mb-6">
        <x-lucide-arrow-left class="w-4 h-4" />
        {{ $labels['plural'] }}ga qaytish
    </a>

    <form action="{{ $action }}" method="POST" enctype="multipart/form-data" novalidate
        x-data="{ locale: '{{ $primary }}' }">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="card p-6 sm:p-8">

            <div class="mb-8 pb-6 border-b border-[var(--border-subtle)] flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <h2 class="text-xl font-bold text-white tracking-tight">
                        {{ $isEdit ? $labels['singular'] . ' ma\'lumotlari' : 'Yangi ' . mb_strtolower($labels['singular']) }}
                    </h2>
                    @if(!empty($labels['hint']))
                    <p class="text-sm text-[var(--text-muted)] mt-1">{{ $labels['hint'] }}</p>
                    @endif
                </div>
                <div class="w-11 h-11 rounded-xl flex items-center justify-center bg-[var(--accent-soft)] border border-[var(--accent-border)] flex-shrink-0">
                    <x-dynamic-component :component="'lucide-' . $labels['icon']" class="w-5 h-5 text-[var(--accent-hover)]" />
                </div>
            </div>

            {{-- Language switcher: only rendered when the section has translatable fields --}}
            @php $hasTranslations = collect($fields)->contains(fn($field) => in_array($field['type'], ['trans', 'trans_list'])); @endphp
            @if($hasTranslations)
            <div class="flex items-center gap-1.5 p-1 mb-7 rounded-[var(--radius-md)] bg-[var(--bg-surface)] border border-[var(--border-subtle)] w-fit flex-wrap">
                @foreach($locales as $code => $name)
                <button type="button" @click="locale = '{{ $code }}'"
                    class="px-3.5 py-1.5 rounded-lg text-sm font-semibold transition-colors cursor-pointer"
                    :class="locale === '{{ $code }}'
                        ? 'text-white shadow-md'
                        : 'text-[var(--text-muted)] hover:text-[var(--text-secondary)]'"
                    :style="locale === '{{ $code }}' ? 'background: linear-gradient(135deg, var(--accent-hover), var(--accent-alt));' : ''">
                    {{ $name }}
                    @if($code === $primary)
                    <span class="text-[0.6rem] opacity-70">*</span>
                    @endif
                </button>
                @endforeach
            </div>
            <p class="text-xs text-[var(--text-muted)] -mt-5 mb-7">
                <span class="text-[var(--accent-hover)]">*</span> Asosiy til majburiy. Bo'sh qoldirilgan tillar shu tildan to'ldiriladi.
            </p>
            @endif

            <div class="space-y-6">
                @foreach($fields as $field)
                @php
                    $name     = $field['name'];
                    $value    = $valueOf($field);
                    $required = $field['required'] ?? false;
                @endphp

                <div>
                    @if($field['type'] !== 'bool')
                    <label class="block text-sm font-semibold text-[var(--text-secondary)] mb-2">
                        {{ $field['label'] }}
                        @if($required)<span class="text-[var(--accent)]">*</span>@endif
                    </label>
                    @endif

                    @switch($field['type'])

                        @case('trans')
                        @case('trans_list')
                            @foreach($locales as $code => $localeName)
                            {{-- Inline display:none, not x-cloak: Alpine loads deferred and the
                                 layout has no [x-cloak] rule, so every language would flash first. --}}
                            <div x-show="locale === '{{ $code }}'" @if($code !== $primary) style="display: none;" @endif>
                                @if(($field['type'] === 'trans_list') || !empty($field['textarea']))
                                <textarea name="{{ $name }}[{{ $code }}]" rows="{{ $field['rows'] ?? 3 }}"
                                    class="input @error($name . '.' . $code) border-[var(--accent)] @enderror"
                                    placeholder="{{ $field['label'] }} — {{ $localeName }}">{{ $value[$code] ?? '' }}</textarea>
                                @else
                                <input type="text" name="{{ $name }}[{{ $code }}]" value="{{ $value[$code] ?? '' }}"
                                    class="input @error($name . '.' . $code) border-[var(--accent)] @enderror"
                                    placeholder="{{ $field['label'] }} — {{ $localeName }}">
                                @endif

                                @error($name . '.' . $code)
                                <p class="text-[var(--accent)] text-xs mt-2 flex items-center gap-1">
                                    <x-lucide-alert-circle class="w-3.5 h-3.5 flex-shrink-0" />{{ $message }}
                                </p>
                                @enderror
                            </div>
                            @endforeach
                            @break

                        @case('image')
                            <div x-data="{ preview: @js($value), remove: false }" class="flex flex-col sm:flex-row items-start gap-5">
                                <div class="w-40 h-28 rounded-[var(--radius-md)] border border-[var(--border-subtle)] overflow-hidden flex items-center justify-center flex-shrink-0"
                                    style="background: rgba(255,255,255,0.03);">
                                    <template x-if="preview && !remove">
                                        <img :src="preview" alt="" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!preview || remove">
                                        <x-lucide-image class="w-6 h-6 text-[var(--text-muted)]" />
                                    </template>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <input type="file" name="{{ $name }}_file" accept="image/*"
                                        @change="remove = false; preview = $event.target.files.length ? URL.createObjectURL($event.target.files[0]) : preview"
                                        class="file-input">

                                    @if(!empty($field['help']))
                                    <p class="text-xs text-[var(--text-muted)] mt-2">{{ $field['help'] }}</p>
                                    @endif

                                    <template x-if="preview">
                                        <label class="inline-flex items-center gap-2 mt-3 text-sm text-[var(--text-secondary)]">
                                            <input type="checkbox" name="{{ $name }}_remove" value="1" x-model="remove"
                                                class="w-4 h-4 rounded accent-[var(--accent)] cursor-pointer">
                                            Rasmni o'chirish
                                        </label>
                                    </template>

                                    @error($name . '_file')
                                    <p class="text-[var(--accent)] text-xs mt-2 flex items-center gap-1">
                                        <x-lucide-alert-circle class="w-3.5 h-3.5 flex-shrink-0" />{{ $message }}
                                    </p>
                                    @enderror
                                </div>
                            </div>
                            @break

                        @case('icon')
                            <div x-data="{ icon: @js($value ?: 'Code') }">
                                <div class="flex flex-wrap gap-2">
                                    @foreach(SiteIcons::NAMES as $iconName)
                                    <button type="button" @click="icon = @js($iconName)" title="{{ $iconName }}"
                                        class="w-11 h-11 rounded-xl flex items-center justify-center border transition-colors cursor-pointer"
                                        :class="icon === @js($iconName)
                                            ? 'bg-[var(--accent-soft)] border-[var(--accent-border)] text-[var(--accent-hover)]'
                                            : 'border-[var(--border-subtle)] text-[var(--text-muted)] hover:text-[var(--text-secondary)] hover:border-[var(--border-strong)]'">
                                        <x-dynamic-component :component="SiteIcons::component($iconName)" class="w-4 h-4" />
                                    </button>
                                    @endforeach
                                </div>
                                <input type="hidden" name="{{ $name }}" :value="icon">
                                <p class="text-xs text-[var(--text-muted)] mt-2">Tanlangan: <span class="font-mono text-[var(--text-secondary)]" x-text="icon"></span></p>
                            </div>
                            @break

                        @case('select')
                            <select name="{{ $name }}" class="input cursor-pointer @error($name) border-[var(--accent)] @enderror">
                                @unless($required)
                                <option value="">— tanlanmagan —</option>
                                @endunless
                                @foreach($field['options'] as $optionValue => $optionLabel)
                                <option value="{{ $optionValue }}" @selected((string) $value === (string) $optionValue)>{{ $optionLabel }}</option>
                                @endforeach
                            </select>
                            @break

                        @case('bool')
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="hidden" name="{{ $name }}" value="0">
                                <input type="checkbox" name="{{ $name }}" value="1" @checked($value)
                                    class="w-5 h-5 mt-0.5 rounded accent-[var(--accent)] cursor-pointer flex-shrink-0">
                                <span>
                                    <span class="block text-sm font-semibold text-white">{{ $field['label'] }}</span>
                                    @if(!empty($field['help']))
                                    <span class="block text-xs text-[var(--text-muted)] mt-0.5">{{ $field['help'] }}</span>
                                    @endif
                                </span>
                            </label>
                            @break

                        @case('list')
                            <textarea name="{{ $name }}" rows="{{ $field['rows'] ?? 3 }}"
                                class="input @error($name) border-[var(--accent)] @enderror"
                                placeholder="Laravel&#10;React&#10;MySQL">{{ $value }}</textarea>
                            @break

                        @case('number')
                            <input type="number" name="{{ $name }}" value="{{ $value ?? 0 }}"
                                @isset($field['min']) min="{{ $field['min'] }}" @endisset
                                @isset($field['max']) max="{{ $field['max'] }}" @endisset
                                class="input max-w-[10rem] @error($name) border-[var(--accent)] @enderror">
                            @break

                        @case('textarea')
                            <textarea name="{{ $name }}" rows="{{ $field['rows'] ?? 3 }}"
                                class="input @error($name) border-[var(--accent)] @enderror">{{ $value }}</textarea>
                            @break

                        @default
                            <input type="{{ $field['type'] === 'url' ? 'url' : ($field['type'] === 'date' ? 'date' : 'text') }}"
                                name="{{ $name }}" value="{{ $value }}"
                                class="input @error($name) border-[var(--accent)] @enderror"
                                placeholder="{{ $field['placeholder'] ?? '' }}">
                    @endswitch

                    @if(!empty($field['help']) && !in_array($field['type'], ['bool', 'image']))
                    <p class="text-xs text-[var(--text-muted)] mt-2">{{ $field['help'] }}</p>
                    @endif

                    @error($name)
                    <p class="text-[var(--accent)] text-xs mt-2 flex items-center gap-1">
                        <x-lucide-alert-circle class="w-3.5 h-3.5 flex-shrink-0" />{{ $message }}
                    </p>
                    @enderror
                </div>
                @endforeach
            </div>

            <div class="flex items-center justify-end gap-3 pt-7 mt-8 border-t border-[var(--border-subtle)]">
                <a href="{{ route('admin.' . $key . '.index') }}" class="btn-secondary">Bekor qilish</a>
                <button type="submit" class="btn-primary">
                    <x-lucide-save class="w-4 h-4" />
                    Saqlash
                </button>
            </div>
        </div>
    </form>
</div>
@endsection
