@extends('layouts.dashboard')

@php
    $confidenceColor = match ($forecast['confidence']) {
        'high' => '#22C55E',
        'medium' => '#F59E0B',
        default => '#8B95A5',
    };
@endphp

@section('title', 'Forecast')
@section('breadcrumb', 'Plan')
@section('header_title', 'Forecast')

@section('content')
<div class="space-y-6 max-w-4xl">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <p class="text-sm text-[var(--text-muted)]">
            Projected from {{ $source->format('F Y') }} onto
            <span class="text-white font-semibold">{{ $source->addMonth()->format('F Y') }}</span>.
        </p>

        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold border"
                style="color: {{ $confidenceColor }}; border-color: {{ $confidenceColor }}59; background: {{ $confidenceColor }}1f;">
                <span class="w-1.5 h-1.5 rounded-full" style="background: {{ $confidenceColor }};"></span>
                {{ ucfirst($forecast['confidence']) }} confidence
            </span>

            <form method="POST" action="{{ route('admin.forecast.store') }}">
                @csrf
                <button type="submit" class="btn-secondary !py-2 text-sm">
                    <x-lucide-save class="w-4 h-4" />Freeze
                </button>
            </form>
        </div>
    </div>

    @unless($forecast['enough_data'])
    <div class="card p-8 text-center">
        <div class="w-12 h-12 mx-auto rounded-2xl flex items-center justify-center bg-[var(--accent-soft)] border border-[var(--accent-border)]">
            <x-lucide-trending-up class="w-5 h-5 text-[var(--accent-hover)]" />
        </div>
        <h3 class="text-lg font-bold text-white mt-4">Not enough history yet</h3>
        <p class="text-sm text-[var(--text-secondary)] mt-2 max-w-md mx-auto">
            A projection built on a handful of plans would be a guess wearing a percentage sign.
            Keep using Plan and this page fills in on its own.
        </p>
    </div>
    @else

    {{-- What the projection is built from --}}
    <div class="card p-6">
        <h3 class="text-sm font-bold uppercase tracking-wider text-[var(--text-muted)] mb-4">{{ $source->format('F Y') }}</h3>
        <div class="grid gap-4 grid-cols-2 lg:grid-cols-4">
            @foreach([
                ['Plans', $forecast['source']['total']],
                ['Completed', $forecast['source']['completed']],
                ['Raw rate', $forecast['source']['raw_rate'] . '%'],
                ['True rate', $forecast['source']['true_rate'] . '%'],
            ] as [$label, $value])
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-[var(--text-muted)]">{{ $label }}</p>
                <p class="text-2xl font-bold font-mono text-white mt-1">{{ $value }}</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- The projection itself --}}
    <div class="card p-6">
        <h3 class="text-sm font-bold uppercase tracking-wider text-[var(--text-muted)] mb-1">
            {{ $source->addMonth()->format('F Y') }} projection
        </h3>
        <p class="text-xs text-[var(--text-muted)] mb-5">
            If you schedule this many plans, this is roughly how many land — at the rate you actually held last month.
        </p>

        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($forecast['projection'] as $row)
            <div class="rounded-[var(--radius-md)] border border-[var(--border-subtle)] p-4" style="background: rgba(99,102,241,0.06);">
                <p class="text-xs text-[var(--text-muted)]">{{ $row['plans'] }} plans</p>
                <p class="text-2xl font-bold font-mono text-white mt-1">~{{ $row['completed'] }}</p>
                <p class="text-[11px] text-[var(--text-muted)] mt-0.5">completed</p>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Recommendations --}}
    @if($forecast['recommendations'] !== [])
    <div class="card p-6">
        <h3 class="text-sm font-bold uppercase tracking-wider text-[var(--text-muted)] mb-4">What the numbers suggest</h3>
        <ul class="space-y-3">
            @foreach($forecast['recommendations'] as $line)
            <li class="text-sm text-[var(--text-secondary)] leading-relaxed pl-4 border-l-2 border-[var(--accent-border)]">
                {{ $line }}
            </li>
            @endforeach
        </ul>
    </div>
    @endif

    {{-- The segments the recommendations came from --}}
    <div class="grid gap-4 lg:grid-cols-2">
        <div class="card p-6">
            <h3 class="text-sm font-bold uppercase tracking-wider text-[var(--text-muted)] mb-4">Time of day</h3>
            @include('admin.plan.partials.rate-bars', ['rows' => $forecast['segments']['hour_band']])
        </div>

        <div class="card p-6">
            <h3 class="text-sm font-bold uppercase tracking-wider text-[var(--text-muted)] mb-4">Postponement behaviour</h3>
            @include('admin.plan.partials.rate-bars', ['rows' => $forecast['segments']['postponement']])
        </div>
    </div>
    @endunless
</div>
@endsection
