@extends('layouts.auth')

@section('title', 'Masuk ke Sistem')

@section('content')
<div class="w-full max-w-5xl mx-auto" x-data="{
    email: @json(old('email', '')),
    password: '',
    showPassword: false,
    isSubmitting: false
}">

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-center">

        <!-- ============================================================
             LEFT COLUMN: MARCHING BAND SHOWCASE & ANIMATIONS (DESKTOP / TABLET)
             ============================================================ -->
        <div class="lg:col-span-6 xl:col-span-7 text-center lg:text-left space-y-6">

            <!-- Logo & Badge -->
            <div class="flex flex-col lg:flex-row items-center gap-4">
                <div class="relative group">
                    <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full border-4 border-[#C6A044] p-1 shadow-2xl bg-[#101D3B] group-hover:scale-105 transition duration-300">
                        <img src="{{ asset('logo.jpg') }}"
                             alt="Symphony Logo"
                             class="w-full h-full rounded-full object-cover">
                    </div>
                    <!-- Outer pulsating golden aura -->
                    <div class="absolute inset-0 rounded-full border-2 border-[#C6A044]/40 animate-pulse-gold pointer-events-none"></div>
                </div>

                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-[#C6A044]/15 border border-[#C6A044]/30 text-[#C6A044] text-[10px] sm:text-xs font-black uppercase tracking-wider">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#C6A044] animate-ping"></span>
                        Sistem Presensi Marching Band 2026
                    </span>
                    <h1 class="text-2xl sm:text-3xl xl:text-4xl font-black text-white tracking-wide uppercase mt-1">
                        SYMPHONY <span class="text-[#C6A044]">MB</span>
                    </h1>
                    <p class="text-xs sm:text-sm font-semibold text-[#F7F5EF]/70 tracking-widest uppercase">
                        SMK Al Wasliyah Perdagangan
                    </p>
                </div>
            </div>

            <!-- Tagline -->
            <p class="text-xs sm:text-sm text-gray-300 leading-relaxed max-w-xl mx-auto lg:mx-0">
                Aplikasi manajemen keorganisasian, pencatatan presensi real-time berbasis QR Code dinamis, dan pembinaan kedisiplinan seluruh seksi marching band.
            </p>

            <!-- ============================================================
                 MARCHING BAND INSTRUMENTS ANIMATED CARDS (Terompet, Battery, Color Guard)
                 ============================================================ -->
            <div class="grid grid-cols-3 gap-2 sm:gap-3 pt-2">

                <!-- 1. Brass Section: Terompet (Trumpet) Animated -->
                <div class="rounded-2xl border border-white/10 bg-white/5 dark:bg-white/5 backdrop-blur-md p-3 text-center hover:border-[#C6A044]/50 transition group hover:-translate-y-1 duration-300 shadow-sm">
                    <div class="h-12 flex items-center justify-center">
                        <!-- Custom SVG Trumpet Fanfare -->
                        <svg class="w-12 h-10 text-[#C6A044] animate-trumpet" viewBox="0 0 64 32" fill="none" stroke="currentColor">
                            <!-- Lead pipe & Mouthpiece -->
                            <path d="M4 16 L22 16" stroke-width="2.5" stroke-linecap="round"/>
                            <rect x="2" y="14" width="3" height="4" rx="1" fill="#C6A044"/>
                            <!-- Valve Casings -->
                            <rect x="22" y="11" width="4" height="10" rx="1" fill="#C6A044" stroke-width="1"/>
                            <rect x="28" y="11" width="4" height="10" rx="1" fill="#C6A044" stroke-width="1"/>
                            <rect x="34" y="11" width="4" height="10" rx="1" fill="#C6A044" stroke-width="1"/>
                            <!-- Valve Finger Caps -->
                            <line x1="24" y1="8" x2="24" y2="11" stroke-width="2"/>
                            <circle cx="24" cy="7" r="1.5" fill="#E5C768"/>
                            <line x1="30" y1="8" x2="30" y2="11" stroke-width="2"/>
                            <circle cx="30" cy="7" r="1.5" fill="#E5C768"/>
                            <line x1="36" y1="8" x2="36" y2="11" stroke-width="2"/>
                            <circle cx="36" cy="7" r="1.5" fill="#E5C768"/>
                            <!-- Tuning slide loop -->
                            <path d="M24 21 C24 25, 36 25, 36 21" stroke-width="1.8"/>
                            <!-- Bell Branch & Flare -->
                            <path d="M38 16 L50 16 C54 16, 58 13, 62 10 L62 22 C58 19, 54 16, 50 16 Z" fill="#C6A044" stroke-width="1.5"/>
                            <!-- Sound waves -->
                            <path d="M63 13 C64 15, 64 17, 63 19" stroke-width="1.5" stroke-linecap="round" opacity="0.6"/>
                        </svg>
                    </div>
                    <p class="text-[11px] font-black text-white mt-1">Brass Section</p>
                    <p class="text-[9px] text-[#C6A044] font-medium">Trumpet & Horn</p>
                </div>

                <!-- 2. Percussion Battery: Snare Drum & Crossed Sticks Animated -->
                <div class="rounded-2xl border border-white/10 bg-white/5 dark:bg-white/5 backdrop-blur-md p-3 text-center hover:border-emerald-400/50 transition group hover:-translate-y-1 duration-300 shadow-sm">
                    <div class="h-12 flex items-center justify-center">
                        <!-- Custom SVG Marching Snare Drum Battery -->
                        <svg class="w-12 h-10 text-emerald-400 animate-drum" viewBox="0 0 64 36" fill="none" stroke="currentColor">
                            <!-- Crossed Drumsticks in background -->
                            <line x1="10" y1="4" x2="54" y2="32" stroke="#F7F5EF" stroke-width="2" stroke-linecap="round" opacity="0.7"/>
                            <circle cx="10" cy="4" r="1.5" fill="#F7F5EF"/>
                            <line x1="54" y1="4" x2="10" y2="32" stroke="#F7F5EF" stroke-width="2" stroke-linecap="round" opacity="0.7"/>
                            <circle cx="54" cy="4" r="1.5" fill="#F7F5EF"/>
                            <!-- Drum Shell Cylinder -->
                            <rect x="14" y="10" width="36" height="18" rx="2" fill="#101D3B" stroke="#101D3B" stroke-width="1.5"/>
                            <!-- Top Counterhoop / Rim -->
                            <ellipse cx="32" cy="10" rx="18" ry="4" fill="#C6A044" stroke-width="1.5"/>
                            <!-- Bottom Rim -->
                            <ellipse cx="32" cy="28" rx="18" ry="4" fill="#C6A044" stroke-width="1.5"/>
                            <!-- Tension Lugs / Rods -->
                            <line x1="18" y1="11" x2="18" y2="27" stroke="#FFFFFF" stroke-width="1.5"/>
                            <line x1="25" y1="13" x2="25" y2="29" stroke="#FFFFFF" stroke-width="1.5"/>
                            <line x1="32" y1="14" x2="32" y2="30" stroke="#FFFFFF" stroke-width="1.5"/>
                            <line x1="39" y1="13" x2="39" y2="29" stroke="#FFFFFF" stroke-width="1.5"/>
                            <line x1="46" y1="11" x2="46" y2="27" stroke="#FFFFFF" stroke-width="1.5"/>
                        </svg>
                    </div>
                    <p class="text-[11px] font-black text-white mt-1">Battery Percussion</p>
                    <p class="text-[9px] text-emerald-300 font-medium">Snare & Bass Drum</p>
                </div>

                <!-- 3. Color Guard: Flowing Silk Flag Animated -->
                <div class="rounded-2xl border border-white/10 bg-white/5 dark:bg-white/5 backdrop-blur-md p-3 text-center hover:border-purple-400/50 transition group hover:-translate-y-1 duration-300 shadow-sm">
                    <div class="h-12 flex items-center justify-center">
                        <!-- Custom SVG Color Guard Silk Flag -->
                        <svg class="w-12 h-10 text-purple-400 animate-flag" viewBox="0 0 64 36" fill="none" stroke="currentColor">
                            <!-- Flag Pole -->
                            <line x1="14" y1="4" x2="14" y2="34" stroke="#F7F5EF" stroke-width="2.5" stroke-linecap="round"/>
                            <circle cx="14" cy="4" r="2" fill="#C6A044"/>
                            <!-- Silk Wave -->
                            <path d="M14 6 C24 3, 30 14, 42 8 C48 5, 54 9, 58 7 L54 22 C48 24, 42 19, 32 23 C22 27, 18 19, 14 22 Z"
                                  fill="url(#flagGradient)" stroke="#C6A044" stroke-width="1.2"/>
                            <defs>
                                <linearGradient id="flagGradient" x1="0" y1="0" x2="1" y2="1">
                                    <stop offset="0%" stop-color="#C6A044" stop-opacity="0.9"/>
                                    <stop offset="50%" stop-color="#8B5CF6" stop-opacity="0.8"/>
                                    <stop offset="100%" stop-color="#3B82F6" stop-opacity="0.9"/>
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                    <p class="text-[11px] font-black text-white mt-1">Color Guard</p>
                    <p class="text-[9px] text-purple-300 font-medium">Silk Flag & Rifle</p>
                </div>

            </div>

            <!-- Motto Banner -->
            <div class="hidden lg:flex items-center gap-3 px-4 py-2.5 rounded-2xl bg-white/5 border border-white/10 text-xs text-white/80">
                <span class="text-[#C6A044] text-base">♩</span>
                <span class="italic font-medium">"Harmoni Dalam Nada • Keteguhan Dalam Disiplin • Kejayaan Dalam Prestasi"</span>
            </div>

        </div>

        <!-- ============================================================
             RIGHT COLUMN: PREMIUM GLASSMORPHIC LOGIN FORM
             ============================================================ -->
        <div class="lg:col-span-6 xl:col-span-5">

            <div class="rounded-3xl bg-white/95 dark:bg-[#0D1629]/95 backdrop-blur-2xl p-6 sm:p-8 shadow-2xl border border-white/20 dark:border-white/10 animate-scale-in">

                <!-- Header inside form card -->
                <div class="text-left mb-6">
                    <div class="flex items-center justify-between">
                        <span class="inline-block px-2.5 py-0.5 rounded-md bg-[#101D3B]/5 dark:bg-white/10 text-[#101D3B] dark:text-[#C6A044] text-[10px] font-black uppercase tracking-wider">
                            Portal Akses
                        </span>
                        <span class="text-[11px] font-bold text-gray-400 font-mono">TAHUN 2026</span>
                    </div>
                    <h2 class="text-2xl font-black text-[#101D3B] dark:text-white mt-2">
                        Masuk ke Akun
                    </h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Masukkan email dan kata sandi Anda untuk melanjutkan.
                    </p>
                </div>

                <!-- Session Status / Flash Alert -->
                @if(session('status'))
                    <div class="mb-4 flex items-center gap-3 px-3.5 py-2.5 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 rounded-xl text-xs text-emerald-800 dark:text-emerald-300">
                        <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-4" @submit="isSubmitting = true">
                    @csrf

                    <!-- Email Field -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-gray-700 dark:text-gray-200 mb-1.5">
                            Alamat Email
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                </svg>
                            </div>
                            <input id="email"
                                   type="email"
                                   name="email"
                                   x-model="email"
                                   required
                                   autofocus
                                   autocomplete="username"
                                   placeholder="nama@symphony.com"
                                   class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#16233B] pl-10 pr-4 py-2.5 text-sm text-gray-800 dark:text-white placeholder-gray-400 focus:border-[#C6A044] focus:ring-2 focus:ring-[#C6A044]/20 focus:outline-none transition {{ $errors->has('email') ? 'border-red-400 focus:border-red-400' : '' }}">
                        </div>
                        @error('email')
                            <p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password Field -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-xs font-bold text-gray-700 dark:text-gray-200">
                                Kata Sandi
                            </label>
                            @if(Route::has('password.request'))
                                <a href="{{ route('password.request') }}"
                                   class="text-[11px] text-[#C6A044] hover:underline font-semibold transition">
                                    Lupa kata sandi?
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </div>
                            <input id="password"
                                   :type="showPassword ? 'text' : 'password'"
                                   name="password"
                                   x-model="password"
                                   required
                                   autocomplete="current-password"
                                   placeholder="••••••••"
                                   class="w-full rounded-xl border border-gray-200 dark:border-white/10 bg-white dark:bg-[#16233B] pl-10 pr-10 py-2.5 text-sm text-gray-800 dark:text-white placeholder-gray-400 focus:border-[#C6A044] focus:ring-2 focus:ring-[#C6A044]/20 focus:outline-none transition {{ $errors->has('password') ? 'border-red-400 focus:border-red-400' : '' }}">

                            <!-- Show/Hide Password Eye Button -->
                            <button type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-white transition">
                                <template x-if="!showPassword">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </template>
                                <template x-if="showPassword">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                    </svg>
                                </template>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-xs text-red-500 font-semibold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center gap-2 pt-1">
                        <input id="remember_me"
                               type="checkbox"
                               name="remember"
                               class="w-4 h-4 rounded border-gray-300 dark:border-white/20 accent-[#C6A044] cursor-pointer">
                        <label for="remember_me" class="text-xs text-gray-600 dark:text-gray-300 cursor-pointer select-none">
                            Ingat saya di perangkat ini
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        <button type="submit"
                                :disabled="isSubmitting"
                                class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-xs sm:text-sm font-black tracking-wide text-white bg-gradient-to-r from-[#101D3B] via-[#1a2d57] to-[#101D3B] hover:from-[#1a2d57] hover:to-[#101D3B] dark:from-[#C6A044] dark:to-[#b58f35] dark:text-[#101D3B] transition shadow-lg hover:shadow-xl active:scale-95 disabled:opacity-60">
                            <template x-if="!isSubmitting">
                                <div class="inline-flex items-center gap-2">
                                    <svg class="w-4 h-4 text-[#C6A044] dark:text-[#101D3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                    </svg>
                                    <span>Masuk ke Sistem</span>
                                </div>
                            </template>
                            <template x-if="isSubmitting">
                                <div class="inline-flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4 text-white dark:text-[#101D3B]" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Memproses Masuk...</span>
                                </div>
                            </template>
                        </button>
                    </div>

                    <!-- Return to Landing Page Button -->
                    <div class="pt-3 text-center border-t border-gray-100 dark:border-white/10 mt-3">
                        <a href="{{ url('/') }}"
                           class="inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 rounded-xl text-xs font-bold text-gray-700 dark:text-gray-300 bg-gray-50 hover:bg-gray-100 dark:bg-white/5 dark:hover:bg-white/10 border border-gray-200 dark:border-white/10 transition-colors">
                            <svg class="w-4 h-4 text-[#C6A044]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            <span>Kembali ke Halaman Beranda</span>
                        </a>
                    </div>

                </form>

            </div>

        </div>

    </div>

</div>
@endsection
