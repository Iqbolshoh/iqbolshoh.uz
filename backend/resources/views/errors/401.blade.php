@extends('layouts.app')

@section('title', '401 · Laravel Default')

@section('content')
<div class="min-h-screen w-full flex items-center justify-center relative overflow-hidden" style="background: var(--bg-base);">

    <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[30rem] h-[30rem] rounded-full blur-[120px] pointer-events-none" style="background: rgba(249, 115, 22, 0.10);"></div>

    <div class="relative z-10 text-center px-4 max-w-lg mx-auto flex flex-col items-center py-20 page-enter">

        <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl mb-6" style="background: rgba(249, 115, 22, 0.10); border: 1px solid rgba(249, 115, 22, 0.25); color: #f97316;">
            <x-lucide-key class="w-10 h-10" />
        </div>

        <h1 class="text-7xl sm:text-9xl font-black leading-none tracking-tighter mb-2" style="color: var(--text-primary);">
            401
        </h1>

        <h2 class="text-2xl sm:text-3xl font-extrabold mb-3 tracking-tight" style="color: var(--text-primary);">Kirish taqiqlangan</h2>

        <p class="text-base mb-8 font-medium" style="color: var(--text-secondary);">
            Bu sahifaga kirish uchun avval tizimga kirishingiz kerak.
        </p>

        <a href="{{ route('login') }}" class="btn-primary px-8">
            <x-lucide-log-in class="w-5 h-5" /> Tizimga kirish
        </a>

    </div>
</div>
@endsection
