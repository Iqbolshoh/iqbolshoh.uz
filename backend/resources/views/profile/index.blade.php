@extends('layouts.dashboard')
@section('title', 'Mening profilim')
@section('header_title', 'Mening profilim')

@section('content')
@php
    $colors = ['bg-blue-500/20 text-blue-400','bg-indigo-500/20 text-indigo-400','bg-violet-500/20 text-violet-400','bg-sky-500/20 text-sky-400'];
    $avatarColor = $colors[$user->id % count($colors)];
    $roles = $user->getRoleNames();
@endphp

<div class="max-w-2xl mx-auto space-y-6">

    {{-- Avatar kartasi --}}
    <div class="card p-6">
        <div class="flex items-center gap-5">
            <div class="w-16 h-16 rounded-2xl flex items-center justify-center font-extrabold text-2xl flex-shrink-0 {{ $avatarColor }}">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
            <div>
                <h2 class="text-xl font-extrabold text-white">{{ $user->name }}</h2>
                <p class="text-sm text-[var(--text-muted)] mt-0.5">{{ $user->email }}</p>
                <div class="flex flex-wrap gap-1.5 mt-2">
                    @foreach($roles as $role)
                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-bold" style="background:var(--accent-soft);color:var(--accent-hover);border:1px solid var(--accent-border)">
                        {{ $role }}
                    </span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Profil ma'lumotlari --}}
    <div class="card p-6">
        <h3 class="text-sm font-bold uppercase tracking-widest text-[var(--text-muted)] mb-5">Profil ma'lumotlari</h3>
        <form action="{{ route('profile.update') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-[var(--text-muted)] mb-1.5 uppercase tracking-wide">Ism</label>
                <input name="name" value="{{ old('name', $user->name) }}" class="input @error('name') border-red-500/60 @enderror" required>
                @error('name')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-[var(--text-muted)] mb-1.5 uppercase tracking-wide">Email</label>
                <input name="email" type="email" value="{{ old('email', $user->email) }}" class="input @error('email') border-red-500/60 @enderror" required>
                @error('email')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="pt-2">
                <button type="submit" class="btn-primary">
                    <x-lucide-save class="w-4 h-4" /> Saqlash
                </button>
            </div>
        </form>
    </div>

    {{-- Parol o'zgartirish --}}
    <div class="card p-6">
        <h3 class="text-sm font-bold uppercase tracking-widest text-[var(--text-muted)] mb-5">Parol o'zgartirish</h3>
        <form action="{{ route('profile.password') }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-[var(--text-muted)] mb-1.5 uppercase tracking-wide">Joriy parol</label>
                <input name="current_password" type="password" class="input @error('current_password') border-red-500/60 @enderror" placeholder="••••••••">
                @error('current_password')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-[var(--text-muted)] mb-1.5 uppercase tracking-wide">Yangi parol</label>
                <input name="password" type="password" class="input @error('password') border-red-500/60 @enderror" placeholder="Kamida 6 belgi">
                @error('password')<p class="text-xs text-red-400 mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-[var(--text-muted)] mb-1.5 uppercase tracking-wide">Yangi parolni tasdiqlang</label>
                <input name="password_confirmation" type="password" class="input" placeholder="••••••••">
            </div>

            <div class="pt-2">
                <button type="submit" class="btn-secondary">
                    <x-lucide-lock class="w-4 h-4" /> Parolni o'zgartirish
                </button>
            </div>
        </form>
    </div>

    {{-- Tizim ma'lumotlari --}}
    <div class="card p-4">
        <div class="flex flex-wrap gap-x-6 gap-y-2 text-xs text-[var(--text-muted)] font-mono">
            <span>ID: #{{ $user->id }}</span>
            <span>Ro'yxatdan o'tgan: {{ $user->created_at->format('d.m.Y') }}</span>
        </div>
    </div>

</div>
@endsection
