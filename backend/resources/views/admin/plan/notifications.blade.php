@extends('layouts.dashboard')

@section('title', 'Notifications')
@section('breadcrumb', 'Plan')
@section('header_title', 'Notifications')

@section('content')
<div x-data="{ deleteModalOpen: false, deleteUrl: '', deleteName: '' }">

    @include('admin.plan.partials.delete-modal', ['what' => 'notification'])

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <p class="text-sm text-[var(--text-muted)]">
            Everything the system has tried to send.
            @if($failedCount > 0)
            <span class="text-[var(--accent-hover)] font-semibold">{{ $failedCount }} failed and can be retried.</span>
            @endif
        </p>

        <form method="GET" class="flex items-center gap-2">
            <select name="kind" onchange="this.form.submit()" class="input !w-auto !py-2 text-sm cursor-pointer">
                <option value="">All kinds</option>
                @foreach($kinds as $kind)
                <option value="{{ $kind->value }}" @selected(($filters['kind'] ?? null) === $kind->value)>{{ $kind->label() }}</option>
                @endforeach
            </select>

            <select name="status" onchange="this.form.submit()" class="input !w-auto !py-2 text-sm cursor-pointer">
                <option value="">All statuses</option>
                @foreach($statuses as $status)
                <option value="{{ $status->value }}" @selected(($filters['status'] ?? null) === $status->value)>{{ $status->label() }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="space-y-2">
        @forelse($notifications as $notification)
        <div class="card p-4">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">

                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 bg-[var(--accent-soft)] border border-[var(--accent-border)]">
                    <x-dynamic-component :component="'lucide-' . $notification->kind->icon()" class="w-4 h-4 text-[var(--accent-hover)]" />
                </div>

                <div class="min-w-0 flex-1">
                    <p class="font-semibold text-white truncate">{{ $notification->title }}</p>
                    <p class="text-xs text-[var(--text-muted)] mt-0.5">
                        {{ $notification->kind->label() }} · {{ $notification->created_at->format('j M Y, H:i') }}
                        @if($notification->sent_at) · sent {{ $notification->sent_at->diffForHumans() }} @endif
                    </p>
                    @if($notification->error)
                    <p class="text-xs text-[var(--accent-hover)] mt-1 truncate">{{ $notification->error }}</p>
                    @endif
                </div>

                <div class="flex items-center gap-2 flex-shrink-0">
                    <x-status-badge :status="$notification->status" />

                    @can('notifications.retry')
                    @if($notification->status->value === 'failed')
                    <form method="POST" action="{{ route('admin.notifications.retry', $notification) }}" class="inline">
                        @csrf
                        <button type="submit" title="Retry"
                            class="p-2 rounded-lg text-[var(--text-muted)] hover:text-[var(--accent-hover)] hover:bg-[var(--accent-soft)] transition-colors cursor-pointer">
                            <x-lucide-refresh-cw class="w-4 h-4" />
                        </button>
                    </form>
                    @endif
                    @endcan

                    @can('notifications.delete')
                    <button type="button" title="Delete"
                        @click="deleteUrl = @js(route('admin.notifications.destroy', $notification)); deleteName = @js($notification->title); deleteModalOpen = true"
                        class="p-2 rounded-lg text-[var(--text-muted)] hover:text-[var(--accent)] hover:bg-[var(--accent-soft)] transition-colors">
                        <x-lucide-trash-2 class="w-4 h-4" />
                    </button>
                    @endcan
                </div>
            </div>
        </div>
        @empty
        <div class="card px-6 py-16 text-center">
            <div class="flex flex-col items-center gap-3">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background: rgba(255,255,255,0.04); border: 1px solid var(--border-subtle);">
                    <x-lucide-bell class="w-6 h-6 text-[var(--text-muted)]" />
                </div>
                <p class="text-sm font-semibold text-[var(--text-secondary)]">Nothing sent yet</p>
            </div>
        </div>
        @endforelse
    </div>

    @if($notifications->hasPages())
    <div class="mt-6">{{ $notifications->links() }}</div>
    @endif
</div>
@endsection
