@extends('layouts.dashboard')

@php
    use App\Models\Plan;
    use Illuminate\Support\Str;
@endphp

@section('title', $plan->title)
@section('breadcrumb', 'Plan')
@section('header_title', 'Plan detail')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <a href="{{ route('admin.plans.index') }}"
        class="inline-flex items-center gap-1.5 text-sm font-medium text-[var(--text-muted)] hover:text-white transition-colors">
        <x-lucide-arrow-left class="w-4 h-4" />
        Back to plans
    </a>

    <div class="card p-6 sm:p-8">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
            <div class="min-w-0">
                <h2 class="text-2xl font-bold text-white tracking-tight">{{ $plan->title }}</h2>
                <p class="text-sm text-[var(--text-muted)] mt-1">
                    {{ $plan->date->format('l, j F Y') }} · {{ Str::substr($plan->start_time, 0, 5) }}
                    · planned {{ Plan::humanMinutes($plan->planned_minutes) }}
                    @if($plan->actual_minutes) · took {{ Plan::humanMinutes($plan->actual_minutes) }} @endif
                </p>
            </div>

            <div class="flex items-center gap-2 flex-shrink-0">
                <x-status-badge :status="$plan->status" />
                <x-status-badge :status="$plan->priority" />
            </div>
        </div>

        @if($plan->description)
        <p class="text-sm text-[var(--text-secondary)] mt-5 leading-relaxed">{{ $plan->description }}</p>
        @endif

        <div class="grid gap-4 sm:grid-cols-3 mt-6 pt-6 border-t border-[var(--border-subtle)]">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-[var(--text-muted)]">Goal</p>
                @if($plan->goal)
                <p class="text-sm text-white mt-1 inline-flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full" style="background: {{ $plan->goal->color ?? '#8B95A5' }};"></span>
                    {{ $plan->goal->title }}
                </p>
                @else
                <p class="text-sm text-[var(--text-muted)] mt-1">Standalone</p>
                @endif
            </div>

            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-[var(--text-muted)]">Postponed</p>
                <p class="text-sm text-white mt-1">{{ $plan->postpone_count }}× {{ $plan->postpone_reason?->label() ? '· ' . $plan->postpone_reason->label() : '' }}</p>
            </div>

            <div>
                <p class="text-[11px] font-bold uppercase tracking-wider text-[var(--text-muted)]">Reminders sent</p>
                <p class="text-sm text-white mt-1">{{ $plan->reminder_count }}</p>
            </div>
        </div>

        @if($plan->fail_reason)
        <div class="mt-5 rounded-[var(--radius-md)] border border-[var(--border-subtle)] p-4" style="background: rgba(239,68,68,0.06);">
            <p class="text-sm text-[var(--text-secondary)]">
                {{ $plan->fail_reason->emoji() }} Marked failed — {{ $plan->fail_reason->label() }}
            </p>
        </div>
        @endif

        @if($plan->interruption)
        <div class="mt-5 rounded-[var(--radius-md)] border border-[var(--border-subtle)] p-4" style="background: rgba(99,102,241,0.06);">
            <p class="text-sm text-[var(--text-secondary)]">
                {{ $plan->interruption->type->emoji() }} Interrupted by {{ $plan->interruption->type->label() }}
                @if($plan->interruption->title) — {{ $plan->interruption->title }} @endif
            </p>
        </div>
        @endif
    </div>

    {{-- The trail. This is what the forecast reads, so it is worth showing in full. --}}
    <div class="card p-6 sm:p-8">
        <h3 class="text-sm font-bold uppercase tracking-wider text-[var(--text-muted)] mb-5">History</h3>

        <ol class="space-y-0">
            @foreach($events as $event)
            <li class="flex gap-4 pb-5 last:pb-0 relative">
                @unless($loop->last)
                <span class="absolute left-[15px] top-8 bottom-0 w-px bg-[var(--border-subtle)]"></span>
                @endunless

                <span class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 bg-[var(--accent-soft)] border border-[var(--accent-border)]">
                    <x-dynamic-component :component="'lucide-' . $event->event_type->icon()" class="w-3.5 h-3.5 text-[var(--accent-hover)]" />
                </span>

                <div class="min-w-0 pt-1">
                    <p class="text-sm font-semibold text-white">{{ $event->event_type->label() }}</p>
                    <p class="text-xs text-[var(--text-muted)] mt-0.5">
                        {{ $event->created_at->format('j M Y, H:i') }}
                        @if($event->to_time)
                        · {{ Str::substr($event->from_time, 0, 5) }} → {{ Str::substr($event->to_time, 0, 5) }}
                        @endif
                    </p>
                </div>
            </li>
            @endforeach
        </ol>
    </div>

    @if($notifications->isNotEmpty())
    <div class="card p-6 sm:p-8">
        <h3 class="text-sm font-bold uppercase tracking-wider text-[var(--text-muted)] mb-5">Notifications</h3>
        <div class="space-y-2">
            @foreach($notifications as $notification)
            <div class="flex items-center justify-between gap-3 py-2 border-b border-[var(--border-subtle)] last:border-0">
                <p class="text-sm text-[var(--text-secondary)] truncate">{{ $notification->title }}</p>
                <div class="flex items-center gap-3 flex-shrink-0">
                    <span class="text-xs text-[var(--text-muted)]">{{ $notification->created_at->format('j M, H:i') }}</span>
                    <x-status-badge :status="$notification->status" />
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
