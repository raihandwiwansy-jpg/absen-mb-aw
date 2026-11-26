<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Symphony Marching Band') }} — Informasi Jadwal & Presensi</title>

    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="{{ asset('logo.jpg') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Pre-compiled Vite Assets -->
    @if (file_exists(public_path('build/manifest.json')))
        @php
            $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
            $cssFile = $manifest['resources/css/app.css']['file'] ?? null;
            $jsFile  = $manifest['resources/js/app.js']['file'] ?? null;
        @endphp
        @if($cssFile)
            <link rel="stylesheet" href="{{ asset('build/' . $cssFile) }}">
        @endif
        @if($jsFile)
            <script type="module" src="{{ asset('build/' . $jsFile) }}"></script>
        @endif
    @endif

    <style>
        /* ── Design Tokens ─────────────────────────────────────────── */
        :root {
            --navy:   #070D1C;
            --navy-s: #0F1D3B;
            --navy-c: #132448;
            --gold:   #C6A044;
            --gold-l: #E5C368;
            --gold-b: #F5D37A;
        }

        /* ── Base ──────────────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; }

        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            background-color: var(--navy);
            color: #F8FAFC;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        .font-heading  { font-family: 'Outfit', sans-serif; }
        .font-mono-num { font-family: 'JetBrains Mono', monospace; }

        /* ── Gradient Background ───────────────────────────────────── */
        .aurora-bg {
            background-color: #060C1A;
            background-image:
                radial-gradient(ellipse 60% 40% at 10% 20%, rgba(198,160,68,.13) 0%, transparent 55%),
                radial-gradient(ellipse 50% 35% at 90% 15%, rgba(28,52,102,.55) 0%, transparent 50%),
                radial-gradient(ellipse 70% 50% at 50% 90%, rgba(198,160,68,.07) 0%, transparent 55%),
                linear-gradient(180deg, #060C1A 0%, #0C1830 55%, #060C1A 100%);
        }

        /* ── Gold Gradient Text ────────────────────────────────────── */
        .text-gold-gradient {
            background: linear-gradient(135deg, #FFF6D8 0%, #E5C368 45%, #C6A044 80%, #FFF2C2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* ── Glass Card ────────────────────────────────────────────── */
        .glass-card {
            background: rgba(14,26,54,.9);
            border: 1px solid rgba(198,160,68,.22);
            box-shadow: 0 16px 40px -12px rgba(0,0,0,.65);
        }
        @supports (backdrop-filter: blur(10px)) {
            .glass-card {
                background: rgba(14,26,54,.78);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
            }
        }

        /* ── Dot Grid ──────────────────────────────────────────────── */
        .dot-grid {
            background-size: 28px 28px;
            background-image:
                radial-gradient(circle, rgba(255,255,255,.045) 1px, transparent 1px);
        }

        /* ── Portal Ring Animations (GPU only: transform) ──────────── */
        @keyframes spinCW  { to { transform: translate(-50%,-50%) rotate(360deg); } }
        @keyframes spinCCW { to { transform: translate(-50%,-50%) rotate(-360deg); } }

        .portal-wrap {
            position: fixed;
            top: 50%;
            left: 50%;
            pointer-events: none;
            z-index: 0;
        }

        .p-ring {
            position: absolute;
            border-radius: 50%;
            border: 1px solid rgba(198,160,68,.10);
            will-change: transform;
            top: 0; left: 0;
        }
        .p-ring-1 {
            width: 380px; height: 380px;
            margin: -190px 0 0 -190px;
            animation: spinCW 28s linear infinite;
            border-color: rgba(198,160,68,.12);
        }
        .p-ring-2 {
            width: 580px; height: 580px;
            margin: -290px 0 0 -290px;
            animation: spinCCW 42s linear infinite;
            border-color: rgba(198,160,68,.07);
        }
        .p-ring-3 {
            width: 800px; height: 800px;
            margin: -400px 0 0 -400px;
            animation: spinCW 65s linear infinite;
            border-color: rgba(255,255,255,.04);
        }
        .p-ring-4 {
            width: 1050px; height: 1050px;
            margin: -525px 0 0 -525px;
            animation: spinCCW 90s linear infinite;
            border-color: rgba(255,255,255,.025);
        }

        /* Accent dots on ring-1 */
        .p-ring-1::before,
        .p-ring-1::after {
            content: '';
            position: absolute;
            width: 6px; height: 6px;
            border-radius: 50%;
            background: var(--gold);
            top: 50%; left: -3px;
            margin-top: -3px;
            box-shadow: 0 0 8px var(--gold);
        }
        .p-ring-1::after { left: auto; right: -3px; background: var(--gold-l); }

        /* ── Ambient Glow ──────────────────────────────────────────── */
        @keyframes glowPulse {
            0%,100% { opacity: .07; }
            50%      { opacity: .17; }
        }
        .amb-glow {
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            will-change: opacity;
            animation: glowPulse 5s ease-in-out infinite;
        }
        .amb-gold {
            width: 480px; height: 480px;
            top: -120px; left: -120px;
            background: radial-gradient(circle, rgba(198,160,68,.30) 0%, transparent 65%);
        }
        .amb-blue {
            width: 420px; height: 420px;
            bottom: -120px; right: -100px;
            background: radial-gradient(circle, rgba(28,52,120,.45) 0%, transparent 65%);
            animation-delay: 2.5s;
        }

        /* ── Floating Instrument Notes ─────────────────────────────── */
        @keyframes floatA {
            0%   { transform: translate3d(0,0,0) scale(.92); opacity: 0; }
            18%  { opacity: 1; }
            82%  { opacity: 1; }
            100% { transform: translate3d(18px,-130px,0) scale(1.08); opacity: 0; }
        }
        @keyframes floatB {
            0%   { transform: translate3d(0,0,0) scale(1); opacity: 0; }
            22%  { opacity: 1; }
            78%  { opacity: 1; }
            100% { transform: translate3d(-22px,-155px,0) scale(1.12); opacity: 0; }
        }
        @keyframes floatC {
            0%   { transform: translate3d(0,0,0) rotate(-5deg); opacity: 0; }
            20%  { opacity: 1; }
            80%  { opacity: 1; }
            100% { transform: translate3d(10px,-120px,0) rotate(8deg); opacity: 0; }
        }

        .fi-a { animation: floatA 8s ease-in-out infinite;      will-change: transform, opacity; }
        .fi-b { animation: floatB 9.5s ease-in-out 1.6s infinite; will-change: transform, opacity; }
        .fi-c { animation: floatC 11s ease-in-out 3.2s infinite; will-change: transform, opacity; }

        /* ── Star Blink ────────────────────────────────────────────── */
        @keyframes starBlink {
            0%,100% { opacity: .08; }
            50%      { opacity: .55; }
        }
        .star { animation: starBlink 3.5s ease-in-out infinite; will-change: opacity; }

        /* ── Card Pulse Border ─────────────────────────────────────── */
        @keyframes cardPulse {
            0%,100% { border-color: rgba(198,160,68,.28); }
            50%      { border-color: rgba(198,160,68,.65); box-shadow: 0 0 28px rgba(198,160,68,.18); }
        }
        .card-pulse { animation: cardPulse 3.8s ease-in-out infinite; }

        /* ── Entrance Fade ─────────────────────────────────────────── */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-up   { animation: fadeUp .6s ease both; }
        .delay-100 { animation-delay: .1s; }
        .delay-200 { animation-delay: .2s; }
        .delay-300 { animation-delay: .3s; }

        /* ── Reduced Motion ────────────────────────────────────────── */
        @media (prefers-reduced-motion: reduce) {
            .p-ring, .amb-glow, .fi-a, .fi-b, .fi-c,
            .star, .card-pulse, .fade-up { animation: none !important; }
        }

        /* ── Utility ───────────────────────────────────────────────── */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>
<body class="min-h-screen aurora-bg text-slate-100 relative selection:bg-[#C6A044] selection:text-[#070D1C]"
      x-data="{ mobileMenuOpen: false }">

    {{-- ================================================================
         BACKGROUND LAYER — Portal rings + glows + instruments + stars
         ================================================================ --}}
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0" aria-hidden="true">

        <!-- Dot Grid -->
        <div class="absolute inset-0 dot-grid"></div>

        <!-- Ambient glows -->
        <div class="amb-glow amb-gold z-0"></div>
        <div class="amb-glow amb-blue  z-0"></div>

        <!-- Portal rings (centered) -->
        <div class="portal-wrap">
            <div class="p-ring p-ring-1"></div>
            <div class="p-ring p-ring-2"></div>
            <div class="p-ring p-ring-3"></div>
            <div class="p-ring p-ring-4"></div>
        </div>

        <!-- Floating instruments -->
        <div class="absolute top-[14%] left-[5%]  text-3xl select-none fi-a opacity-30">🎺</div>
        <div class="absolute top-[38%] left-[12%] text-4xl select-none fi-b opacity-25">🥁</div>
        <div class="absolute top-[66%] left-[6%]  text-3xl select-none fi-c opacity-20">🎷</div>
        <div class="absolute top-[18%] right-[7%] text-4xl select-none fi-b opacity-25">🎻</div>
        <div class="absolute top-[48%] right-[10%]text-3xl select-none fi-a opacity-20">🎹</div>
        <div class="absolute top-[74%] right-[5%] text-5xl select-none fi-b opacity-28">𝄞</div>
        <div class="absolute top-[8%]  left-[44%] text-2xl select-none fi-c opacity-18">♬</div>
        <div class="absolute top-[82%] left-[38%] text-3xl select-none fi-a opacity-20">🎼</div>
        <div class="absolute top-[55%] left-[48%] text-2xl select-none fi-b opacity-15">♩</div>
        <div class="absolute top-[30%] right-[28%]text-xl select-none fi-c opacity-15">🎵</div>

        <!-- Star dots -->
        <div class="star absolute w-1   h-1   rounded-full bg-white         top-[12%] left-[28%]" style="animation-delay:.4s"></div>
        <div class="star absolute w-1.5 h-1.5 rounded-full bg-amber-200    top-[32%] left-[52%]" style="animation-delay:1.1s"></div>
        <div class="star absolute w-1   h-1   rounded-full bg-white         top-[58%] left-[22%]" style="animation-delay:2.2s"></div>
        <div class="star absolute w-1   h-1   rounded-full bg-white         top-[42%] right-[33%]" style="animation-delay:3s"></div>
        <div class="star absolute w-1.5 h-1.5 rounded-full bg-amber-100    top-[78%] left-[58%]" style="animation-delay:1.7s"></div>
        <div class="star absolute w-1   h-1   rounded-full bg-white         top-[22%] left-[72%]" style="animation-delay:.8s"></div>
        <div class="star absolute w-1   h-1   rounded-full bg-white         top-[88%] left-[18%]" style="animation-delay:4s"></div>
    </div>

    {{-- ================================================================
         TOP NAVIGATION BAR
         ================================================================ --}}
    <header class="sticky top-0 z-50 w-full bg-[#070D1C]/96 border-b border-[#C6A044]/20 backdrop-blur-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 sm:h-18">

                <!-- Brand -->
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <img src="{{ asset('logo.jpg') }}"
                         alt="Symphony Logo"
                         class="w-9 h-9 sm:w-10 sm:h-10 rounded-full object-cover border-2 border-[#C6A044] shadow-md group-hover:scale-105 transition-transform duration-300">
                    <div class="flex flex-col leading-none">
                        <span class="font-heading font-extrabold text-sm sm:text-base tracking-widest text-gold-gradient uppercase">SYMPHONY</span>
                        <span class="text-[9px] sm:text-[10px] font-semibold text-slate-400 tracking-wide mt-0.5">SMK AL WASLIYAH PERDAGANGAN</span>
                    </div>
                </a>

                <!-- Desktop Nav -->
                <nav class="hidden md:flex items-center gap-5 text-xs sm:text-sm font-medium">
                    <a href="#jadwal-utama" class="text-slate-300 hover:text-[#C6A044] transition-colors flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-[#C6A044]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Jadwal & Dresscode
                    </a>
                    <a href="#divisi-seksi" class="text-slate-300 hover:text-[#C6A044] transition-colors flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-[#C6A044]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        Divisi Marching Band
                    </a>
                </nav>

                <!-- CTA + Mobile Toggle -->
                <div class="flex items-center gap-2">
                    @auth
                        @php
                            $user = auth()->user();
                            $isMember = $user->hasRole('member') && !$user->hasAnyRole(['super_admin','administrator','coach','attendance_officer']);
                            $targetRoute = $isMember ? route('portal.dashboard') : route('dashboard');
                        @endphp
                        <a href="{{ $targetRoute }}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl font-heading font-bold text-xs text-[#070D1C] bg-[#E5C368] hover:bg-white shadow-md transition-colors">
                            <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                            {{ $isMember ? 'Portal Anggota' : 'Dashboard' }}
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center gap-1.5 px-3.5 py-2 sm:px-4 sm:py-2.5 rounded-xl font-heading font-bold text-xs sm:text-sm text-[#070D1C] bg-gradient-to-r from-[#E5C368] to-[#C6A044] hover:brightness-110 shadow-md transition-all">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                            Masuk Sistem
                        </a>
                    @endauth

                    <!-- Mobile burger -->
                    <button type="button"
                            @click="mobileMenuOpen = !mobileMenuOpen"
                            class="md:hidden p-2 rounded-lg text-slate-300 hover:text-white hover:bg-white/10 transition"
                            aria-label="Menu">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            <path x-show="mobileMenuOpen"  stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div x-show="mobileMenuOpen" x-transition class="md:hidden py-3 border-t border-white/10 space-y-1">
                <a href="#jadwal-utama"  @click="mobileMenuOpen=false" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-200 hover:bg-white/10">📅 Jadwal Latihan & Seragam</a>
                <a href="#divisi-seksi" @click="mobileMenuOpen=false" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-200 hover:bg-white/10">🎺 Divisi & Data Master</a>
                @auth
                    <a href="{{ route('attendance.scan') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-emerald-400 hover:bg-white/10">📸 Scan QR Presensi</a>
                @endauth
            </div>
        </div>
    </header>

    {{-- ================================================================
         ACTIVE SESSION BANNER
         ================================================================ --}}
    @if($activeSession)
    <div class="relative z-30 bg-emerald-950 border-b border-emerald-500/40 px-4 py-2.5">
        <div class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-2 text-center sm:text-left">
            <div class="flex items-center gap-2.5">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                <span class="font-bold text-emerald-300 text-xs sm:text-sm">
                    SESI PRESENSI AKTIF: {{ $activeSession->practiceSchedule->title ?? 'Latihan' }}
                </span>
            </div>
            <a href="{{ route('attendance.scan') }}"
               class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-lg text-xs font-bold bg-emerald-400 hover:bg-emerald-300 text-[#070D1C] transition-colors">
                Scan QR Sekarang &rarr;
            </a>
        </div>
    </div>
    @endif

    {{-- ================================================================
         HERO SECTION
         ================================================================ --}}
    <section class="relative z-10 pt-10 pb-5 sm:pt-14 sm:pb-8 text-center px-4">
        <div class="max-w-3xl mx-auto space-y-3 fade-up">
            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-white/5 border border-[#C6A044]/30 text-amber-200 text-[10px] sm:text-xs font-bold uppercase tracking-wider font-mono-num">
                <span class="w-2 h-2 rounded-full bg-[#C6A044] animate-pulse"></span>
                <span>PAPAN INFORMASI RESMI</span>
            </div>

            <h1 class="font-heading font-black text-2xl sm:text-4xl lg:text-5xl tracking-tight text-white leading-tight">
                Jadwal Latihan &amp; <span class="text-gold-gradient">Dresscode</span>
            </h1>

            <p class="text-slate-400 text-xs sm:text-sm max-w-xl mx-auto leading-relaxed fade-up delay-100">
                Pantau waktu latihan, seragam yang harus dipakai, tempat latihan, dan hitung mundur presensi secara langsung.
            </p>
        </div>
    </section>

    {{-- ================================================================
         MAIN FEATURED SCHEDULE CARD
         ================================================================ --}}
    <main id="jadwal-utama" class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 py-4 sm:py-6">

        @if($featuredSchedule)
            @php
                $isTomorrow   = $featuredSchedule->date->isTomorrow();
                $isToday      = $featuredSchedule->date->isToday();
                $scheduleDate = $featuredSchedule->date;
                $targetIso    = $scheduleDate->format('Y-m-d') . 'T' . $featuredSchedule->start_time;
            @endphp

            <div class="glass-card card-pulse rounded-2xl sm:rounded-3xl p-5 sm:p-8 fade-up delay-200"
                 x-data="{
                     targetTime: new Date('{{ $targetIso }}').getTime(),
                     days: '00', hours: '00', minutes: '00', seconds: '00',
                     hasStarted: false,
                     updateCountdown() {
                         const diff = this.targetTime - Date.now();
                         if (diff <= 0) {
                             this.hasStarted = true;
                             this.days = this.hours = this.minutes = this.seconds = '00';
                             return;
                         }
                         this.hasStarted = false;
                         const d = Math.floor(diff / 86400000);
                         const h = Math.floor((diff % 86400000) / 3600000);
                         const m = Math.floor((diff % 3600000)  / 60000);
                         const s = Math.floor((diff % 60000)    / 1000);
                         this.days    = String(d).padStart(2,'0');
                         this.hours   = String(h).padStart(2,'0');
                         this.minutes = String(m).padStart(2,'0');
                         this.seconds = String(s).padStart(2,'0');
                     }
                 }"
                 x-init="updateCountdown(); setInterval(() => updateCountdown(), 1000)">

                <!-- Badge Row -->
                <div class="flex flex-wrap items-center justify-between gap-2.5 pb-4 border-b border-white/10">
                    <div class="flex items-center gap-2 flex-wrap">
                        @if($isTomorrow)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500 text-slate-950 font-heading font-black text-[10px] sm:text-xs uppercase tracking-wide">🌟 JADWAL BESOK</span>
                        @elseif($isToday)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500 text-slate-950 font-heading font-black text-[10px] sm:text-xs uppercase tracking-wide">🔥 HARI INI</span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#C6A044] text-slate-950 font-heading font-black text-[10px] sm:text-xs uppercase tracking-wide">📅 JADWAL TERDEKAT</span>
                        @endif

                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                            {{ $featuredSchedule->type === 'regular'     ? 'bg-blue-500/20  text-blue-300  border border-blue-500/40'  : '' }}
                            {{ $featuredSchedule->type === 'additional'  ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40' : '' }}
                            {{ $featuredSchedule->type === 'competition' ? 'bg-rose-500/20  text-rose-300  border border-rose-500/40'  : '' }}">
                            {{ $featuredSchedule->type_label }}
                        </span>
                    </div>

                    <div class="flex items-center gap-1.5 text-xs font-semibold text-slate-300">
                        <svg class="w-3.5 h-3.5 text-[#C6A044]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ $scheduleDate->translatedFormat('l, d F Y') }}
                    </div>
                </div>

                <!-- Title -->
                <div class="pt-4 pb-3">
                    <h2 class="font-heading font-black text-xl sm:text-2xl lg:text-3xl text-white tracking-tight leading-snug">
                        {{ $featuredSchedule->title }}
                    </h2>
                    @if($featuredSchedule->description)
                        <p class="text-slate-400 text-xs sm:text-sm mt-1.5 leading-relaxed">{{ $featuredSchedule->description }}</p>
                    @endif
                </div>

                <!-- 3 Info Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3 my-3">

                    <!-- Jam -->
                    <div class="rounded-xl bg-white/5 border border-white/10 p-4 sm:p-5">
                        <div class="flex items-center gap-1.5 text-[#E5C368] text-[10px] font-bold uppercase tracking-wider font-mono-num mb-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            JAM LATIHAN
                        </div>
                        <div class="font-mono-num font-black text-xl sm:text-2xl text-amber-200">
                            {{ substr($featuredSchedule->start_time,0,5) }} &mdash; {{ substr($featuredSchedule->end_time,0,5) }} <span class="text-sm font-bold">WIB</span>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-2">Hadir 15 menit sebelum mulai</p>
                    </div>

                    <!-- Dresscode (highlighted) -->
                    <div class="rounded-xl bg-[#C6A044]/12 border-2 border-[#C6A044] p-4 sm:p-5 shadow-lg">
                        <div class="flex items-center gap-1.5 text-amber-300 text-[10px] font-bold uppercase tracking-wider font-mono-num mb-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 4h2a2 2 0 012 2v3a1 1 0 01-.6.9l-2.4 1.1a1 1 0 00-.6.9V20a1 1 0 01-1 1H6a1 1 0 01-1-1v-8.1a1 1 0 00-.6-.9L2 9.9A1 1 0 011.4 9V6a2 2 0 012-2h2m11 0a4 4 0 00-8 0m8 0a4 4 0 01-8 0"/></svg>
                            DRESSCODE / SERAGAM
                        </div>
                        <div class="font-heading font-black text-lg sm:text-xl text-white leading-snug">
                            {{ $featuredSchedule->dress_code ?: 'Kaos Latihan Symphony & Training Gelap' }}
                        </div>
                        <p class="text-[11px] text-amber-200/80 mt-2">Wajib bersepatu & berpakaian rapi</p>
                    </div>

                    <!-- Lokasi -->
                    <div class="rounded-xl bg-white/5 border border-white/10 p-4 sm:p-5">
                        <div class="flex items-center gap-1.5 text-blue-300 text-[10px] font-bold uppercase tracking-wider font-mono-num mb-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            LOKASI LATIHAN
                        </div>
                        <div class="font-heading font-bold text-base sm:text-lg text-slate-100 leading-snug">
                            {{ $featuredSchedule->location }}
                        </div>
                        <p class="text-[11px] text-slate-400 mt-2">
                            Petugas: <strong class="text-slate-200">{{ $featuredSchedule->officer->name ?? 'Staff Umum' }}</strong>
                        </p>
                    </div>
                </div>

                <!-- Countdown -->
                <div class="mt-4 pt-4 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto text-center sm:text-left">
                        <span class="text-[10px] font-bold text-slate-400 uppercase font-mono-num whitespace-nowrap">
                            <span x-show="!hasStarted">HITUNG MUNDUR MENUJU LATIHAN:</span>
                            <span x-show="hasStarted" class="text-emerald-400">STATUS SAAT INI:</span>
                        </span>

                        <div class="flex items-center gap-1.5" x-show="!hasStarted">
                            @foreach(['days' => 'Hari', 'hours' => 'Jam', 'minutes' => 'Menit'] as $key => $label)
                            <div class="bg-black/40 border border-white/15 rounded-lg px-2.5 py-1 text-center min-w-[46px]">
                                <span class="font-mono-num font-black text-lg text-amber-300" x-text="{{ $key }}">00</span>
                                <span class="block text-[8px] text-slate-400 uppercase">{{ $label }}</span>
                            </div>
                            <span class="font-bold text-[#C6A044] text-sm">:</span>
                            @endforeach
                            <div class="bg-[#C6A044]/20 border border-[#C6A044]/40 rounded-lg px-2.5 py-1 text-center min-w-[46px]">
                                <span class="font-mono-num font-black text-lg text-amber-200" x-text="seconds">00</span>
                                <span class="block text-[8px] text-amber-200 uppercase">Detik</span>
                            </div>
                        </div>

                        <div x-show="hasStarted" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-emerald-500/20 text-emerald-300 text-xs font-bold border border-emerald-500/30">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                            Latihan Sedang Berlangsung
                        </div>
                    </div>

                    <div class="w-full sm:w-auto flex justify-end">
                        @auth
                            <a href="{{ route('attendance.scan') }}"
                               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl font-heading font-bold text-xs sm:text-sm text-[#070D1C] bg-[#C6A044] hover:bg-amber-300 transition-colors shadow-md">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                                Scan QR Presensi
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                               class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl font-heading font-bold text-xs sm:text-sm text-[#070D1C] bg-gradient-to-r from-[#E5C368] to-[#C6A044] hover:brightness-110 transition-all shadow-md">
                                Masuk Akun Anggota &rarr;
                            </a>
                        @endauth
                    </div>
                </div>
            </div>

        @else
            <div class="glass-card rounded-2xl p-10 text-center max-w-md mx-auto fade-up delay-200">
                <span class="text-5xl block mb-3">🎺</span>
                <h3 class="font-heading font-bold text-lg text-white">Belum Ada Jadwal Latihan</h3>
                <p class="text-slate-400 text-xs mt-2">Jadwal terbaru akan muncul di sini setelah dibuat oleh pengurus.</p>
            </div>
        @endif

    </main>

    {{-- ================================================================
         UPCOMING SCHEDULES
         ================================================================ --}}
    @if(isset($upcomingSchedules) && $upcomingSchedules->count() > 0)
    <section class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 py-5">
        <h2 class="text-[10px] font-bold text-[#C6A044] uppercase tracking-widest font-mono-num mb-3 flex items-center gap-2">
            <span class="w-4 h-px bg-[#C6A044]/50 inline-block"></span>
            AGENDA LATIHAN MENDATANG LAINNYA
            <span class="w-4 h-px bg-[#C6A044]/50 inline-block"></span>
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
            @foreach($upcomingSchedules as $sch)
            <div class="glass-card rounded-xl p-4 flex flex-col justify-between hover:border-[#C6A044]/40 transition-colors">
                <div>
                    <div class="flex items-center justify-between text-[10px] text-slate-400 mb-2">
                        <span class="px-2 py-0.5 rounded bg-white/10 text-slate-200 font-bold">{{ $sch->type_label }}</span>
                        <span>{{ $sch->date->translatedFormat('d M Y') }}</span>
                    </div>
                    <h3 class="font-heading font-bold text-sm text-white line-clamp-2">{{ $sch->title }}</h3>
                    <div class="mt-3 space-y-1 text-xs">
                        <div class="text-amber-200 font-mono-num">⏰ {{ substr($sch->start_time,0,5) }} - {{ substr($sch->end_time,0,5) }} WIB</div>
                        <div class="text-slate-300">👕 {{ $sch->dress_code ?: 'Seragam Latihan' }}</div>
                        <div class="text-slate-400 text-[11px]">📍 {{ $sch->location }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    {{-- ================================================================
         STATS + DIVISIONS
         ================================================================ --}}
    <section id="divisi-seksi" class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 py-10">

        <!-- Stat Counters -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-8">
            <div class="glass-card rounded-xl p-4 text-center hover:border-amber-400/40 transition-colors">
                <span class="font-heading font-black text-2xl sm:text-3xl text-amber-300 block">{{ $totalMembers }}</span>
                <span class="text-[10px] uppercase font-bold text-slate-400 mt-0.5 block">Anggota Aktif</span>
            </div>
            <div class="glass-card rounded-xl p-4 text-center hover:border-[#C6A044]/40 transition-colors">
                <span class="font-heading font-black text-2xl sm:text-3xl text-[#C6A044] block">{{ $totalDivisions }}</span>
                <span class="text-[10px] uppercase font-bold text-slate-400 mt-0.5 block">Divisi Seksi</span>
            </div>
            <div class="glass-card rounded-xl p-4 text-center hover:border-amber-200/40 transition-colors">
                <span class="font-heading font-black text-2xl sm:text-3xl text-amber-200 block">{{ $totalInstruments }}</span>
                <span class="text-[10px] uppercase font-bold text-slate-400 mt-0.5 block">Instrumen Alat</span>
            </div>
            <div class="glass-card rounded-xl p-4 text-center hover:border-emerald-400/40 transition-colors">
                <span class="font-heading font-bold text-sm text-emerald-400 block mt-1 truncate">{{ $activePeriod->name ?? '2025/2026' }}</span>
                <span class="text-[10px] uppercase font-bold text-slate-400 mt-0.5 block">Periode Aktif</span>
            </div>
        </div>

        <!-- Divisions -->
        <h2 class="text-[10px] font-bold text-[#C6A044] uppercase tracking-widest font-mono-num mb-4 text-center flex items-center justify-center gap-2">
            <span class="w-8 h-px bg-[#C6A044]/40 inline-block"></span>
            SEKSI DIVISI SYMPHONY MARCHING BAND
            <span class="w-8 h-px bg-[#C6A044]/40 inline-block"></span>
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
            @forelse($divisions as $d)
            <div class="glass-card rounded-xl p-3.5 flex items-center gap-3 hover:border-[#C6A044]/35 transition-colors">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center text-xl shrink-0 bg-white/8"
                     style="border-left: 3px solid {{ $d->color ?? '#C6A044' }};">
                    @if(str_contains(strtolower($d->name), 'brass')) 🎺
                    @elseif(str_contains(strtolower($d->name), 'percussion')) 🥁
                    @elseif(str_contains(strtolower($d->name), 'woodwind')) 🎷
                    @elseif(str_contains(strtolower($d->name), 'color') || str_contains(strtolower($d->name), 'guard')) 🚩
                    @elseif(str_contains(strtolower($d->name), 'pit')) 🎹
                    @else 🎵
                    @endif
                </div>
                <div class="min-w-0 flex-1">
                    <h3 class="font-heading font-bold text-sm text-white truncate">{{ $d->name }}</h3>
                    <p class="text-[11px] text-slate-400 truncate">{{ $d->members_count ?? 0 }} Personel Terdaftar</p>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-6 text-xs text-slate-400">
                Divisi marching band akan segera dimuat.
            </div>
            @endforelse
        </div>
    </section>

    {{-- ================================================================
         FOOTER
         ================================================================ --}}
    <footer class="relative z-10 border-t border-white/8 bg-[#060C1A] py-8 px-4 text-center">
        <div class="max-w-5xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <div class="flex items-center gap-2.5">
                <img src="{{ asset('logo.jpg') }}" alt="Logo" class="w-7 h-7 rounded-full border border-[#C6A044]/40">
                <span class="font-semibold text-slate-400">Symphony Marching Band &bull; SMK Al Wasliyah Perdagangan</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-[#C6A044] text-base">♬</span>
                <span>&copy; {{ date('Y') }} Symphony MB &mdash; v2.5.0</span>
            </div>
        </div>
    </footer>

</body>
</html>
