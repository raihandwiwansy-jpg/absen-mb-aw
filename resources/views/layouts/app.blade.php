<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Symphony') }} — @yield('title', 'Dashboard')</title>

    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="{{ asset('logo.jpg') }}">

    <!-- Dark Mode Initializer & Global Clock -->
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }

        window.initDigitalClock = function (elementId) {
            function update() {
                const el = document.getElementById(elementId);
                if (!el) return;
                const now = new Date();
                const hours = String(now.getHours()).padStart(2, '0');
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const seconds = String(now.getSeconds()).padStart(2, '0');
                el.textContent = `${hours}:${minutes}:${seconds} WIB`;
            }
            update();
            setInterval(update, 1000);
        };
    </script>

    <!-- Alpine.js (must be before Livewire) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles

    @stack('styles')
</head>
@php
    $currentUser = Auth::user();
    $isStaff = $currentUser && $currentUser->hasAnyRole(['super_admin', 'administrator', 'attendance_officer', 'coach']);
    $homeRoute = $isStaff ? route('dashboard') : route('portal.dashboard');
@endphp
<body class="h-full" x-data="symphonyApp()">

<!-- ============================================================
     SIDEBAR OVERLAY (mobile)
     ============================================================ -->
<div id="sidebar-overlay"
     :class="{ 'active': sidebarOpen && isMobile }"
     @click="sidebarOpen = false"></div>

<!-- ============================================================
     SIDEBAR
     ============================================================ -->
<aside id="sidebar"
       :class="{
           'sidebar-collapsed': sidebarCollapsed && !isMobile,
           'mobile-open': sidebarOpen && isMobile,
       }"
       class="sidebar-scroll overflow-y-auto">

    <!-- Brand Logo -->
    <div class="sidebar-logo-wrap">
        <a href="{{ $homeRoute }}" class="flex items-center gap-3 group">
            <img src="{{ asset('logo.jpg') }}"
                 alt="Symphony Logo"
                 class="sidebar-logo-img group-hover:scale-105 transition-transform duration-300">
            <div class="sidebar-brand-text" x-show="!sidebarCollapsed || isMobile">
                <span class="sidebar-brand-name">SYMPHONY</span>
                <span class="sidebar-brand-school">SMK AL WASLIYAH<br>PERDAGANGAN</span>
            </div>
        </a>

        <!-- Mobile close button (X) -->
        <button @click="sidebarOpen = false"
                class="lg:hidden ml-auto p-1.5 rounded-lg text-white/50 hover:text-white hover:bg-white/10 transition-colors"
                aria-label="Tutup Menu">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>

        <!-- Collapse toggle (desktop only) -->
        <button @click="sidebarCollapsed = !sidebarCollapsed"
                class="ml-auto text-white/40 hover:text-gold transition-colors hidden lg:block flex-shrink-0"
                x-show="!isMobile"
                :title="sidebarCollapsed ? 'Perlebar sidebar' : 'Kecilkan sidebar'">
            <svg class="w-4 h-4 transition-transform duration-300"
                 :class="{ 'rotate-180': sidebarCollapsed }"
                 fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
            </svg>
        </button>
    </div>

    <!-- Navigation wrapped in modern module cards -->
    <nav class="flex-1 py-3 px-2 space-y-2.5">
        @if($isStaff)
            <!-- Module 1: Utama -->
            <div class="sidebar-module-card">
                <div class="sidebar-module-label" x-show="!sidebarCollapsed || isMobile">Utama</div>
                <a href="{{ route('dashboard') }}"
                   class="sidebar-nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span class="nav-label" x-show="!sidebarCollapsed || isMobile">Dashboard</span>
                </a>
            </div>

            <!-- Module 2: Keorganisasian -->
            <div class="sidebar-module-card">
                <div class="sidebar-module-label" x-show="!sidebarCollapsed || isMobile">Keorganisasian</div>
                <a href="{{ route('members.index') }}"
                   class="sidebar-nav-item {{ request()->routeIs('members.*') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span class="nav-label" x-show="!sidebarCollapsed || isMobile">Data Anggota</span>
                </a>
                <a href="{{ route('divisions.index') }}"
                   class="sidebar-nav-item {{ request()->routeIs('divisions.*') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                    </svg>
                    <span class="nav-label" x-show="!sidebarCollapsed || isMobile">Divisi & Instrumen</span>
                </a>
            </div>

            <!-- Module 3: Presensi & Jadwal -->
            <div class="sidebar-module-card">
                <div class="sidebar-module-label" x-show="!sidebarCollapsed || isMobile">Presensi & Jadwal</div>
                <a href="{{ route('schedules.index') }}"
                   class="sidebar-nav-item {{ request()->routeIs('schedules.*') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="nav-label" x-show="!sidebarCollapsed || isMobile">Jadwal Latihan</span>
                </a>
                <a href="{{ route('attendance.index') }}"
                   class="sidebar-nav-item {{ request()->routeIs('attendance.*') && !request()->routeIs('attendance.scan') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    <span class="nav-label" x-show="!sidebarCollapsed || isMobile">Sesi Absensi</span>
                </a>
                <a href="{{ route('attendance.scan') }}"
                   class="sidebar-nav-item {{ request()->routeIs('attendance.scan') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                    <span class="nav-label" x-show="!sidebarCollapsed || isMobile">Scanner QR</span>
                </a>
                <a href="{{ route('permission-requests.index') }}"
                   class="sidebar-nav-item {{ request()->routeIs('permission-requests.*') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="nav-label" x-show="!sidebarCollapsed || isMobile">Izin & Sakit</span>
                </a>
            </div>

            <!-- Module 4: Laporan -->
            <div class="sidebar-module-card">
                <div class="sidebar-module-label" x-show="!sidebarCollapsed || isMobile">Laporan & Rekap</div>
                <a href="{{ route('reports.index') }}"
                   class="sidebar-nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="nav-label" x-show="!sidebarCollapsed || isMobile">Laporan Presensi</span>
                </a>
            </div>

            <!-- Module 5: Pengaturan Sistem -->
            <div class="sidebar-module-card">
                <div class="sidebar-module-label" x-show="!sidebarCollapsed || isMobile">Sistem</div>
                @can('manage-users')
                <a href="{{ route('users.index') }}"
                   class="sidebar-nav-item {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="nav-label" x-show="!sidebarCollapsed || isMobile">Pengguna</span>
                </a>
                @endcan

                <a href="{{ route('settings.index') }}"
                   class="sidebar-nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="nav-label" x-show="!sidebarCollapsed || isMobile">Pengaturan</span>
                </a>
            </div>
        @else
            <!-- Regular Member Navigation -->
            <!-- Module 1: Portal Pribadi -->
            <div class="sidebar-module-card">
                <div class="sidebar-module-label" x-show="!sidebarCollapsed || isMobile">Portal Anggota</div>
                <a href="{{ route('portal.dashboard') }}"
                   class="sidebar-nav-item {{ request()->routeIs('portal.dashboard') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span class="nav-label" x-show="!sidebarCollapsed || isMobile">Dashboard Pribadi</span>
                </a>
                <a href="{{ route('portal.profile') }}"
                   class="sidebar-nav-item {{ request()->routeIs('portal.profile') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="nav-label" x-show="!sidebarCollapsed || isMobile">Profil Keanggotaan</span>
                </a>
            </div>

            <!-- Module 2: Presensi & Jadwal -->
            <div class="sidebar-module-card">
                <div class="sidebar-module-label" x-show="!sidebarCollapsed || isMobile">Presensi & Jadwal</div>
                <a href="{{ route('portal.schedules') }}"
                   class="sidebar-nav-item {{ request()->routeIs('portal.schedules') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span class="nav-label" x-show="!sidebarCollapsed || isMobile">Jadwal Latihan</span>
                </a>
                <a href="{{ route('attendance.scan') }}"
                   class="sidebar-nav-item {{ request()->routeIs('attendance.scan') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                    <span class="nav-label" x-show="!sidebarCollapsed || isMobile">Scan QR Presensi</span>
                </a>
                <a href="{{ route('portal.attendance') }}"
                   class="sidebar-nav-item {{ request()->routeIs('portal.attendance') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    <span class="nav-label" x-show="!sidebarCollapsed || isMobile">Riwayat Kehadiran</span>
                </a>
                <a href="{{ route('permission-requests.index') }}"
                   class="sidebar-nav-item {{ request()->routeIs('permission-requests.*') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span class="nav-label" x-show="!sidebarCollapsed || isMobile">Pengajuan Izin / Sakit</span>
                </a>
            </div>

            <!-- Module 3: Akun -->
            <div class="sidebar-module-card">
                <div class="sidebar-module-label" x-show="!sidebarCollapsed || isMobile">Akun</div>
                <a href="{{ route('profile.edit') }}"
                   class="sidebar-nav-item {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    <span class="nav-label" x-show="!sidebarCollapsed || isMobile">Ubah Kata Sandi</span>
                </a>
            </div>
        @endif
    </nav>

    <!-- User quick info at bottom of sidebar (wrapped card) -->
    <div class="p-3 border-t border-white/10 mt-auto bg-black/20"
         x-show="!sidebarCollapsed || isMobile">
        <div class="flex items-center justify-between p-2 rounded-xl bg-white/5 border border-white/10 hover:border-gold/30 transition-all">
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-gold to-gold-dark flex items-center justify-center flex-shrink-0 shadow-md ring-2 ring-gold/30">
                    <span class="text-navy text-xs font-black">
                        {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                    </span>
                </div>
                <div class="overflow-hidden min-w-0">
                    <p class="text-white text-xs font-bold truncate">{{ Auth::user()->name ?? 'User' }}</p>
                    <p class="text-gold/80 text-[10px] truncate capitalize">{{ Auth::user()->getRoleNames()->first() ?? 'Member' }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
                @csrf
                <button type="submit"
                        class="p-1.5 text-white/50 hover:text-red-400 hover:bg-white/10 rounded-lg transition-colors"
                        title="Keluar">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </button>
            </form>
        </div>
    </div>

</aside>

<!-- ============================================================
     MAIN CONTENT AREA
     ============================================================ -->
<div id="main-content"
     :class="{ 'sidebar-collapsed': sidebarCollapsed && !isMobile }">

    <!-- TOP NAVBAR -->
    <header id="top-navbar">
        <!-- Left: Hamburger + Brand (mobile) + Page breadcrumb -->
        <div class="flex items-center gap-2 sm:gap-3 min-w-0 flex-shrink-0">
            <!-- Mobile hamburger -->
            <button @click="sidebarOpen = !sidebarOpen"
                    class="lg:hidden p-1.5 -ml-1 text-gray-500 hover:text-navy hover:bg-gray-100 rounded-xl transition-all flex-shrink-0"
                    aria-label="Buka Menu">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <!-- Mobile Mini Brand: Circular seal on mobile, text appears on tablet+ -->
            <a href="{{ $homeRoute }}" class="lg:hidden flex items-center gap-2 flex-shrink-0">
                <img src="{{ asset('logo.jpg') }}" alt="Logo" class="w-7 h-7 rounded-full ring-1 ring-gold/40 object-cover flex-shrink-0">
                <span class="font-extrabold text-navy dark:text-gold text-xs tracking-wider hidden sm:inline">SYMPHONY</span>
            </a>

            <!-- Breadcrumb / Page title (Desktop/Tablet) -->
            <div class="hidden lg:block min-w-0">
                <h1 class="text-sm font-semibold text-navy truncate">
                    @yield('title', 'Dashboard')
                </h1>
                @hasSection('breadcrumb')
                    <p class="text-xs text-gray-400 mt-0.5 truncate">@yield('breadcrumb')</p>
                @endif
            </div>
        </div>

        <!-- Right: Search Command Palette + Quick Scan + Notifications + User Dropdown -->
        <div class="flex items-center gap-1.5 sm:gap-2.5 flex-shrink-0">

            <!-- Command Palette Trigger (Ctrl + K) -->
            <button @click="cmdPaletteOpen = true"
                    type="button"
                    class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-gray-100/80 hover:bg-gray-200/80 border border-gray-200/60 text-xs text-gray-500 hover:text-navy transition shadow-inner">
                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                </svg>
                <span>Cari menu...</span>
                <kbd class="px-1.5 py-0.5 rounded text-[10px] font-mono bg-white border border-gray-200 shadow-xs text-gray-500 font-bold">Ctrl K</kbd>
            </button>

            <!-- Quick Scan QR Button (Icon on mobile, labeled on sm+) -->
            <a href="{{ route('attendance.scan') }}"
               class="inline-flex items-center gap-1.5 p-2 sm:px-3 sm:py-1.5 rounded-xl text-xs font-bold text-navy bg-gold/20 hover:bg-gold/30 border border-gold/40 transition flex-shrink-0"
               title="Scan QR Presensi">
                <svg class="w-4 h-4 text-gold-dark" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                </svg>
                <span class="hidden sm:inline">Scan QR</span>
            </a>

            <!-- Dark Mode Switcher -->
            <button @click="toggleTheme()"
                    type="button"
                    class="p-2 text-gray-500 hover:text-navy dark:text-gold dark:hover:text-gold-light hover:bg-gray-100 dark:hover:bg-white/10 rounded-xl transition flex-shrink-0"
                    :title="isDark ? 'Mode Terang' : 'Mode Gelap'">
                <template x-if="!isDark">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                </template>
                <template x-if="isDark">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-gold animate-spin-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </template>
            </button>

            <!-- Notifications Bell -->
            <div class="relative flex-shrink-0" x-data="{ open: false }">
                <button @click="open = !open" @click.outside="open = false"
                        class="relative p-2 text-gray-500 hover:text-navy hover:bg-gray-100 rounded-xl transition-all">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    @php
                        $unreadCount = Auth::user()->notifications()->where('is_read', false)->count();
                    @endphp
                    @if($unreadCount > 0)
                        <span class="notif-badge">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                    @endif
                </button>

                <!-- Notification dropdown -->
                <div x-show="open"
                     x-transition
                     class="dropdown-menu absolute right-0 top-full mt-2 w-72 sm:w-80">
                    <div class="p-4 border-b border-gray-100">
                        <h3 class="font-semibold text-sm text-navy">Notifikasi</h3>
                    </div>
                    <div class="max-h-64 overflow-y-auto">
                        @php
                            $notifications = Auth::user()->notifications()->latest()->limit(5)->get();
                        @endphp
                        @forelse($notifications as $notification)
                            <a href="#"
                               class="dropdown-item {{ !$notification->is_read ? 'bg-gold/5' : '' }}">
                                <div class="w-2 h-2 rounded-full {{ !$notification->is_read ? 'bg-gold' : 'bg-gray-300' }} flex-shrink-0 mt-1"></div>
                                <div>
                                    <p class="text-xs font-medium text-navy">{{ $notification->title }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">{{ $notification->created_at->diffForHumans() }}</p>
                                </div>
                            </a>
                        @empty
                            <div class="p-6 text-center text-sm text-gray-400">
                                <svg class="w-10 h-10 mx-auto mb-2 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                Tidak ada notifikasi
                            </div>
                        @endforelse
                    </div>
                    @if($notifications->count() > 0)
                        <div class="p-3 border-t border-gray-100 text-center">
                            <a href="#" class="text-xs text-gold font-semibold hover:text-gold-dark">
                                Lihat semua notifikasi
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- User Dropdown -->
            <div class="relative flex-shrink-0" x-data="{ open: false }">
                <button @click="open = !open" @click.outside="open = false"
                        class="flex items-center gap-1.5 sm:gap-2.5 p-1 sm:pl-3 sm:pr-4 sm:py-1.5 rounded-xl hover:bg-gray-50 transition-colors border border-transparent hover:border-gray-200">
                    <!-- Avatar -->
                    <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-navy flex items-center justify-center flex-shrink-0 ring-1 ring-gold/40">
                        <span class="text-gold text-xs font-bold">
                            {{ strtoupper(substr(Auth::user()->name ?? 'U', 0, 1)) }}
                        </span>
                    </div>
                    <div class="hidden sm:block text-left">
                        <p class="text-xs font-semibold text-navy">{{ Auth::user()->name ?? 'User' }}</p>
                        <p class="text-[10px] text-gray-400">{{ Auth::user()->email ?? '' }}</p>
                    </div>
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-gray-400 transition-transform duration-200"
                         :class="{ 'rotate-180': open }"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>

                <!-- User dropdown menu -->
                <div x-show="open"
                     x-transition
                     class="dropdown-menu absolute right-0 top-full mt-2 w-56 max-w-[calc(100vw-32px)] z-50">
                    <div class="p-4 border-b border-gray-100">
                        <p class="text-xs font-semibold text-navy">{{ Auth::user()->name ?? 'User' }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">{{ Auth::user()->email ?? '' }}</p>
                        <span class="badge badge-navy mt-1">
                            {{ Auth::user()->getRoleNames()->first() ?? 'member' }}
                        </span>
                    </div>
                    <div class="p-2">
                        <a href="{{ $isStaff ? route('profile.edit') : route('portal.profile') }}" class="dropdown-item">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Profil Saya
                        </a>
                        @if($isStaff)
                        <a href="{{ route('settings.index') }}" class="dropdown-item">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Pengaturan
                        </a>
                        @endif
                        <div class="border-t border-gray-100 my-1"></div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item danger w-full">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Page Content -->
    <main class="page-content animate-fade-in">

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-4 flex items-center gap-3 px-4 py-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-800"
                 data-auto-dismiss>
                <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 flex items-center gap-3 px-4 py-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-800"
                 data-auto-dismiss>
                <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if(session('warning'))
            <div class="mb-4 flex items-center gap-3 px-4 py-3 bg-yellow-50 border border-yellow-200 rounded-xl text-sm text-yellow-800"
                 data-auto-dismiss>
                <svg class="w-5 h-5 text-yellow-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19H19a2 2 0 001.75-2.75L13.75 4a2 2 0 00-3.5 0L2.25 16.25A2 2 0 004 19h1.07z"/>
                </svg>
                <span>{{ session('warning') }}</span>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="border-t border-gray-200 px-6 py-4 text-center">
        <p class="text-xs text-gray-400">
            &copy; {{ date('Y') }} <span class="text-gold font-semibold">Symphony Marching Band</span>
            &mdash; SMK AL WASLIYAH PERDAGANGAN
        </p>
    </footer>

</div><!-- end #main-content -->

<!-- ============================================================
     COMMAND PALETTE MODAL (Ctrl + K)
     ============================================================ -->
<div x-show="cmdPaletteOpen"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-[999] overflow-y-auto bg-black/60 backdrop-blur-sm flex items-start justify-center p-4 sm:pt-20"
     style="display: none;"
     @keydown.window.escape="cmdPaletteOpen = false">

    <div @click.outside="cmdPaletteOpen = false"
         class="w-full max-w-xl rounded-2xl bg-white shadow-2xl border border-gold/30 overflow-hidden transform transition-all">

        <!-- Search Input -->
        <div class="relative border-b border-gray-100 p-4 flex items-center gap-3">
            <svg class="w-5 h-5 text-gold flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
            </svg>
            <input type="text"
                   x-ref="cmdInput"
                   x-model="cmdSearch"
                   placeholder="Ketik untuk mencari menu, aksi, atau halaman..."
                   class="w-full text-sm font-medium text-navy placeholder-gray-400 focus:outline-none bg-transparent">
            <kbd class="px-2 py-0.5 rounded text-[10px] font-mono bg-gray-100 text-gray-500 font-bold">ESC</kbd>
        </div>

        <!-- Quick Items List -->
        <div class="max-h-80 overflow-y-auto p-2 space-y-1">
            <template x-for="item in filteredCmdItems" :key="item.url">
                <a :href="item.url"
                   class="flex items-center justify-between p-3 rounded-xl hover:bg-gold/10 text-navy transition group">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-navy/5 text-navy group-hover:bg-gold group-hover:text-navy flex items-center justify-center transition"
                             x-html="item.icon"></div>
                        <div>
                            <p class="text-xs font-bold" x-text="item.title"></p>
                            <p class="text-[10px] text-gray-400" x-text="item.desc"></p>
                        </div>
                    </div>
                    <svg class="w-4 h-4 text-gray-300 group-hover:text-gold transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </template>
            <div x-show="filteredCmdItems.length === 0" class="p-6 text-center text-xs text-gray-400">
                Tidak ada menu yang sesuai dengan kata kunci.
            </div>
        </div>

        <div class="p-3 bg-gray-50 border-t border-gray-100 flex items-center justify-between text-[11px] text-gray-400">
            <span>Symphony Quick Navigator</span>
            <span>Tekan <kbd class="font-bold text-navy">Enter</kbd> untuk memilih</span>
        </div>
    </div>
</div>

@livewireScripts

@stack('scripts')

<script>
    function symphonyApp() {
        return {
            isDark: document.documentElement.classList.contains('dark'),
            toggleTheme() {
                this.isDark = !this.isDark;
                if (this.isDark) {
                    document.documentElement.classList.add('dark');
                    localStorage.setItem('theme', 'dark');
                } else {
                    document.documentElement.classList.remove('dark');
                    localStorage.setItem('theme', 'light');
                }
            },
            sidebarOpen: false,
            sidebarCollapsed: localStorage.getItem('sidebar-collapsed') === 'true',
            isMobile: window.innerWidth < 1024,
            cmdPaletteOpen: false,
            cmdSearch: '',

            cmdItems: [
                @if($isStaff)
                {
                    title: 'Dashboard Utama',
                    desc: 'Ringkasan statistik kehadiran dan kegiatan',
                    url: '{{ route('dashboard') }}',
                    icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>'
                },
                {
                    title: 'Pemindai Presensi (Scan QR)',
                    desc: 'Buka kamera pemindai QR kehadiran mandiri',
                    url: '{{ route('attendance.scan') }}',
                    icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>'
                },
                {
                    title: 'Manajemen Anggota',
                    desc: 'Daftar, tambah, filter & profil anggota',
                    url: '{{ route('members.index') }}',
                    icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>'
                },
                {
                    title: 'Jadwal Latihan & Kalender',
                    desc: 'Agenda latihan rutin, tambahan & kompetisi',
                    url: '{{ route('schedules.index') }}',
                    icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>'
                },
                {
                    title: 'Sesi Presensi Aktif',
                    desc: 'Buka sesi, lembar presensi, proyektor QR',
                    url: '{{ route('attendance.sessions.index') }}',
                    icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>'
                },
                {
                    title: 'Permohonan Izin & Sakit',
                    desc: 'Verifikasi pengajuan izin anggota',
                    url: '{{ route('permission-requests.index') }}',
                    icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>'
                },
                {
                    title: 'Laporan & Analitik',
                    desc: 'Rekap kehadiran, ekspor Excel & PDF',
                    url: '{{ route('reports.index') }}',
                    icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>'
                },
                @else
                {
                    title: 'Dashboard Pribadi',
                    desc: 'Ringkasan kehadiran & jadwal latihan saya',
                    url: '{{ route('portal.dashboard') }}',
                    icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>'
                },
                {
                    title: 'Jadwal Latihan & Agenda',
                    desc: 'Daftar jadwal latihan, dresscode & kalender kegiatan',
                    url: '{{ route('portal.schedules') }}',
                    icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>'
                },
                {
                    title: 'Profil Keanggotaan',
                    desc: 'Biodata keanggotaan & kartu identitas',
                    url: '{{ route('portal.profile') }}',
                    icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>'
                },
                {
                    title: 'Pemindai Presensi (Scan QR)',
                    desc: 'Buka kamera pemindai QR kehadiran mandiri',
                    url: '{{ route('attendance.scan') }}',
                    icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>'
                },
                {
                    title: 'Riwayat Kehadiran',
                    desc: 'Daftar catatan kehadiran latihan saya',
                    url: '{{ route('portal.attendance') }}',
                    icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>'
                },
                {
                    title: 'Ajukan Izin / Sakit',
                    desc: 'Formulir pengajuan izin atau sakit latihan',
                    url: '{{ route('permission-requests.create') }}',
                    icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>'
                },
                @endif
                {
                    title: 'Ubah Kata Sandi',
                    desc: 'Perbarui kata sandi dan keamanan akun',
                    url: '{{ route('profile.edit') }}',
                    icon: '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>'
                }
            ],

            get filteredCmdItems() {
                if (!this.cmdSearch) return this.cmdItems;
                const q = this.cmdSearch.toLowerCase();
                return this.cmdItems.filter(item =>
                    item.title.toLowerCase().includes(q) || item.desc.toLowerCase().includes(q)
                );
            },

            init() {
                this.$watch('sidebarCollapsed', (val) => {
                    localStorage.setItem('sidebar-collapsed', val ? 'true' : 'false');
                });

                window.addEventListener('resize', () => {
                    this.isMobile = window.innerWidth < 1024;
                    if (!this.isMobile) {
                        this.sidebarOpen = false;
                    }
                });

                // Global shortcut Ctrl+K / Cmd+K
                window.addEventListener('keydown', (e) => {
                    if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
                        e.preventDefault();
                        this.cmdPaletteOpen = !this.cmdPaletteOpen;
                        if (this.cmdPaletteOpen) {
                            setTimeout(() => {
                                if (this.$refs.cmdInput) this.$refs.cmdInput.focus();
                            }, 50);
                        }
                    }
                });

                // Trigger flash messages in window.showToast
                @if(session('success'))
                    window.showToast(@json(session('success')), 'success');
                @endif
                @if(session('error'))
                    window.showToast(@json(session('error')), 'error');
                @endif
                @if(session('warning'))
                    window.showToast(@json(session('warning')), 'warning');
                @endif
            },
        };
    }
</script>

</body>
</html>
