@extends('layouts.app')

@section('has_nav', 'yes')
@section('title', 'Kirish · ' . config('app.program_name'))

@section('content')
<div class="min-h-screen flex items-center justify-center p-4 sm:p-6 relative overflow-hidden bg-[var(--bg-base)]">

    <div class="absolute inset-0 z-0 overflow-hidden pointer-events-none" aria-hidden="true">
        <div class="absolute top-[-10%] left-[-5%] w-[28rem] h-[28rem] rounded-full blur-[120px]" style="background: rgba(59, 130, 246, 0.08);"></div>
        <div class="absolute bottom-[-10%] right-[-5%] w-[32rem] h-[32rem] rounded-full blur-[140px]" style="background: rgba(139, 92, 246, 0.07);"></div>
    </div>

    <div class="card relative z-10 w-full max-w-md p-8 sm:p-10 space-y-7 shadow-2xl shadow-black/50 page-enter border border-[var(--border-strong)]">

        <div class="text-center flex flex-col items-center">
            <a href="{{ route('home') }}" class="w-14 h-14 flex items-center justify-center rounded-2xl bg-white/5 border border-[var(--border-strong)] shadow-sm transition-transform duration-300 hover:scale-105 overflow-hidden">
                <img src="{{ asset('/images/logo.png') }}" alt="{{ config('app.program_name') }}" class="w-full h-full object-contain p-1.5">
            </a>

            <h2 class="mt-5 text-2xl sm:text-3xl font-extrabold text-white tracking-tight leading-tight">
                Qaytib keldingiz
            </h2>
            <p class="mt-2 text-sm text-[var(--text-secondary)]">
                {{ config('app.program_name') }} hisobingizga kiring
            </p>
        </div>

        <form action="{{ route('login') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label for="email" class="block text-sm font-semibold text-[var(--text-primary)] mb-1.5">
                    Email manzil
                </label>
                <input id="email" name="email" type="email" required
                    value="{{ old('email') }}"
                    autocomplete="email"
                    class="input @error('email') !border-[var(--accent)] !shadow-[0_0_0_3px_var(--accent-soft)] @enderror"
                    placeholder="example@mail.com">

                @error('email')
                <p class="mt-2 text-xs font-medium text-[var(--accent-alt)] flex items-center gap-1.5">
                    <x-lucide-circle-alert class="w-3.5 h-3.5 shrink-0" />
                    {{ $message }}
                </p>
                @enderror
            </div>

            <div x-data="{ showPassword: false }">
                <label for="password" class="block text-sm font-semibold text-[var(--text-primary)] mb-1.5">
                    Password
                </label>
                <div class="relative">
                    <input id="password" name="password"
                        :type="showPassword ? 'text' : 'password'"
                        required
                        autocomplete="current-password"
                        class="input pr-12"
                        placeholder="••••••••">

                    <button type="button"
                        @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 flex items-center pr-3.5 text-[var(--text-muted)] hover:text-[var(--accent-alt)] focus:outline-none transition-colors cursor-pointer">
                        <x-lucide-eye :class="showPassword ? 'hidden' : 'block'" class="w-5 h-5" />
                        <x-lucide-eye-off :class="showPassword ? 'block' : 'hidden'" class="w-5 h-5 hidden" />
                    </button>
                </div>
            </div>

            <div class="flex items-center justify-between text-sm pt-1">
                <label class="flex items-center gap-2.5 cursor-pointer group">
                    <input type="checkbox" name="remember" checked
                        class="w-4 h-4 rounded border-[var(--border-strong)] bg-[var(--bg-surface)] text-[var(--accent)] focus:ring-[var(--accent)] focus:ring-offset-0 focus:ring-offset-transparent cursor-pointer transition-colors">
                    <span class="text-[var(--text-secondary)] group-hover:text-white transition-colors">
                        Eslab qolish
                    </span>
                </label>
            </div>

            <button type="submit" class="btn-primary w-full py-3 text-base mt-2">
                <x-lucide-log-in class="w-5 h-5" />
                Kirish
            </button>
        </form>
    </div>
</div>
@endsection
