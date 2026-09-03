@extends('layouts.dashboard')

@section('title', $config['singular'] . ' — ' . $message->name)
@section('breadcrumb', $config['plural'])
@section('header_title', $message->name)

@section('content')
@php
    use Illuminate\Support\Str;

    // The rows are read-only, so the detail view is just a labelled dump of
    // what the visitor submitted plus the request metadata kept with it.
    $details = $type === 'contact'
        ? ['Mavzu' => $message->subject]
        : [
            'Xizmat' => $message->service_name,
            'Narx'   => $message->service_price,
            'Telefon' => $message->phone,
        ];
@endphp

<div class="max-w-3xl mx-auto" x-data="{ deleteModalOpen: false }">

    {{-- Delete confirmation --}}
    <div x-show="deleteModalOpen" x-cloak class="fixed inset-0 z-[60]" role="dialog" aria-modal="true" style="display:none;">
        <div x-show="deleteModalOpen" @click="deleteModalOpen = false"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            class="fixed inset-0 bg-black/70 backdrop-blur-sm cursor-pointer"></div>

        <div class="flex min-h-screen items-center justify-center p-4 relative z-10">
            <div x-show="deleteModalOpen" @click.away="deleteModalOpen = false"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                class="w-full max-w-md rounded-[var(--radius-lg)] bg-[var(--bg-raised)] border border-[var(--border-strong)] shadow-2xl shadow-black/60 p-8">

                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[var(--accent-soft)] border border-[var(--accent-border)]">
                    <x-lucide-trash-2 class="h-6 w-6 text-[var(--accent)]" />
                </div>

                <div class="mt-5 text-center">
                    <h3 class="text-xl font-bold text-white tracking-tight">Delete {{ mb_strtolower($config['singular']) }}</h3>
                    <p class="mt-2 text-sm text-[var(--text-secondary)]">This cannot be undone.</p>
                </div>

                <div class="mt-7 flex flex-col sm:flex-row gap-3">
                    <button type="button" @click="deleteModalOpen = false" class="btn-secondary flex-1">Cancel</button>
                    <form action="{{ route('admin.messages.destroy', [$type, $message->id]) }}" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-[0.625rem] rounded-[var(--radius-md)] text-sm font-semibold text-white bg-[var(--accent)] hover:bg-[var(--accent-hover)] transition-colors shadow-lg shadow-[var(--accent-glow)] cursor-pointer">
                            <x-lucide-trash-2 class="w-4 h-4" />
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-between gap-4 mb-6">
        <a href="{{ route('admin.messages.index', $type) }}"
            class="inline-flex items-center gap-1.5 text-sm font-medium text-[var(--text-muted)] hover:text-white transition-colors">
            <x-lucide-arrow-left class="w-4 h-4" />
            {{ $config['plural'] }}ga qaytish
        </a>

        @can('messages.delete')
        <button type="button" @click="deleteModalOpen = true" class="btn-ghost !text-[var(--accent-hover)]">
            <x-lucide-trash-2 class="w-4 h-4" />
            Delete
        </button>
        @endcan
    </div>

    <div class="card p-6 sm:p-8">

        <div class="flex items-start gap-4 pb-6 mb-6 border-b border-[var(--border-subtle)]">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center font-bold text-white flex-shrink-0"
                style="background: linear-gradient(135deg, var(--accent-hover), var(--accent-alt));">
                {{ mb_strtoupper(mb_substr($message->name, 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <h2 class="text-xl font-bold text-white tracking-tight truncate">{{ $message->name }}</h2>
                <a href="mailto:{{ $message->email }}" class="text-sm text-[var(--accent-hover)] hover:underline break-all">{{ $message->email }}</a>
            </div>
            <span class="badge badge-success flex-shrink-0">
                <x-lucide-check class="w-3 h-3" />
                Read
            </span>
        </div>

        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-6">
            @foreach($details as $label => $value)
            @if($value)
            <div>
                <dt class="text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] mb-1">{{ $label }}</dt>
                <dd class="text-sm text-white">
                    @if($label === 'Telefon')
                    <a href="tel:{{ $value }}" class="text-[var(--accent-hover)] hover:underline">{{ $value }}</a>
                    @else
                    {{ $value }}
                    @endif
                </dd>
            </div>
            @endif
            @endforeach

            <div>
                <dt class="text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] mb-1">Yuborilgan vaqt</dt>
                <dd class="text-sm text-white font-mono">{{ $message->created_at->format('d.m.Y H:i') }}</dd>
            </div>
        </dl>

        @if($message->message)
        <div class="mb-6">
            <h3 class="text-xs font-bold uppercase tracking-wider text-[var(--text-muted)] mb-2">Message</h3>
            <div class="rounded-[var(--radius-md)] border border-[var(--border-subtle)] p-4 text-sm text-[var(--text-secondary)] leading-relaxed whitespace-pre-line"
                style="background: rgba(255,255,255,0.02);">{{ $message->message }}</div>
        </div>
        @endif

        <div class="pt-5 border-t border-[var(--border-subtle)] flex flex-wrap items-center gap-x-6 gap-y-2 text-xs text-[var(--text-muted)]">
            @if($message->ip)
            <span class="font-mono">IP: {{ $message->ip }}</span>
            @endif
            @if($message->user_agent)
            <span class="truncate max-w-full">{{ Str::limit($message->user_agent, 90) }}</span>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-3 pt-6 mt-6 border-t border-[var(--border-subtle)]">
            <a href="mailto:{{ $message->email }}" class="btn-primary">
                <x-lucide-reply class="w-4 h-4" />
                Email orqali javob berish
            </a>
            @if($type === 'orders' && $message->phone)
            <a href="tel:{{ $message->phone }}" class="btn-secondary">
                <x-lucide-phone class="w-4 h-4" />
                Call
            </a>
            @endif
        </div>
    </div>
</div>
@endsection
