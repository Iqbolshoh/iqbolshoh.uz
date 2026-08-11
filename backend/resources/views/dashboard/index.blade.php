@extends('layouts.dashboard')

@section('title', 'Dashboard')
@section('header_title', 'Dashboard')

@section('header_actions')
<a href="{{ url('/') }}" target="_blank" rel="noopener" class="btn-secondary">
    <x-lucide-external-link class="w-4 h-4" />
    Saytni ochish
</a>
@endsection

@section('content')
@php
    // Section key → sidebar label and icon, so the tiles link straight to the
    // page that edits them.
    $sections = [
        'projects'      => ['Loyihalar', 'folder-git-2'],
        'services'      => ['Xizmatlar', 'briefcase'],
        'tech-stacks'   => ['Texnologiyalar', 'layers'],
        'stats'         => ['Stats', 'bar-chart-3'],
        'highlights'    => ["Ta'kidlar", 'sparkles'],
        'journeys'      => ['Journey', 'milestone'],
        'beyonds'       => ['Dasturlashdan tashqari', 'heart-handshake'],
        'process-steps' => ['Ish jarayoni', 'list-checks'],
    ];
@endphp

<div class="mb-8">
    <h2 class="text-2xl font-bold text-white tracking-tight">Assalomu alaykum, {{ $user->name }}</h2>
    <p class="text-sm text-[var(--text-muted)] mt-1">Manage every piece of iqbolshoh.uz content from here.</p>
</div>

{{-- Inbox --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-6">
    @foreach([
        'contact' => ['Aloqa xabarlari', 'mail'],
        'orders'  => ['Service orders', 'shopping-bag'],
    ] as $type => [$label, $icon])
    <a href="{{ route('admin.messages.index', $type) }}" class="card card-hover p-6 flex items-center gap-5">
        <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-[var(--accent-soft)] border border-[var(--accent-border)] flex-shrink-0">
            <x-dynamic-component :component="'lucide-' . $icon" class="w-5 h-5 text-[var(--accent-hover)]" />
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-sm font-semibold text-[var(--text-secondary)]">{{ $label }}</p>
            <p class="text-3xl font-extrabold text-white leading-tight mt-0.5 font-mono">{{ $inbox[$type]['total'] }}</p>
        </div>
        @if($inbox[$type]['unread'] > 0)
        <span class="badge badge-accent flex-shrink-0">{{ $inbox[$type]['unread'] }} yangi</span>
        @endif
    </a>
    @endforeach
</div>

{{-- Content sections --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    @foreach($sections as $key => [$label, $icon])
    <a href="{{ route('admin.' . $key . '.index') }}" class="card card-hover p-5">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: rgba(255,255,255,0.04); border: 1px solid var(--border-subtle);">
                <x-dynamic-component :component="'lucide-' . $icon" class="w-4 h-4 text-[var(--text-secondary)]" />
            </div>
            <span class="text-2xl font-extrabold text-white font-mono leading-none">{{ $counts[$key] }}</span>
        </div>
        <p class="text-sm font-semibold text-[var(--text-secondary)] truncate">{{ $label }}</p>
    </a>
    @endforeach
</div>

{{-- Latest submissions --}}
<div class="card overflow-hidden">
    <div class="px-6 py-4 border-b border-[var(--border-subtle)] flex items-center justify-between gap-4">
        <h3 class="text-sm font-bold text-white">Latest enquiries</h3>
        <a href="{{ route('admin.messages.index', 'contact') }}" class="text-xs font-semibold text-[var(--accent-hover)] hover:text-[var(--accent-alt)] transition-colors">
            All
        </a>
    </div>

    <div class="divide-y divide-[var(--border-subtle)]">
        @forelse($recent as $entry)
        <a href="{{ route('admin.messages.show', [$entry['type'], $entry['id']]) }}"
            class="flex items-start gap-4 px-6 py-4 hover:bg-white/[0.02] transition-colors">

            <div class="w-9 h-9 rounded-xl flex items-center justify-center font-bold text-sm flex-shrink-0 text-white"
                style="background: linear-gradient(135deg, var(--accent-hover), var(--accent-alt));">
                {{ mb_strtoupper(mb_substr($entry['name'], 0, 1)) }}
            </div>

            <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2 flex-wrap">
                    <p class="text-sm font-semibold text-white truncate">{{ $entry['name'] }}</p>
                    <span class="badge" style="background: rgba(255,255,255,0.05); color: var(--text-secondary);">
                        {{ $entry['type'] === 'contact' ? 'Message' : 'Order' }}
                    </span>
                    @if($entry['unread'])
                    <span class="badge badge-accent">New</span>
                    @endif
                </div>
                <p class="text-xs text-[var(--text-muted)] mt-1 truncate">{{ \Illuminate\Support\Str::limit($entry['summary'], 90) }}</p>
            </div>

            <span class="text-[0.68rem] text-[var(--text-muted)] font-mono whitespace-nowrap flex-shrink-0">
                {{ $entry['date']->format('d.m.Y') }}
            </span>
        </a>
        @empty
        <div class="px-6 py-14 text-center">
            <div class="flex flex-col items-center gap-3">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background: rgba(255,255,255,0.04); border: 1px solid var(--border-subtle);">
                    <x-lucide-inbox class="w-6 h-6 text-[var(--text-muted)]" />
                </div>
                <p class="text-sm font-semibold text-[var(--text-secondary)]">No enquiries yet</p>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
