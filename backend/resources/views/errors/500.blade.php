@extends('layouts.app')

@section('title', '500 · Laravel Default')

@section('content')
<div class="min-h-screen w-full flex items-center justify-center relative overflow-hidden" style="background: var(--bg-base);">

    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[30rem] h-[30rem] rounded-full blur-[120px] pointer-events-none" style="background: rgba(244, 63, 94, 0.10);"></div>

    <div class="relative z-10 text-center px-4 max-w-lg mx-auto flex flex-col items-center py-20 page-enter">

        <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl mb-6" style="background: rgba(244, 63, 94, 0.10); border: 1px solid rgba(244, 63, 94, 0.25); color: #f43f5e;">
            <x-lucide-server-crash class="w-10 h-10" />
        </div>

        <h1 class="text-7xl sm:text-9xl font-black leading-none tracking-tighter mb-2" style="color: var(--text-primary);">
            500
        </h1>

        <h2 class="text-2xl sm:text-3xl font-extrabold mb-3 tracking-tight" style="color: var(--text-primary);">Server xatosi</h2>

        <p class="text-base mb-8 font-medium" style="color: var(--text-secondary);">
            Serverda kutilmagan xatolik yuz berdi. Iltimos, keyinroq qayta urinib ko'ring.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3 w-full sm:w-auto">
            <button onclick="window.location.reload()" class="btn-primary w-full sm:w-auto">
                <x-lucide-refresh-cw class="w-5 h-5" /> Sahifani yangilash
            </button>
            <a href="{{ route('home') }}" class="btn-secondary w-full sm:w-auto">
                <x-lucide-home class="w-5 h-5" /> Bosh sahifa
            </a>
        </div>

    </div>
</div>
@endsection
