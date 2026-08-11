@extends('layouts.dashboard')

@section('title', $config['plural'])
@section('breadcrumb', 'Aloqa')
@section('header_title', $config['plural'])

@section('content')
<div x-data="{ deleteModalOpen: false, deleteUrl: '', deleteName: '' }">

    {{-- Delete confirmation --}}
    <div x-show="deleteModalOpen" x-cloak class="fixed inset-0 z-[60]" role="dialog" aria-modal="true" style="display:none;">
        <div x-show="deleteModalOpen"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            @click="deleteModalOpen = false"
            class="fixed inset-0 bg-black/70 backdrop-blur-sm cursor-pointer"></div>

        <div class="flex min-h-screen items-center justify-center p-4 relative z-10">
            <div x-show="deleteModalOpen"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                @click.away="deleteModalOpen = false"
                class="w-full max-w-md rounded-[var(--radius-lg)] bg-[var(--bg-raised)] border border-[var(--border-strong)] shadow-2xl shadow-black/60 p-8">

                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-[var(--accent-soft)] border border-[var(--accent-border)]">
                    <x-lucide-trash-2 class="h-6 w-6 text-[var(--accent)]" />
                </div>

                <div class="mt-5 text-center">
                    <h3 class="text-xl font-bold text-white tracking-tight">{{ $config['singular'] }}ni o'chirish</h3>
                    <p class="mt-2 text-sm text-[var(--text-secondary)] leading-relaxed">
                        <span class="font-semibold text-white" x-text='"\"" + deleteName + "\""'></span> yuborgan
                        {{ mb_strtolower($config['singular']) }} butunlay o'chiriladi.
                    </p>
                </div>

                <div class="mt-7 flex flex-col sm:flex-row gap-3">
                    <button type="button" @click="deleteModalOpen = false" class="btn-secondary flex-1">Bekor qilish</button>
                    <form :action="deleteUrl" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-5 py-[0.625rem] rounded-[var(--radius-md)] text-sm font-semibold text-white bg-[var(--accent)] hover:bg-[var(--accent-hover)] transition-colors shadow-lg shadow-[var(--accent-glow)] cursor-pointer">
                            <x-lucide-trash-2 class="w-4 h-4" />
                            O'chirish
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Toolbar --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.messages.index', $type) }}"
                class="px-3.5 py-1.5 rounded-lg text-sm font-semibold transition-colors {{ request('filter') !== 'unread' ? 'bg-[var(--accent-soft)] text-[var(--accent-hover)] border border-[var(--accent-border)]' : 'text-[var(--text-muted)] hover:text-[var(--text-secondary)]' }}">
                Hammasi
            </a>
            <a href="{{ route('admin.messages.index', [$type, 'filter' => 'unread']) }}"
                class="px-3.5 py-1.5 rounded-lg text-sm font-semibold transition-colors {{ request('filter') === 'unread' ? 'bg-[var(--accent-soft)] text-[var(--accent-hover)] border border-[var(--accent-border)]' : 'text-[var(--text-muted)] hover:text-[var(--text-secondary)]' }}">
                O'qilmagan
                @if($unread > 0)
                <span class="nav-badge" style="margin-left: 0.375rem;">{{ $unread }}</span>
                @endif
            </a>
        </div>

        <div class="flex items-center gap-3 flex-wrap">
            <form method="GET" action="{{ request()->url() }}" class="flex items-center gap-2">
                @if(request('filter'))<input type="hidden" name="filter" value="{{ request('filter') }}">@endif
                <div class="relative">
                    <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 h-5 w-5 text-[var(--text-muted)] pointer-events-none" />
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Ism, email, matn..."
                        class="pl-11 pr-4 py-2 text-sm rounded-[var(--radius-md)] bg-[var(--bg-surface)] border border-[var(--border-subtle)] text-white placeholder-[var(--text-muted)] focus:outline-none focus:border-[var(--accent)] w-56 transition-colors">
                </div>
                @if(request('search'))
                <a href="{{ route('admin.messages.index', $type) }}" class="p-2 rounded-lg text-[var(--text-muted)] hover:text-white hover:bg-white/5 transition-colors" title="Tozalash">
                    <x-lucide-x class="w-4 h-4" />
                </a>
                @endif
            </form>

            @if($unread > 0)
            <form action="{{ route('admin.messages.read-all', $type) }}" method="POST">
                @csrf
                <button type="submit" class="btn-secondary">
                    <x-lucide-check-check class="w-4 h-4" />
                    Hammasini o'qilgan deb belgilash
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="divide-y divide-[var(--border-subtle)]">
            @forelse($messages as $message)
            <div class="flex items-start gap-4 px-5 sm:px-6 py-4 transition-colors hover:bg-white/[0.02] {{ $message->read_at ? '' : 'bg-[var(--accent-soft)]' }}">

                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-sm flex-shrink-0 text-white"
                    style="background: linear-gradient(135deg, var(--accent-hover), var(--accent-alt));">
                    {{ mb_strtoupper(mb_substr($message->name, 0, 1)) }}
                </div>

                <a href="{{ route('admin.messages.show', [$type, $message->id]) }}" class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 flex-wrap">
                        <p class="text-sm font-bold text-white truncate">{{ $message->name }}</p>
                        @unless($message->read_at)
                        <span class="badge badge-accent">Yangi</span>
                        @endunless
                        @if($type === 'orders' && $message->service_name)
                        <span class="badge" style="background: rgba(255,255,255,0.05); color: var(--text-secondary);">{{ $message->service_name }}</span>
                        @endif
                    </div>

                    <p class="text-xs text-[var(--text-muted)] mt-0.5 truncate">{{ $message->email }}</p>

                    <p class="text-sm text-[var(--text-secondary)] mt-1.5 line-clamp-2">
                        {{ \Illuminate\Support\Str::limit($type === 'contact' ? ($message->subject ? $message->subject . ' — ' . $message->message : $message->message) : ($message->message ?: '—'), 140) }}
                    </p>
                </a>

                <div class="flex flex-col items-end gap-2 flex-shrink-0">
                    <span class="text-[0.68rem] text-[var(--text-muted)] font-mono whitespace-nowrap">
                        {{ $message->created_at->format('d.m.Y H:i') }}
                    </span>
                    @can('messages.delete')
                    <button type="button"
                        @click="deleteUrl = @js(route('admin.messages.destroy', [$type, $message->id])); deleteName = @js($message->name); deleteModalOpen = true"
                        class="p-2 rounded-lg text-[var(--text-muted)] hover:text-[var(--accent)] hover:bg-[var(--accent-soft)] transition-colors" title="O'chirish">
                        <x-lucide-trash-2 class="w-4 h-4" />
                    </button>
                    @endcan
                </div>
            </div>
            @empty
            <div class="px-6 py-16 text-center">
                <div class="flex flex-col items-center gap-3">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background: rgba(255,255,255,0.04); border: 1px solid var(--border-subtle);">
                        <x-dynamic-component :component="'lucide-' . $config['icon']" class="w-6 h-6 text-[var(--text-muted)]" />
                    </div>
                    <p class="text-sm font-semibold text-[var(--text-secondary)]">Hozircha {{ mb_strtolower($config['plural']) }} yo'q</p>
                    <p class="text-xs text-[var(--text-muted)]">Saytdagi forma to'ldirilganda shu yerda paydo bo'ladi</p>
                </div>
            </div>
            @endforelse
        </div>

        @if($messages->hasPages())
        <div class="px-6 py-4 border-t border-[var(--border-subtle)]">
            {{ $messages->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
