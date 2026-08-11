{{--
    Create/edit form shared by every content section, rendered from the
    controller's field specification.
--}}
@extends('layouts.dashboard')

@php
    use App\Support\SiteContent;
    use App\Support\SiteIcons;
    use App\Support\SiteTech;

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

        if ($field['type'] === 'tech') {
            return array_values(old($field['name'], (array) $stored));
        }

        return old($field['name'], $stored);
    };
@endphp

@section('title', $isEdit ? 'Edit ' . mb_strtolower($labels['singular']) : 'New ' . mb_strtolower($labels['singular']))
@section('breadcrumb', $labels['plural'])
@section('header_title', $isEdit ? 'Edit ' . mb_strtolower($labels['singular']) : 'New ' . mb_strtolower($labels['singular']))

@section('content')
<div class="max-w-4xl mx-auto">

    <a href="{{ route('admin.' . $key . '.index') }}"
        class="inline-flex items-center gap-1.5 text-sm font-medium text-[var(--text-muted)] hover:text-white transition-colors mb-6">
        <x-lucide-arrow-left class="w-4 h-4" />
        Back to {{ mb_strtolower($labels['plural']) }}
    </a>

    <form action="{{ $action }}" method="POST" enctype="multipart/form-data" novalidate
        x-data="{ locale: '{{ $primary }}' }">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="card p-6 sm:p-8">

            <div class="mb-8 pb-6 border-b border-[var(--border-subtle)] flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <h2 class="text-xl font-bold text-white tracking-tight">
                        {{ $isEdit ? $labels['singular'] . ' details' : 'New ' . mb_strtolower($labels['singular']) }}
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
                <span class="text-[var(--accent-hover)]">*</span> The primary language is required. Languages left blank are filled from it.
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
                                    class="input @error($name . '.' . $code) is-invalid @enderror"
                                    placeholder="{{ $field['label'] }} — {{ $localeName }}">{{ $value[$code] ?? '' }}</textarea>
                                @else
                                <input type="text" name="{{ $name }}[{{ $code }}]" value="{{ $value[$code] ?? '' }}"
                                    class="input @error($name . '.' . $code) is-invalid @enderror"
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
                            {{-- Drop zone: the whole card is the file picker, so the
                                 preview doubles as the "replace this image" target. --}}
                            <div x-data="imageField(@js($value))" class="space-y-3">
                                <div @dragover.prevent="dragging = true"
                                    @dragleave.prevent="dragging = false"
                                    @drop.prevent="dragging = false; take($event.dataTransfer.files)"
                                    @click="$refs.file.click()"
                                    @keydown.enter.prevent="$refs.file.click()"
                                    @keydown.space.prevent="$refs.file.click()"
                                    role="button" tabindex="0"
                                    class="drop-zone"
                                    :class="{ 'drop-zone-active': dragging, 'drop-zone-filled': shown }">

                                    <input type="file" name="{{ $name }}_file" accept="image/*" x-ref="file" class="hidden"
                                        @change="take($event.target.files)">
                                    <input type="hidden" name="{{ $name }}_remove" :value="remove ? 1 : 0">

                                    <template x-if="shown">
                                        <div class="flex flex-col sm:flex-row items-center gap-4 sm:gap-5 w-full min-w-0">
                                            <img :src="shown" alt=""
                                                class="w-full sm:w-40 h-32 object-cover rounded-[var(--radius-md)] border border-[var(--border-subtle)] flex-shrink-0">
                                            <div class="min-w-0 flex-1 w-full text-center sm:text-left">
                                                <p class="text-sm font-semibold text-white truncate" x-text="fileName || 'Current image'"></p>
                                                <p class="text-xs text-[var(--text-muted)] mt-1 break-words" x-text="fileSize || 'Click to replace, or drop a new image'"></p>
                                                <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mt-3">
                                                    <button type="button" @click.stop="$refs.file.click()" class="zone-btn">
                                                        <x-lucide-refresh-cw class="w-3.5 h-3.5" />Replace
                                                    </button>
                                                    <button type="button" @click.stop="clear()" class="zone-btn zone-btn-danger">
                                                        <x-lucide-trash-2 class="w-3.5 h-3.5" />Remove
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </template>

                                    <template x-if="!shown">
                                        <div class="text-center py-8">
                                            <div class="w-12 h-12 mx-auto rounded-2xl flex items-center justify-center bg-[var(--accent-soft)] border border-[var(--accent-border)]">
                                                <x-lucide-image-plus class="w-5 h-5 text-[var(--accent-hover)]" />
                                            </div>
                                            <p class="text-sm font-semibold text-white mt-3">Drop an image here</p>
                                            <p class="text-xs text-[var(--text-muted)] mt-1">
                                                or <span class="text-[var(--accent-hover)] font-semibold">click to choose one</span>
                                                @if(!empty($field['help'])) · {{ $field['help'] }} @endif
                                            </p>
                                            <p class="text-[11px] text-[var(--text-muted)] mt-2">PNG, JPG or WEBP · up to 4 MB</p>
                                        </div>
                                    </template>
                                </div>

                                <p x-show="error" x-cloak x-text="error"
                                    class="text-[var(--accent)] text-xs flex items-center gap-1"></p>

                                @error($name . '_file')
                                <p class="text-[var(--accent)] text-xs flex items-center gap-1">
                                    <x-lucide-alert-circle class="w-3.5 h-3.5 flex-shrink-0" />{{ $message }}
                                </p>
                                @enderror
                            </div>
                            @break

                        @case('tech')
                            @php
                                $catalog = SiteTech::map($value);
                            @endphp
                            <div x-data="techField(@js(array_values($value)), @js($catalog))" class="space-y-3">
                                {{-- Chips carry the hidden inputs, so the posted order is
                                     the order shown here. --}}
                                <div class="flex flex-wrap gap-2" x-show="selected.length" x-cloak>
                                    <template x-for="(tech, i) in selected" :key="tech">
                                        <span class="tech-chip" :style="chipStyle(tech)">
                                            <template x-if="iconOf(tech)">
                                                <img :src="iconOf(tech)" alt="" class="w-3.5 h-3.5 shrink-0">
                                            </template>
                                            <span x-text="tech"></span>
                                            <button type="button" @click="remove(i)" class="tech-chip-x" :aria-label="'Remove ' + tech">
                                                <x-lucide-x class="w-3 h-3" />
                                            </button>
                                            <input type="hidden" name="{{ $name }}[]" :value="tech">
                                        </span>
                                    </template>
                                </div>

                                <div class="relative" @click.outside="open = false">
                                    <div class="input flex items-center gap-2 cursor-text" @click="open = true; $refs.search.focus()">
                                        <x-lucide-search class="w-4 h-4 text-[var(--text-muted)] flex-shrink-0" />
                                        <input type="text" x-ref="search" x-model="query" @focus="open = true"
                                            @keydown.enter.prevent="addTyped()"
                                            @keydown.escape="open = false"
                                            placeholder="Search technologies, or type a new one…"
                                            class="bg-transparent border-0 outline-none flex-1 min-w-0 text-sm p-0">
                                        <span class="text-xs text-[var(--text-muted)] flex-shrink-0" x-text="selected.length + ' selected'"></span>
                                    </div>

                                    <div x-show="open" x-cloak x-transition.opacity.duration.150ms class="tech-dropdown scroll-area">
                                        <template x-for="tech in matches()" :key="tech">
                                            <button type="button" @click="toggle(tech)" class="tech-option" :class="{ 'tech-option-on': selected.includes(tech) }">
                                                <span class="w-4 h-4 flex items-center justify-center flex-shrink-0">
                                                    <template x-if="iconOf(tech)">
                                                        <img :src="iconOf(tech)" alt="" class="w-4 h-4">
                                                    </template>
                                                    <template x-if="!iconOf(tech)">
                                                        <span class="w-2 h-2 rounded-full" :style="`background: ${colorOf(tech)}`"></span>
                                                    </template>
                                                </span>
                                                <span class="flex-1 text-left" x-text="tech"></span>
                                                <template x-if="selected.includes(tech)">
                                                    <x-lucide-check class="w-4 h-4 text-[var(--accent-hover)] flex-shrink-0" />
                                                </template>
                                            </button>
                                        </template>

                                        <button type="button" x-show="canAddTyped()" @click="addTyped()" class="tech-option text-[var(--accent-hover)]">
                                            <x-lucide-plus class="w-4 h-4 flex-shrink-0" />
                                            <span x-text="`Add &quot;${query.trim()}&quot;`"></span>
                                        </button>

                                        <p x-show="!matches().length && !canAddTyped()" class="px-3 py-4 text-xs text-center text-[var(--text-muted)]">
                                            Nothing found
                                        </p>
                                    </div>
                                </div>

                                @if(!empty($field['help']))
                                <p class="text-xs text-[var(--text-muted)]">{{ $field['help'] }}</p>
                                @endif

                                @error($name)
                                <p class="text-[var(--accent)] text-xs flex items-center gap-1">
                                    <x-lucide-alert-circle class="w-3.5 h-3.5 flex-shrink-0" />{{ $message }}
                                </p>
                                @enderror
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
                                <p class="text-xs text-[var(--text-muted)] mt-2">Selected: <span class="font-mono text-[var(--text-secondary)]" x-text="icon"></span></p>
                            </div>
                            @break

                        @case('select')
                            <select name="{{ $name }}" class="input cursor-pointer @error($name) is-invalid @enderror">
                                @unless($required)
                                <option value="">— none —</option>
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
                                class="input @error($name) is-invalid @enderror"
                                placeholder="Laravel&#10;React&#10;MySQL">{{ $value }}</textarea>
                            @break

                        @case('number')
                            <input type="number" name="{{ $name }}" value="{{ $value ?? 0 }}"
                                @isset($field['min']) min="{{ $field['min'] }}" @endisset
                                @isset($field['max']) max="{{ $field['max'] }}" @endisset
                                class="input max-w-[10rem] @error($name) is-invalid @enderror">
                            @break

                        @case('textarea')
                            <textarea name="{{ $name }}" rows="{{ $field['rows'] ?? 3 }}"
                                class="input @error($name) is-invalid @enderror">{{ $value }}</textarea>
                            @break

                        @default
                            <input type="{{ $field['type'] === 'url' ? 'url' : ($field['type'] === 'date' ? 'date' : 'text') }}"
                                name="{{ $name }}" value="{{ $value }}"
                                class="input @error($name) is-invalid @enderror"
                                placeholder="{{ $field['placeholder'] ?? '' }}">
                    @endswitch

                    @if(!empty($field['help']) && !in_array($field['type'], ['bool', 'image', 'tech']))
                    <p class="text-xs text-[var(--text-muted)] mt-2">{{ $field['help'] }}</p>
                    @endif

                    @unless($field['type'] === 'tech')
                    @error($name)
                    <p class="text-[var(--accent)] text-xs mt-2 flex items-center gap-1">
                        <x-lucide-alert-circle class="w-3.5 h-3.5 flex-shrink-0" />{{ $message }}
                    </p>
                    @enderror
                    @endunless
                </div>
                @endforeach
            </div>

            <div class="flex items-center justify-end gap-3 pt-7 mt-8 border-t border-[var(--border-subtle)]">
                <a href="{{ route('admin.' . $key . '.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">
                    <x-lucide-save class="w-4 h-4" />
                    Save
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
    [x-cloak] {
        display: none !important;
    }

    /* ---------- Image drop zone ---------- */
    .drop-zone {
        display: flex;
        align-items: center;
        justify-content: center;
        max-width: 100%;
        overflow: hidden;
        padding: 1.25rem;
        border: 1.5px dashed var(--border-strong);
        border-radius: var(--radius-lg);
        background: rgba(255, 255, 255, 0.02);
        cursor: pointer;
        transition: border-color .2s, background .2s;
    }

    .drop-zone:hover,
    .drop-zone:focus-visible {
        border-color: var(--accent-border);
        background: var(--accent-soft);
        outline: none;
    }

    .drop-zone-active {
        border-color: var(--accent-hover);
        background: var(--accent-soft);
    }

    /* A filled zone is a preview card first and a target second, so it drops
       the dashed border that invites a drop on the empty state. */
    .drop-zone-filled {
        border-style: solid;
        border-color: var(--border-subtle);
        padding: 1rem;
    }

    /* The layout's .btn-* rules are plain CSS loaded after Tailwind's sheet, so
       utility overrides on a .btn-secondary lose and the button renders at full
       size — wide enough to push out of the drop zone. These are their own
       class instead of a size override. */
    .zone-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.375rem 0.75rem;
        border-radius: var(--radius-md);
        border: 1px solid var(--border-subtle);
        background: var(--bg-overlay);
        font-size: 0.75rem;
        font-weight: 600;
        color: var(--text-primary);
        white-space: nowrap;
        cursor: pointer;
        transition: border-color .2s, color .2s, background .2s;
    }

    .zone-btn:hover {
        border-color: var(--border-strong);
    }

    .zone-btn-danger {
        color: var(--accent-hover);
        background: transparent;
        border-color: transparent;
    }

    .zone-btn-danger:hover {
        background: var(--accent-soft);
        border-color: var(--accent-border);
    }

    /* ---------- Technology picker ---------- */
    .tech-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.3rem 0.4rem 0.3rem 0.6rem;
        border-radius: 999px;
        border: 1px solid;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .tech-chip-x {
        display: inline-flex;
        padding: 0.15rem;
        border-radius: 999px;
        opacity: 0.65;
        cursor: pointer;
        transition: opacity .15s, background .15s;
    }

    .tech-chip-x:hover {
        opacity: 1;
        background: rgba(255, 255, 255, 0.15);
    }

    .tech-dropdown {
        position: absolute;
        z-index: 30;
        left: 0;
        right: 0;
        margin-top: 0.4rem;
        max-height: 17rem;
        overflow-y: auto;
        padding: 0.35rem;
        border-radius: var(--radius-md);
        border: 1px solid var(--border-strong);
        background: var(--bg-raised);
        box-shadow: 0 18px 40px -14px rgba(0, 0, 0, 0.65);
    }

    .tech-option {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        width: 100%;
        padding: 0.5rem 0.625rem;
        border-radius: var(--radius-sm);
        font-size: 0.8125rem;
        color: var(--text-secondary);
        cursor: pointer;
        transition: background .15s, color .15s;
    }

    .tech-option:hover {
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-primary);
    }

    .tech-option-on {
        color: var(--text-primary);
        background: var(--accent-soft);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('imageField', (current) => ({
            current,
            objectUrl: null,
            fileName: '',
            fileSize: '',
            remove: false,
            dragging: false,
            error: '',

            get shown() {
                return this.objectUrl || (this.remove ? null : this.current);
            },

            take(files) {
                const file = files && files[0];
                if (!file) return;

                if (!file.type.startsWith('image/')) {
                    this.error = 'Only image files are accepted.';
                    return;
                }

                if (file.size > 4 * 1024 * 1024) {
                    this.error = "The image must be 4 MB or smaller.";
                    return;
                }

                // The drop path never touches the file input, so assign the
                // dropped file to it — otherwise the form posts without it.
                const bucket = new DataTransfer();
                bucket.items.add(file);
                this.$refs.file.files = bucket.files;

                if (this.objectUrl) URL.revokeObjectURL(this.objectUrl);

                this.error = '';
                this.remove = false;
                this.objectUrl = URL.createObjectURL(file);
                this.fileName = file.name;
                this.fileSize = (file.size / 1024).toFixed(0) + ' KB';
            },

            clear() {
                if (this.objectUrl) URL.revokeObjectURL(this.objectUrl);

                this.$refs.file.value = '';
                this.objectUrl = null;
                this.fileName = '';
                this.fileSize = '';
                this.error = '';
                this.remove = true;
            },
        }));

        Alpine.data('techField', (initial, catalog) => ({
            selected: initial,
            catalog,
            query: '',
            open: false,

            iconOf(tech) {
                return this.catalog[tech]?.icon || null;
            },

            colorOf(tech) {
                return this.catalog[tech]?.color || '#8B95A5';
            },

            chipStyle(tech) {
                const color = this.colorOf(tech);
                return `color: ${color}; border-color: ${color}59; background: ${color}1f;`;
            },

            matches() {
                const q = this.query.trim().toLowerCase();
                const names = Object.keys(this.catalog);

                return q ? names.filter((name) => name.toLowerCase().includes(q)) : names;
            },

            canAddTyped() {
                const value = this.query.trim();
                return value.length > 0
                    && !Object.keys(this.catalog).some((name) => name.toLowerCase() === value.toLowerCase());
            },

            toggle(tech) {
                const at = this.selected.indexOf(tech);
                at === -1 ? this.selected.push(tech) : this.selected.splice(at, 1);
            },

            addTyped() {
                const value = this.query.trim();
                if (!value) return;

                // Typing an existing name selects it rather than creating a
                // near-duplicate that differs only in case.
                const known = Object.keys(this.catalog)
                    .find((name) => name.toLowerCase() === value.toLowerCase());

                const tech = known || value;

                if (!known) this.catalog[tech] = { icon: null, color: '#8B95A5' };
                if (!this.selected.includes(tech)) this.selected.push(tech);

                this.query = '';
            },

            remove(index) {
                this.selected.splice(index, 1);
            },
        }));
    });
</script>
@endpush
