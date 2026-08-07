@extends('layouts.app')

@section('title', '403 · Laravel Default')

@section('content')
<div class="min-h-screen w-full flex items-center justify-center relative overflow-hidden" style="background: var(--bg-base);">

    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[30rem] h-[30rem] rounded-full blur-[120px] pointer-events-none" style="background: rgba(239, 68, 68, 0.10);"></div>

    <div class="relative z-10 text-center px-4 max-w-lg mx-auto flex flex-col items-center py-20 page-enter">

        <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl mb-6" style="background: rgba(239, 68, 68, 0.10); border: 1px solid rgba(239, 68, 68, 0.25); color: #ef4444;">
            <x-lucide-lock class="w-10 h-10" />
        </div>

        <h1 class="text-7xl sm:text-9xl font-black leading-none tracking-tighter mb-2" style="color: var(--text-primary);">
            403
        </h1>

        <h2 class="text-2xl sm:text-3xl font-extrabold mb-3 tracking-tight" style="color: var(--text-primary);">Ruxsat yo'q</h2>

        <p class="text-base mb-8 font-medium" style="color: var(--text-secondary);">
            Bu sahifaga kirish huquqingiz yo'q.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 w-full sm:w-auto">
            <button onclick="history.back()" class="btn-secondary w-full sm:w-auto">
                <x-lucide-arrow-left class="w-5 h-5" /> Orqaga
            </button>
            <a href="{{ route('home') }}" class="btn-primary w-full sm:w-auto">
                <x-lucide-home class="w-5 h-5" /> Bosh sahifa
            </a>
        </div>

    </div>
</div>
@endsection
