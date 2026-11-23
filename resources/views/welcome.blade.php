<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Symphony Marching Band') }} — Informasi Jadwal & Presensi</title>

    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="{{ asset('logo.jpg') }}">

    <!-- Google Fonts Outfit & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700;800&display=swap" rel="stylesheet">

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

    <!-- Alpine.js (Lightweight reactive state) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Pre-compiled Vite Assets -->
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
    @endif

    <style>
        :root {
            --navy-base: #080E1E;
            --navy-surface: #0F1D3B;
            --navy-card: #132448;
            --gold: #C6A044;
            --gold-light: #E5C368;
            --gold-bright: #F5D37A;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--navy-base);
            color: #F8FAFC;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        .font-heading {
            font-family: 'Outfit', sans-serif;
        }

        .font-mono-num {
            font-family: 'JetBrains Mono', monospace;
        }

        /* Lightweight 60fps GPU-Accelerated Background (No heavy blur filters) */
        .aurora-canvas {
            background-color: #070D1C;
            background-image: 
                radial-gradient(circle at 10% 15%, rgba(198, 160, 68, 0.12) 0%, transparent 40%),
                radial-gradient(circle at 90% 25%, rgba(28, 52, 102, 0.5) 0%, transparent 45%),
                radial-gradient(circle at 50% 85%, rgba(198, 160, 68, 0.08) 0%, transparent 50%),
                linear-gradient(180deg, #070D1C 0%, #0D1933 50%, #070D1C 100%);
        }

        /* Gold Gradient Text */
        .text-gold-gradient {
            background: linear-gradient(135deg, #FFF6D8 0%, #E5C368 45%, #C6A044 80%, #FFF2C2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* High-contrast solid-glass card (Fast on low-end phones) */
        .fast-glass-card {
            background: rgba(16, 29, 59, 0.88);
            border: 1px solid rgba(198, 160, 68, 0.25);
            box-shadow: 0 12px 30px -10px rgba(0, 0, 0, 0.6);
        }

        @supports (backdrop-filter: blur(8px)) {
            .fast-glass-card {
                background: rgba(16, 29, 59, 0.78);
                backdrop-filter: blur(8px);
                -webkit-backdrop-filter: blur(8px);
            }
        }

        /* Optimized Hardware-Accelerated Floating Notes */
        @keyframes floatUp1 {
            0%   { transform: translate3d(0, 0, 0) scale(0.9); opacity: 0; }
            20%  { opacity: 0.28; }
            80%  { opacity: 0.28; }
            100% { transform: translate3d(20px, -140px, 0) scale(1.1); opacity: 0; }
        }
        @keyframes floatUp2 {
            0%   { transform: translate3d(0, 0, 0) scale(1); opacity: 0; }
            25%  { opacity: 0.32; }
            75%  { opacity: 0.32; }
            100% { transform: translate3d(-25px, -160px, 0) scale(1.15); opacity: 0; }
        }

        .note-float-1 {
            animation: floatUp1 7s infinite ease-in-out;
            will-change: transform, opacity;
        }
        .note-float-2 {
            animation: floatUp2 8.5s infinite ease-in-out 1.8s;
            will-change: transform, opacity;
        }

        /* Subtle pulse for hero card without lag */
        @keyframes cardPulseGlow {
            0%, 100% { border-color: rgba(198, 160, 68, 0.3); }
            50%      { border-color: rgba(198, 160, 68, 0.65); box-shadow: 0 0 25px rgba(198, 160, 68, 0.2); }
        }
        .card-glow-border {
            animation: cardPulseGlow 3.5s infinite ease-in-out;
        }

        /* Subtle Grid Pattern */
        .bg-mesh-grid {
            background-size: 32px 32px;
            background-image: 
                linear-gradient(to right, rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
        }

        /* Disable animation if user prefers reduced motion */
        @media (prefers-reduced-motion: reduce) {
            .note-float-1, .note-float-2, .card-glow-border {
                animation: none !important;
            }
        }
    </style>
</head>
<body class="min-h-screen aurora-canvas text-slate-100 relative selection:bg-[#C6A044] selection:text-[#070D1C]"
      x-data="{ 
          mobileMenuOpen: false
      }">

    <!-- ============================================================
         BACKGROUND LIGHTWEIGHT FLOATING MUSIC NOTES (GPU FRIENDLY)
         ============================================================ -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none z-0" aria-hidden="true">
        <div class="absolute inset-0 bg-mesh-grid"></div>

        <!-- 4 hardware-accelerated floating notes (super lightweight on mobile) -->
        <div class="absolute top-[20%] left-[8%] text-[#C6A044] text-3xl select-none note-float-1">♩</div>
        <div class="absolute top-[40%] left-[18%] text-amber-200 text-4xl select-none note-float-2">♫</div>
        <div class="absolute top-[25%] right-[10%] text-amber-300 text-4xl select-none note-float-2">♬</div>
        <div class="absolute top-[55%] right-[14%] text-[#C6A044] text-5xl select-none note-float-1">𝄞</div>
    </div>

    <!-- ============================================================
         TOP NAVIGATION BAR
         ============================================================ -->
    <header class="sticky top-0 z-50 w-full bg-[#080E1E]/95 border-b border-[#C6A044]/25">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-18 sm:h-20">

                <!-- Logo & School Brand -->
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <img src="{{ asset('logo.jpg') }}" 
                         alt="Symphony Logo" 
                         class="w-10 h-10 sm:w-11 sm:h-11 rounded-full object-cover border-2 border-[#C6A044] shadow-md group-hover:scale-105 transition-transform">
                    <div class="flex flex-col">
                        <span class="font-heading font-extrabold text-base sm:text-lg tracking-wider text-gold-gradient uppercase leading-none">
                            SYMPHONY
                        </span>
                        <span class="text-[10px] sm:text-xs font-semibold text-slate-300 tracking-wide mt-0.5">
                            SMK AL WASLIYAH PERDAGANGAN
                        </span>
                    </div>
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center gap-6 text-sm font-medium">
                    <a href="#jadwal-utama" class="text-slate-200 hover:text-[#C6A044] transition-colors flex items-center gap-1.5 py-1">
                        <svg class="w-4 h-4 text-[#C6A044]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        Jadwal & Dresscode
                    </a>
                    <a href="#divisi-seksi" class="text-slate-200 hover:text-[#C6A044] transition-colors flex items-center gap-1.5 py-1">
                        <svg class="w-4 h-4 text-[#C6A044]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        Divisi Marching Band
                    </a>
                </nav>

                <!-- Action Button -->
                <div class="flex items-center gap-2.5">
                    @auth
                        @php
                            $user = auth()->user();
                            $isMember = $user->hasRole('member') && !$user->hasAnyRole(['super_admin', 'administrator', 'coach', 'attendance_officer']);
                            $targetRoute = $isMember ? route('portal.dashboard') : route('dashboard');
                        @endphp
                        <a href="{{ $targetRoute }}" 
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl font-heading font-bold text-xs sm:text-sm text-[#070D1C] bg-[#E5C368] hover:bg-white shadow-md transition-colors">
                            <span class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></span>
                            <span>{{ $isMember ? 'Portal Anggota' : 'Dashboard' }}</span>
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}" 
                           class="inline-flex items-center gap-2 px-4 py-2 sm:px-5 sm:py-2.5 rounded-xl font-heading font-bold text-xs sm:text-sm text-[#070D1C] bg-gradient-to-r from-[#E5C368] to-[#C6A044] hover:brightness-110 shadow-md transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                            <span>Masuk Sistem</span>
                        </a>
                    @endauth

                    <!-- Mobile Menu Toggle -->
                    <button type="button" 
                            @click="mobileMenuOpen = !mobileMenuOpen" 
                            class="md:hidden p-2 rounded-lg text-slate-300 hover:text-white hover:bg-white/10"
                            aria-label="Menu">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path x-show="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            <path x-show="mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Dropdown -->
            <div x-show="mobileMenuOpen" 
                 x-transition
                 class="md:hidden py-3 border-t border-white/10 space-y-2">
                <a href="#jadwal-utama" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-200 hover:bg-white/10">
                    📅 Jadwal Latihan & Seragam
                </a>
                <a href="#divisi-seksi" @click="mobileMenuOpen = false" class="block px-3 py-2 rounded-lg text-sm font-medium text-slate-200 hover:bg-white/10">
                    🎺 Divisi & Data Master
                </a>
                @auth
                    <a href="{{ route('attendance.scan') }}" class="block px-3 py-2 rounded-lg text-sm font-medium text-emerald-400 hover:bg-white/10">
                        📸 Scan QR Presensi
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- ============================================================
         ACTIVE SESSION BANNER (Jika sedang dibuka presensi)
         ============================================================ -->
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
               class="inline-flex items-center gap-1.5 px-3.5 py-1 rounded-lg text-xs font-bold bg-emerald-400 hover:bg-emerald-300 text-[#070D1C]">
                Scan QR Sekarang &rarr;
            </a>
        </div>
    </div>
    @endif

    <!-- ============================================================
         HERO SECTION: CLEAN & DIRECT TITLE
         ============================================================ -->
    <section class="relative z-10 pt-8 pb-4 sm:pt-12 sm:pb-8 text-center px-4">
        <div class="max-w-3xl mx-auto space-y-3">
            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-white/5 border border-[#C6A044]/30 text-amber-200 text-xs font-bold uppercase tracking-wider font-mono-num">
                <span class="w-2 h-2 rounded-full bg-[#C6A044]"></span>
                <span>PAPAN INFORMASI RESMI</span>
            </div>
            <h1 class="font-heading font-black text-2xl sm:text-4xl lg:text-5xl tracking-tight text-white">
                Informasi Jadwal Latihan & <span class="text-gold-gradient">Dresscode</span>
            </h1>
            <p class="text-slate-300 text-xs sm:text-sm max-w-xl mx-auto leading-relaxed">
                Pantau waktu latihan, seragam yang harus dipakai, tempat latihan, dan hitung mundur presensi secara langsung.
            </p>
        </div>
    </section>

    <!-- ============================================================
         MAIN SHOWCASE CARD: FEATURED PRACTICE SCHEDULE
         ============================================================ -->
    <main id="jadwal-utama" class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 py-4 sm:py-8">

        @if($featuredSchedule)
            @php
                $isTomorrow = $featuredSchedule->date->isTomorrow();
                $isToday = $featuredSchedule->date->isToday();
                $scheduleDate = $featuredSchedule->date;
                $targetIso = $scheduleDate->format('Y-m-d') . 'T' . $featuredSchedule->start_time;
            @endphp

            <div class="fast-glass-card card-glow-border rounded-2xl sm:rounded-3xl p-5 sm:p-8"
                 x-data="{
                     targetTime: new Date('{{ $targetIso }}').getTime(),
                     days: '00',
                     hours: '00',
                     minutes: '00',
                     seconds: '00',
                     hasStarted: false,
                     updateCountdown() {
                         const now = new Date().getTime();
                         const diff = this.targetTime - now;

                         if (diff <= 0) {
                             this.hasStarted = true;
                             this.days = '00';
                             this.hours = '00';
                             this.minutes = '00';
                             this.seconds = '00';
                             return;
                         }

                         this.hasStarted = false;
                         const d = Math.floor(diff / (1000 * 60 * 60 * 24));
                         const h = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                         const m = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                         const s = Math.floor((diff % (1000 * 60)) / 1000);

                         this.days = String(d).padStart(2, '0');
                         this.hours = String(h).padStart(2, '0');
                         this.minutes = String(m).padStart(2, '0');
                         this.seconds = String(s).padStart(2, '0');
                     }
                 }"
                 x-init="updateCountdown(); setInterval(() => updateCountdown(), 1000)">

                <!-- Badge Status Header -->
                <div class="flex flex-wrap items-center justify-between gap-3 pb-5 border-b border-white/10">
                    <div class="flex items-center gap-2 flex-wrap">
                        @if($isTomorrow)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-500 text-slate-950 font-heading font-black text-xs sm:text-sm uppercase tracking-wide">
                                🌟 JADWAL LATIHAN BESOK
                            </span>
                        @elseif($isToday)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500 text-slate-950 font-heading font-black text-xs sm:text-sm uppercase tracking-wide">
                                🔥 JADWAL LATIHAN HARI INI
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#C6A044] text-slate-950 font-heading font-black text-xs sm:text-sm uppercase tracking-wide">
                                📅 JADWAL LATIHAN TERDEKAT
                            </span>
                        @endif

                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider
                            {{ $featuredSchedule->type === 'regular' ? 'bg-blue-500/20 text-blue-300 border border-blue-500/40' : '' }}
                            {{ $featuredSchedule->type === 'additional' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/40' : '' }}
                            {{ $featuredSchedule->type === 'competition' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40' : '' }}">
                            {{ $featuredSchedule->type_label }}
                        </span>
                    </div>

                    <div class="text-xs sm:text-sm font-bold text-slate-300 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-[#C6A044]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>{{ $scheduleDate->translatedFormat('l, d F Y') }}</span>
                    </div>
                </div>

                <!-- Judul Latihan -->
                <div class="pt-5 pb-4">
                    <h2 class="font-heading font-black text-xl sm:text-2xl lg:text-3xl text-white tracking-tight leading-snug">
                        {{ $featuredSchedule->title }}
                    </h2>
                    @if($featuredSchedule->description)
                        <p class="text-slate-300 text-xs sm:text-sm mt-1.5 leading-relaxed">
                            {{ $featuredSchedule->description }}
                        </p>
                    @endif
                </div>

                <!-- 3 KARTU INFORMASI UTAMA: JAM, DRESSCODE, LOKASI -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5 sm:gap-4 my-4">

                    <!-- 1. JAM LATIHAN -->
                    <div class="rounded-xl bg-white/5 border border-white/10 p-4 sm:p-5 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 text-[#E5C368] text-xs font-bold uppercase tracking-wider font-mono-num mb-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span>JAM BERAPA LATIHAN</span>
                            </div>
                            <div class="font-mono-num font-black text-xl sm:text-2xl text-amber-200 mt-2">
                                {{ substr($featuredSchedule->start_time, 0, 5) }} &mdash; {{ substr($featuredSchedule->end_time, 0, 5) }} WIB
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-2">
                            &bull; Harap hadir tepat waktu (15 menit sebelum mulai)
                        </p>
                    </div>

                    <!-- 2. DRESSCODE (BAJU APA) - DI-HIGHLIGHT -->
                    <div class="rounded-xl bg-[#C6A044]/15 border-2 border-[#C6A044] p-4 sm:p-5 flex flex-col justify-between shadow-md">
                        <div>
                            <div class="flex items-center gap-2 text-amber-300 text-xs font-bold uppercase tracking-wider font-mono-num mb-1">
                                <!-- T-shirt Icon -->
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 4h2a2 2 0 012 2v3a1 1 0 01-.6.9l-2.4 1.1a1 1 0 00-.6.9V20a1 1 0 01-1 1H6a1 1 0 01-1-1v-8.1a1 1 0 00-.6-.9L2 9.9A1 1 0 011.4 9V6a2 2 0 012-2h2m11 0a4 4 0 00-8 0m8 0a4 4 0 01-8 0"/></svg>
                                <span>PAKAI BAJU APA (DRESSCODE)</span>
                            </div>
                            <div class="font-heading font-black text-lg sm:text-xl text-white mt-1.5 leading-snug">
                                {{ $featuredSchedule->dress_code ?: 'Kaos Latihan Symphony & Training Gelap' }}
                            </div>
                        </div>
                        <p class="text-[11px] text-amber-200/90 mt-2">
                            &bull; Wajib bersepatu latihan & berpakaian rapi
                        </p>
                    </div>

                    <!-- 3. LOKASI LATIHAN -->
                    <div class="rounded-xl bg-white/5 border border-white/10 p-4 sm:p-5 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-2 text-blue-300 text-xs font-bold uppercase tracking-wider font-mono-num mb-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span>LOKASI LATIHAN</span>
                            </div>
                            <div class="font-heading font-bold text-base sm:text-lg text-slate-100 mt-1.5 leading-snug">
                                {{ $featuredSchedule->location }}
                            </div>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-2">
                            Petugas: <strong class="text-slate-200">{{ $featuredSchedule->officer->name ?? 'Staff Umum' }}</strong>
                        </p>
                    </div>

                </div>

                <!-- COUNTDOWN TIMER -->
                <div class="mt-4 pt-5 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between gap-4">

                    <!-- Kotak Hitung Mundur -->
                    <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto text-center sm:text-left">
                        <span class="text-[11px] font-bold text-slate-400 uppercase font-mono-num">
                            <span x-show="!hasStarted">HITUNG MUNDUR MENUJU LATIHAN:</span>
                            <span x-show="hasStarted" class="text-emerald-400">STATUS SAAT INI:</span>
                        </span>

                        <div class="flex items-center gap-1.5" x-show="!hasStarted">
                            <div class="bg-black/40 border border-white/15 rounded-lg px-2.5 py-1 text-center min-w-[48px]">
                                <span class="font-mono-num font-black text-lg text-amber-300" x-text="days">00</span>
                                <span class="block text-[8px] text-slate-400 uppercase">Hari</span>
                            </div>
                            <span class="font-bold text-[#C6A044]">:</span>
                            <div class="bg-black/40 border border-white/15 rounded-lg px-2.5 py-1 text-center min-w-[48px]">
                                <span class="font-mono-num font-black text-lg text-amber-300" x-text="hours">00</span>
                                <span class="block text-[8px] text-slate-400 uppercase">Jam</span>
                            </div>
                            <span class="font-bold text-[#C6A044]">:</span>
                            <div class="bg-black/40 border border-white/15 rounded-lg px-2.5 py-1 text-center min-w-[48px]">
                                <span class="font-mono-num font-black text-lg text-amber-300" x-text="minutes">00</span>
                                <span class="block text-[8px] text-slate-400 uppercase">Menit</span>
                            </div>
                            <span class="font-bold text-[#C6A044]">:</span>
                            <div class="bg-[#C6A044]/20 border border-[#C6A044]/40 rounded-lg px-2.5 py-1 text-center min-w-[48px]">
                                <span class="font-mono-num font-black text-lg text-amber-200" x-text="seconds">00</span>
                                <span class="block text-[8px] text-amber-200 uppercase">Detik</span>
                            </div>
                        </div>

                        <div x-show="hasStarted" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-emerald-500/20 text-emerald-300 text-xs font-bold border border-emerald-500/30">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                            Latihan Sedang Berlangsung / Waktu Dimulai
                        </div>
                    </div>

                    <!-- Tombol Aksi -->
                    <div class="w-full sm:w-auto flex items-center justify-end">
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
            <!-- State Kosong jika belum ada jadwal -->
            <div class="fast-glass-card rounded-2xl p-8 text-center max-w-md mx-auto">
                <span class="text-4xl block mb-2">🎺</span>
                <h3 class="font-heading font-bold text-lg text-white">Belum Ada Jadwal Latihan</h3>
                <p class="text-slate-400 text-xs mt-1">Jadwal latihan terbaru akan ditampilkan di sini setelah dibuat oleh pengurus.</p>
            </div>
        @endif

    </main>

    <!-- ============================================================
         UPCOMING SCHEDULES (JIKA LEBIH DARI 1 JADWAL)
         ============================================================ -->
    @if(isset($upcomingSchedules) && $upcomingSchedules->count() > 0)
    <section class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 py-6">
        <h2 class="text-xs font-bold text-[#C6A044] uppercase tracking-widest font-mono-num mb-3">
            AGENDA LATIHAN MENDATANG LAINNYA
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3.5">
            @foreach($upcomingSchedules as $sch)
            <div class="fast-glass-card rounded-xl p-4 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between text-xs text-slate-400 mb-2">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-white/10 text-slate-200">
                            {{ $sch->type_label }}
                        </span>
                        <span>{{ $sch->date->translatedFormat('d M Y') }}</span>
                    </div>
                    <h3 class="font-heading font-bold text-sm text-white line-clamp-2">
                        {{ $sch->title }}
                    </h3>
                    <div class="mt-3 space-y-1.5 text-xs">
                        <div class="text-amber-200 font-mono-num">
                            ⏰ {{ substr($sch->start_time, 0, 5) }} - {{ substr($sch->end_time, 0, 5) }} WIB
                        </div>
                        <div class="text-slate-300 font-medium">
                            👕 {{ $sch->dress_code ?: 'Seragam Latihan' }}
                        </div>
                        <div class="text-slate-400 text-[11px]">
                            📍 {{ $sch->location }}
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- ============================================================
         DATA MASTER & SEKSI DIVISI
         ============================================================ -->
    <section id="divisi-seksi" class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 py-10">

        <!-- Stat Counter -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-8">
            <div class="fast-glass-card rounded-xl p-3.5 text-center">
                <span class="font-heading font-black text-2xl text-amber-300 block">{{ $totalMembers }}</span>
                <span class="text-[10px] uppercase font-bold text-slate-400">Anggota Aktif</span>
            </div>
            <div class="fast-glass-card rounded-xl p-3.5 text-center">
                <span class="font-heading font-black text-2xl text-[#C6A044] block">{{ $totalDivisions }}</span>
                <span class="text-[10px] uppercase font-bold text-slate-400">Divisi Seksi</span>
            </div>
            <div class="fast-glass-card rounded-xl p-3.5 text-center">
                <span class="font-heading font-black text-2xl text-amber-200 block">{{ $totalInstruments }}</span>
                <span class="text-[10px] uppercase font-bold text-slate-400">Instrumen Alat</span>
            </div>
            <div class="fast-glass-card rounded-xl p-3.5 text-center">
                <span class="font-heading font-bold text-sm text-emerald-400 block mt-1.5 truncate">{{ $activePeriod->name ?? '2025/2026' }}</span>
                <span class="text-[10px] uppercase font-bold text-slate-400">Periode Aktif</span>
            </div>
        </div>

        <!-- Section List -->
        <h2 class="text-xs font-bold text-[#C6A044] uppercase tracking-widest font-mono-num mb-4 text-center">
            SEKSI DIVISI SYMPHONY MARCHING BAND
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
            @forelse($divisions as $d)
            <div class="fast-glass-card rounded-xl p-3.5 flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center text-lg shrink-0 bg-white/10"
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
            <div class="col-span-full text-center py-4 text-xs text-slate-400">
                Divisi marching band akan segera dimuat.
            </div>
            @endforelse
        </div>

    </section>

    <!-- ============================================================
         FOOTER
         ============================================================ -->
    <footer class="relative z-10 border-t border-white/10 bg-[#070D1C] py-8 px-4 text-center">
        <div class="max-w-5xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-400">
            <div class="flex items-center gap-2.5">
                <img src="{{ asset('logo.jpg') }}" alt="Logo" class="w-7 h-7 rounded-full border border-[#C6A044]/40">
                <span class="font-bold text-slate-300">Symphony Marching Band &bull; SMK Al Wasliyah Perdagangan</span>
            </div>
            <div>
                &copy; {{ date('Y') }} Symphony MB. Versi 2.4.0
            </div>
        </div>
    </footer>

</body>
</html>
