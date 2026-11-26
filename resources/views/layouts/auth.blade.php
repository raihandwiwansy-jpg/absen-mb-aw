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

    <style>
        /* ── Auth Portal Background ───────────────────────────────── */
        @keyframes authSpinCW  { to { transform: translate(-50%,-50%) rotate(360deg); } }
        @keyframes authSpinCCW { to { transform: translate(-50%,-50%) rotate(-360deg); } }
        @keyframes authGlow    { 0%,100%{opacity:.06;}50%{opacity:.16;} }
        @keyframes authFloat   {
            0%  {transform:translate3d(0,0,0) scale(.92);opacity:0;}
            18% {opacity:1;}82%{opacity:1;}
            100%{transform:translate3d(14px,-110px,0) scale(1.06);opacity:0;}
        }
        @keyframes authFloat2  {
            0%  {transform:translate3d(0,0,0);opacity:0;}
            22% {opacity:1;}78%{opacity:1;}
            100%{transform:translate3d(-18px,-130px,0);opacity:0;}
        }
        @keyframes authStar    { 0%,100%{opacity:.06;}50%{opacity:.5;} }

        .auth-portal-wrap { position:fixed;top:50%;left:50%;pointer-events:none;z-index:0; }
        .auth-ring {
            position:absolute;border-radius:50%;border:1px solid rgba(198,160,68,.09);
            will-change:transform;top:0;left:0;
        }
        .auth-ring-1{width:300px;height:300px;margin:-150px 0 0 -150px;animation:authSpinCW 26s linear infinite;border-color:rgba(198,160,68,.13);}
        .auth-ring-1::before,.auth-ring-1::after{content:'';position:absolute;width:5px;height:5px;border-radius:50%;background:#C6A044;top:50%;left:-2.5px;margin-top:-2.5px;box-shadow:0 0 6px #C6A044;}
        .auth-ring-1::after{left:auto;right:-2.5px;background:#E5C368;}
        .auth-ring-2{width:500px;height:500px;margin:-250px 0 0 -250px;animation:authSpinCCW 40s linear infinite;border-color:rgba(198,160,68,.06);}
        .auth-ring-3{width:720px;height:720px;margin:-360px 0 0 -360px;animation:authSpinCW 65s linear infinite;border-color:rgba(255,255,255,.035);}

        .auth-amb { position:fixed;border-radius:50%;pointer-events:none;will-change:opacity;animation:authGlow 5s ease-in-out infinite; }
        .auth-amb-1{width:420px;height:420px;top:-100px;left:-100px;background:radial-gradient(circle,rgba(198,160,68,.28) 0%,transparent 65%);}
        .auth-amb-2{width:380px;height:380px;bottom:-100px;right:-80px;background:radial-gradient(circle,rgba(28,52,120,.45) 0%,transparent 65%);animation-delay:2.5s;}

        .auth-fi-a{animation:authFloat  8s ease-in-out infinite;will-change:transform,opacity;}
        .auth-fi-b{animation:authFloat2 9.5s ease-in-out 1.6s infinite;will-change:transform,opacity;}
        .auth-fi-c{animation:authFloat  11s ease-in-out 3.2s infinite;will-change:transform,opacity;}
        .auth-star{animation:authStar 3.5s ease-in-out infinite;will-change:opacity;}

        @media(prefers-reduced-motion:reduce){
            .auth-ring,.auth-amb,.auth-fi-a,.auth-fi-b,.auth-fi-c,.auth-star{animation:none!important;}
        }
    </style>

    @stack('styles')
</head>
<body class="min-h-full auth-bg text-gray-800 dark:text-gray-100 flex flex-col justify-between selection:bg-[#C6A044] selection:text-[#101D3B] relative overflow-x-hidden"
      x-data="{ isDark: document.documentElement.classList.contains('dark') }">

    <!-- ============================================================
         BACKGROUND — Portal rings + glows + instruments + stars
         ============================================================ -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0" aria-hidden="true">

        <!-- Dot grid -->
        <div class="absolute inset-0" style="background-image:radial-gradient(circle,rgba(255,255,255,.04) 1px,transparent 1px);background-size:28px 28px;"></div>

        <!-- Ambient glows -->
        <div class="auth-amb auth-amb-1"></div>
        <div class="auth-amb auth-amb-2"></div>

        <!-- Portal rings (centered) -->
        <div class="auth-portal-wrap">
            <div class="auth-ring auth-ring-1"></div>
            <div class="auth-ring auth-ring-2"></div>
            <div class="auth-ring auth-ring-3"></div>
        </div>

        <!-- Floating instruments -->
        <div class="absolute top-[14%] left-[5%]  text-3xl select-none auth-fi-a opacity-25">🎺</div>
        <div class="absolute top-[42%] left-[10%] text-4xl select-none auth-fi-b opacity-20">🥁</div>
        <div class="absolute top-[70%] left-[7%]  text-2xl select-none auth-fi-c opacity-18">🎷</div>
        <div class="absolute top-[18%] right-[7%] text-4xl select-none auth-fi-b opacity-22">🎻</div>
        <div class="absolute top-[55%] right-[9%] text-3xl select-none auth-fi-a opacity-18">𝄞</div>
        <div class="absolute top-[80%] right-[6%] text-2xl select-none auth-fi-c opacity-20">♬</div>

        <!-- Star dots -->
        <div class="auth-star absolute w-1   h-1   rounded-full bg-white      top-[12%] left-[30%]" style="animation-delay:.5s"></div>
        <div class="auth-star absolute w-1.5 h-1.5 rounded-full bg-amber-200 top-[35%] left-[55%]" style="animation-delay:1.3s"></div>
        <div class="auth-star absolute w-1   h-1   rounded-full bg-white      top-[60%] left-[25%]" style="animation-delay:2.1s"></div>
        <div class="auth-star absolute w-1   h-1   rounded-full bg-white      top-[80%] left-[65%]" style="animation-delay:3.4s"></div>
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
