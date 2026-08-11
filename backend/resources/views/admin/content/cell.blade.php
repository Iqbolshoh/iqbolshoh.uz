{{--
    One listing cell, rendered from the column specification. Shared by the
    table (md and up) and the stacked cards below it, so a column type is
    described in exactly one place.

    @param array  $column
    @param mixed  $value
    @param callable $plain   Picks the primary language out of a translation.
--}}
@php
    use App\Support\SiteIcons;
    use App\Support\SiteTech;
@endphp

@switch($column['type'])
    @case('image')
        @if($value)
        <img src="{{ $value }}" alt="" loading="lazy"
            class="w-16 h-11 object-cover rounded-lg border border-[var(--border-subtle)] bg-[var(--bg-surface)]">
        @else
        <div class="w-16 h-11 rounded-lg border border-[var(--border-subtle)] flex items-center justify-center" style="background: rgba(255,255,255,0.03);">
            <x-lucide-image class="w-4 h-4 text-[var(--text-muted)]" />
        </div>
        @endif
        @break

    @case('icon')
        <div class="w-9 h-9 rounded-xl flex items-center justify-center bg-[var(--accent-soft)] border border-[var(--accent-border)]">
            <x-dynamic-component :component="SiteIcons::component($value)" class="w-4 h-4 text-[var(--accent-hover)]" />
        </div>
        @break

    @case('trans')
        <p class="text-white font-medium max-w-md truncate">{{ $plain($value) }}</p>
        @break

    @case('badge')
        <span class="badge badge-accent">{{ $value }}</span>
        @break

    @case('tech')
        <div class="flex flex-wrap items-center gap-1.5 md:max-w-xs">
            @foreach(array_slice((array) $value, 0, 4) as $tech)
            @php $color = SiteTech::color($tech); $icon = SiteTech::iconUrl($tech); @endphp
            <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-[11px] font-semibold border"
                style="color: {{ $color }}; border-color: {{ $color }}59; background: {{ $color }}1f;">
                @if($icon)
                <img src="{{ $icon }}" alt="" loading="lazy" class="w-3 h-3 shrink-0">
                @endif
                {{ $tech }}
            </span>
            @endforeach
            @if(count((array) $value) > 4)
            <span class="text-xs text-[var(--text-muted)]">+{{ count((array) $value) - 4 }}</span>
            @endif
        </div>
        @break

    @case('list')
        <div class="flex flex-wrap gap-1 md:max-w-xs">
            @foreach(array_slice((array) $value, 0, 3) as $entry)
            <span class="badge" style="background: rgba(255,255,255,0.05); color: var(--text-secondary);">{{ $entry }}</span>
            @endforeach
            @if(count((array) $value) > 3)
            <span class="text-xs text-[var(--text-muted)]">+{{ count((array) $value) - 3 }}</span>
            @endif
        </div>
        @break

    @case('bool')
        @if($value)
        <span class="badge badge-success"><x-lucide-check class="w-3 h-3" /> Yes</span>
        @else
        <span class="text-xs text-[var(--text-muted)]">—</span>
        @endif
        @break

    @case('meter')
        <div class="flex items-center gap-3 min-w-[9rem]">
            <div class="flex-1 h-1.5 rounded-full overflow-hidden" style="background: rgba(255,255,255,0.07);">
                <div class="h-full rounded-full" style="width: {{ (int) $value }}%; background: linear-gradient(90deg, var(--accent-hover), var(--accent-alt));"></div>
            </div>
            <span class="font-mono text-xs text-[var(--text-secondary)]">{{ $value }}%</span>
        </div>
        @break

    @case('strong')
        <span class="font-mono font-bold text-white">{{ $value }}</span>
        @break

    @default
        <span class="text-[var(--text-secondary)]">{{ $value ?: '—' }}</span>
@endswitch
