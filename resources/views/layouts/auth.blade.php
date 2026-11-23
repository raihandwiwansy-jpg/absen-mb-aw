<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Symphony Marching Band') }} — @yield('title', 'Masuk')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="{{ asset('logo.jpg') }}">

    <!-- Dark Mode Initializer -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        function toggleTheme() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @if (file_exists(public_path('build/manifest.json')))
        @php
            $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
            $cssFile = $manifest['resources/css/app.css']['file'] ?? null;
            $jsFile = $manifest['resources/js/app.js']['file'] ?? null;
        @endphp
        @if($cssFile)
            <link rel="stylesheet" href="{{ asset('build/' . $cssFile) }}">
        @endif
        @if($jsFile)
            <script type="module" src="{{ asset('build/' . $jsFile) }}"></script>
        @endif
    @else
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    @stack('styles')
</head>
<body class="min-h-full auth-bg text-gray-800 dark:text-gray-100 flex flex-col justify-between selection:bg-[#C6A044] selection:text-[#101D3B] relative overflow-x-hidden"
      x-data="{ isDark: document.documentElement.classList.contains('dark') }">

    <!-- ============================================================
         BACKGROUND DECORATIVE AURORAS & MARCHING BAND ELEMENTS
         ============================================================ -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0" aria-hidden="true">
        <!-- Golden radial glows -->
        <div class="absolute -top-40 -left-40 w-[550px] h-[550px] bg-[#C6A044]/15 rounded-full blur-[140px]"></div>
        <div class="absolute -bottom-40 -right-40 w-[550px] h-[550px] bg-[#1a2d57]/40 dark:bg-[#C6A044]/10 rounded-full blur-[140px]"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[700px] bg-[#101D3B]/40 dark:bg-black/40 rounded-full blur-[160px]"></div>

        <!-- Floating ambient musical notes -->
        <div class="absolute top-16 left-[10%] text-[#C6A044]/30 text-2xl select-none animate-note-1">♩</div>
        <div class="absolute top-1/3 left-[5%] text-[#C6A044]/25 text-3xl select-none animate-note-2">♫</div>
        <div class="absolute bottom-20 left-[18%] text-[#C6A044]/30 text-xl select-none animate-note-3">♪</div>
        <div class="absolute top-24 right-[12%] text-[#C6A044]/30 text-3xl select-none animate-note-2">♬</div>
        <div class="absolute bottom-32 right-[8%] text-[#C6A044]/25 text-2xl select-none animate-note-1">♩</div>
        <div class="absolute top-2/3 right-[15%] text-[#C6A044]/30 text-4xl select-none animate-note-3">𝄞</div>
    </div>

    <!-- ============================================================
         TOP BAR: BRAND BADGE & THEME TOGGLE
         ============================================================ -->
    <header class="relative z-20 w-full max-w-7xl mx-auto px-4 sm:px-6 py-4 flex items-center justify-between">
        <a href="{{ url('/') }}" class="inline-flex items-center gap-2.5 group">
            <img src="{{ asset('logo.jpg') }}" alt="Symphony Logo" class="w-9 h-9 rounded-full border border-[#C6A044]/40 shadow-sm group-hover:scale-105 transition">
            <div class="text-left">
                <span class="block text-xs font-black tracking-widest text-[#C6A044] uppercase font-mono leading-none">SYMPHONY</span>
                <span class="block text-[9px] font-bold text-white/60 tracking-wider uppercase leading-tight">SMK Al Wasliyah Perdagangan</span>
            </div>
        </a>

        <div class="flex items-center gap-2.5">
            <!-- Back to Home Button -->
            <a href="{{ url('/') }}" 
               class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full border border-white/20 bg-white/10 hover:bg-white/20 text-white text-xs font-semibold backdrop-blur-md transition shadow-xs group">
                <svg class="w-3.5 h-3.5 text-[#C6A044] group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                <span class="hidden sm:inline">Kembali ke Beranda</span>
                <span class="sm:hidden">Beranda</span>
            </a>

            <!-- Dark/Light Mode Switcher -->
            <button type="button"
                    @click="toggleTheme(); isDark = document.documentElement.classList.contains('dark')"
                    class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full border border-white/15 bg-white/10 dark:bg-white/5 hover:bg-white/20 text-white text-xs font-semibold backdrop-blur-md transition shadow-xs"
                    title="Ganti Mode Gelap / Terang">
                <template x-if="isDark">
                    <span class="flex items-center gap-1.5 text-amber-300">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                        <span class="text-[11px] font-medium hidden sm:inline">Terang</span>
                    </span>
                </template>
                <template x-if="!isDark">
                    <span class="flex items-center gap-1.5 text-blue-200">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        <span class="text-[11px] font-medium hidden sm:inline">Gelap</span>
                    </span>
                </template>
            </button>
        </div>
    </header>

    <!-- ============================================================
         MAIN CONTENT AREA
         ============================================================ -->
    <main class="relative z-10 flex-1 flex items-center justify-center w-full max-w-7xl mx-auto px-4 sm:px-6 py-6 sm:py-10">
        @yield('content')
    </main>

    <!-- ============================================================
         FOOTER
         ============================================================ -->
    <footer class="relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-6 py-4 text-center">
        <p class="text-[11px] text-white/50 tracking-wide font-medium">
            &copy; 2026 <strong class="text-white/80 font-bold">Symphony Marching Band</strong> &bull; SMK Al Wasliyah Perdagangan &bull; Versi 2.4.0
        </p>
    </footer>

    @stack('scripts')
</body>
</html>
