<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#05070a" id="theme-color-meta">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    {{-- Apply saved theme immediately to prevent FOUC --}}
    <script>
        (function() {
            if (localStorage.getItem('laravel_default_theme') === 'light') {
                document.documentElement.classList.add('light');
                document.addEventListener('DOMContentLoaded', function() {
                    var m = document.getElementById('theme-color-meta');
                    if (m) m.content = '#f7f8ff';
                });
            }
        })();
    </script>

    <title>@yield('title', config('app.name'))</title>

    {{-- Font: Plus Jakarta Sans (UI) + JetBrains Mono (raqam/kod).
         Ikkalasi ham vite.config.js orqali o'z serverimizda hosting qilinadi. --}}
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        html {
            font-family: 'Plus Jakarta Sans', ui-sans-serif, system-ui, -apple-system, sans-serif;
        }

        body {
            background: var(--bg-base);
            color: var(--text-primary);
            min-height: 100vh;
            transition: background-color .3s, color .3s;
        }

        .font-mono {
            font-family: 'JetBrains Mono', ui-monospace, monospace;
        }

        /* Reusable UI Components */
        .card {
            background: var(--bg-raised);
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            backdrop-filter: blur(12px);
        }

        .btn-primary,
        .btn-secondary,
        .btn-ghost {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.625rem 1.125rem;
            border-radius: var(--radius-md);
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            transition: all .2s var(--ease-out);
            border: none;
            text-decoration: none;
        }

        .btn-primary {
            color: #ffffff;
            background: linear-gradient(135deg, var(--accent-hover), var(--accent-alt));
            box-shadow: 0 4px 16px -4px var(--accent-glow), inset 0 1px 0 rgba(255, 255, 255, 0.15);
            font-weight: 600;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--accent-alt), var(--accent-hover));
            transform: translateY(-1px);
            box-shadow: 0 6px 20px -4px var(--accent-glow), inset 0 1px 0 rgba(255, 255, 255, 0.15);
        }

        .btn-primary:active {
            transform: translateY(0) scale(0.98);
        }

        .btn-secondary {
            color: var(--text-primary);
            background: var(--bg-overlay);
            border: 1px solid var(--border-strong);
        }

        .btn-secondary:hover {
            background: #232938;
            border-color: rgba(255, 255, 255, 0.18);
            transform: translateY(-1px);
        }

        .btn-ghost {
            color: var(--text-secondary);
        }

        .btn-ghost:hover {
            color: var(--text-primary);
            background: rgba(255, 255, 255, 0.05);
        }

        .input {
            width: 100%;
            padding: 0.7rem 1rem;
            background: var(--bg-surface);
            border: 1px solid var(--border-strong);
            border-radius: var(--radius-md);
            font-size: 0.875rem;
            color: var(--text-primary);
            transition: border-color .2s, box-shadow .2s;
        }

        .input::placeholder {
            color: var(--text-muted);
        }

        .input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-soft);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.35rem;
            padding: 0.3rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.725rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
        }

        /* Animations */
        @keyframes page-enter {
            from {
                opacity: 0;
                transform: translateY(16px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .page-enter {
            animation: page-enter .55s var(--ease-out) both;
        }

        :focus-visible {
            outline: 2px solid var(--accent);
            outline-offset: 2px;
            border-radius: 4px;
        }

        @media (prefers-reduced-motion: reduce) {

            *,
            *::before,
            *::after {
                animation-duration: 0.01ms !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* ── Light Mode — vexa binafsha/ko'k palitrasi ── */
        html.light body {
            background: var(--bg-base);
            color: var(--text-primary);
        }

        html.light .text-white {
            color: var(--text-primary) !important;
        }

        html.light .hover\:text-white:hover {
            color: var(--text-primary) !important;
        }

        html.light .btn-primary {
            color: #ffffff !important;
        }

        html.light .text-gray-400 {
            color: #6560a0 !important;
        }

        html.light .bg-white\/5,
        html.light .bg-white\/10 {
            background-color: rgba(99, 76, 231, 0.04) !important;
        }

        html.light .hover\:bg-white\/5:hover,
        html.light .hover\:bg-white\/10:hover {
            background-color: rgba(99, 76, 231, 0.06) !important;
        }

        html.light .border-white\/10 {
            border-color: rgba(99, 76, 231, 0.12) !important;
        }

        html.light .btn-secondary {
            background: #eef0ff;
            border-color: rgba(99, 76, 231, 0.18);
            color: var(--text-primary);
        }

        html.light .btn-secondary:hover {
            background: #e5e8ff;
            border-color: rgba(99, 76, 231, 0.28);
            transform: translateY(-1px);
        }

        html.light .btn-ghost:hover {
            color: var(--text-primary);
            background: rgba(99, 76, 231, 0.07);
        }

        html.light .input {
            background: #f7f8ff;
            border-color: rgba(99, 76, 231, 0.16);
            color: var(--text-primary);
        }

        html.light .input:focus {
            background: #ffffff;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.12);
        }

        html.light .input::placeholder {
            color: #8a87bb;
        }

        html.light .card {
            background: #ffffff;
            border-color: rgba(99, 76, 231, 0.10);
            box-shadow: 0 1px 3px rgba(99, 76, 231, 0.06), 0 4px 18px rgba(99, 76, 231, 0.04);
        }

        html.light [style*="rgba(255,255,255,0.06)"],
        html.light [style*="rgba(255,255,255,0.08)"] {
            background: rgba(99, 76, 231, 0.05) !important;
        }

        html.light [style*="linear-gradient"] .text-white,
        html.light [style*="linear-gradient"].text-white {
            color: #ffffff !important;
        }

        /* keep white text on accent/coloured buttons */
        html.light .bg-\[var\(--accent\)\].text-white,
        html.light .bg-\[var\(--accent\)\] .text-white,
        html.light .bg-\[var\(--accent-hover\)\].text-white,
        html.light .bg-\[var\(--accent-hover\)\] .text-white {
            color: #ffffff !important;
        }
    </style>

    @stack('styles')
</head>

<body class="antialiased selection:bg-[var(--accent)] selection:text-white">

    @yield('content')

    @stack('scripts')
</body>

</html>