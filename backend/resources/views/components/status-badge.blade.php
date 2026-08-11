@props(['status'])

{{-- One badge for every enum that carries a colour, so a status looks the same
     on the listing, the detail page and the calendar. --}}
@php
    $color = $status->color();
@endphp

<span {{ $attributes->merge([
        'class' => 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold border whitespace-nowrap',
    ]) }}
    style="color: {{ $color }}; border-color: {{ $color }}59; background: {{ $color }}1f;">
    <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" style="background: {{ $color }};"></span>
    {{ $status->label() }}
</span>
