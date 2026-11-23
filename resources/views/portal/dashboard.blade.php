@extends('layouts.app')

@section('title', 'Portal Anggota')
@section('breadcrumb', 'Dashboard Pribadi')

@section('content')
<div class="space-y-6">

    @if($activeSession ?? null)
    {{-- Active Attendance Session Alert Banner --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-emerald-900 via-[#101D3B] to-emerald-950 p-5 sm:p-6 text-white shadow-xl border-2 border-emerald-500/40">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-start sm:items-center gap-4">
                <div class="relative w-12 h-12 rounded-2xl bg-emerald-500/20 border border-emerald-400/30 flex items-center justify-center flex-shrink-0 text-emerald-400">
                    <span class="absolute w-3 h-3 rounded-full bg-emerald-400 top-2 right-2 animate-ping"></span>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                </div>
                <div>
                    <div class="inline-flex items-center gap-2 px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px] font-extrabold uppercase tracking-wider mb-1">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        Sesi Presensi Sedang Dibuka!
                    </div>
                    <h3 class="text-base sm:text-lg font-black text-white">
                        {{ $activeSession->practiceSchedule->title ?? 'Sesi Latihan Aktif' }}
                    </h3>
                    <div class="flex flex-wrap items-center gap-3 text-xs text-gray-300 mt-1">
                        <span>⏰ {{ substr($activeSession->practiceSchedule->start_time ?? '', 0, 5) }} - {{ substr($activeSession->practiceSchedule->end_time ?? '', 0, 5) }} WIB</span>
                        <span>•</span>
                        <span>📍 {{ $activeSession->practiceSchedule->location ?? 'Lokasi Latihan' }}</span>
                        @if($activeSession->practiceSchedule->dress_code ?? null)
                            <span>•</span>
                            <span class="text-[#C6A044] font-bold">👕 {{ $activeSession->practiceSchedule->dress_code }}</span>
                        @endif
                    </div>
                </div>
            </div>
            <a href="{{ route('attendance.scan') }}"
               class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl font-black text-xs sm:text-sm text-[#101D3B] bg-[#C6A044] hover:bg-amber-300 transition shadow-xl whitespace-nowrap pulse-ring-gold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                Scan QR Sekarang
            </a>
        </div>
    </div>
    @endif

    {{-- Member Hero Card --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#101D3B] via-[#202B40] to-[#101D3B] p-6 lg:p-8 text-white shadow-2xl border border-[#C6A044]/30">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="relative w-20 h-20 rounded-full border-2 border-[#C6A044] p-1 bg-white/10 shadow-xl flex-shrink-0">
                    @if($member->photo && file_exists(public_path('storage/' . $member->photo)))
                        <img src="{{ asset('storage/' . $member->photo) }}" alt="{{ $member->full_name }}" class="w-full h-full rounded-full object-cover">
                    @else
                        <div class="w-full h-full rounded-full bg-[#101D3B] flex items-center justify-center text-[#C6A044] text-2xl font-black">
                            {{ strtoupper(substr($member->full_name, 0, 2)) }}
                        </div>
                    @endif
                </div>
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#C6A044]/20 border border-[#C6A044]/40 text-[#C6A044] text-xs font-bold tracking-wider uppercase mb-1.5">
                        <span class="w-2 h-2 rounded-full bg-[#C6A044]"></span>
                        {{ $member->member_code }} • {{ $member->division->name ?? 'Seksi Anggota' }}
                    </div>
                    <h2 class="text-2xl lg:text-3xl font-black tracking-tight text-white">
                        Halo, {{ $member->full_name }}!
                    </h2>
                    <p class="text-xs text-gray-300 mt-1 flex flex-wrap items-center gap-3">
                        <span>Instrumen: <strong class="text-white">{{ $member->instrument->name ?? 'Belum Ditentukan' }}</strong></span>
                        <span>•</span>
                        <span>Jabatan: <strong class="text-white">{{ $member->position->name ?? 'Anggota' }}</strong></span>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('attendance.scan') }}"
                   class="btn-gold inline-flex items-center gap-2 px-5 py-3 rounded-2xl font-bold text-xs sm:text-sm shadow-xl hover:shadow-2xl transition pulse-ring-gold"
                   style="background-color: #C6A044; color: #101D3B;">
                    <svg class="w-5 h-5 text-[#101D3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    Scan QR Presensi
                </a>
                <a href="{{ route('permission-requests.create') }}" class="inline-flex items-center gap-2 px-4 py-3 rounded-2xl font-bold text-xs bg-white/10 hover:bg-white/20 text-white transition border border-white/20">
                    Ajukan Izin
                </a>
            </div>
        </div>
    </div>

    {{-- Virtual Member Card & Attendance Gauge Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">

        {{-- Virtual ID Card (KTA Digital Symphony) --}}
        <div class="lg:col-span-5 flex flex-col justify-between member-id-card p-6 sm:p-7 text-white">
            <div>
                {{-- Header of ID Card --}}
                <div class="flex items-center justify-between border-b border-white/15 pb-4">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('logo.jpg') }}" alt="Symphony Logo" class="w-12 h-12 rounded-full border border-[#C6A044]">
                        <div>
                            <h3 class="text-xs font-black tracking-widest text-[#C6A044] uppercase">SYMPHONY MARCHING BAND</h3>
                            <p class="text-[9px] text-gray-300 font-bold uppercase tracking-wider">SMK AL WASLIYAH PERDAGANGAN</p>
                        </div>
                    </div>
                    <span class="text-[9px] font-mono px-2 py-0.5 rounded-full bg-white/10 text-gray-300 border border-white/10">
                        KTA DIGITAL
                    </span>
                </div>

                {{-- Card Content --}}
                <div class="mt-5 flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl overflow-hidden border-2 border-[#C6A044] shadow-md flex-shrink-0 bg-white/10">
                        @if($member->photo && file_exists(public_path('storage/' . $member->photo)))
                            <img src="{{ asset('storage/' . $member->photo) }}" alt="{{ $member->full_name }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center font-black text-xl text-[#C6A044]">
                                {{ strtoupper(substr($member->full_name, 0, 2)) }}
                            </div>
                        @endif
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-black text-white truncate">{{ $member->full_name }}</p>
                        <p class="text-[11px] font-mono text-[#C6A044] font-bold">{{ $member->member_code }}</p>
                        <p class="text-[10px] text-gray-300 mt-0.5">{{ $member->division->name ?? '-' }} • {{ $member->instrument->name ?? '-' }}</p>
                    </div>
                </div>
            </div>

            {{-- Card Footer --}}
            <div class="mt-6 pt-4 border-t border-white/10 flex items-center justify-between text-[10px] text-gray-400">
                <span>Periode: <strong>2026/2027</strong></span>
                <span class="font-mono text-emerald-400 font-bold">STATUS: AKTIF</span>
            </div>
        </div>

        {{-- Personal Stats Cards (7 cols) --}}
        <div class="lg:col-span-7 grid grid-cols-2 sm:grid-cols-3 gap-3">
            <div class="bg-white p-4 rounded-3xl border border-gray-100 shadow-sm text-center flex flex-col justify-center">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Total Sesi</span>
                <span class="text-3xl font-black text-[#101D3B] mt-1 block" data-counter="{{ $totalSessions }}">0</span>
            </div>
            <div class="bg-emerald-50 border border-emerald-100 p-4 rounded-3xl shadow-sm text-center flex flex-col justify-center">
                <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider block">Hadir Tepat</span>
                <span class="text-3xl font-black text-emerald-700 mt-1 block" data-counter="{{ $presentCount - $lateCount }}">0</span>
            </div>
            <div class="bg-amber-50 border border-amber-100 p-4 rounded-3xl shadow-sm text-center flex flex-col justify-center">
                <span class="text-[10px] font-bold text-amber-800 uppercase tracking-wider block">Terlambat</span>
                <span class="text-3xl font-black text-amber-700 mt-1 block" data-counter="{{ $lateCount }}">0</span>
            </div>
            <div class="bg-blue-50 border border-blue-100 p-4 rounded-3xl shadow-sm text-center flex flex-col justify-center">
                <span class="text-[10px] font-bold text-blue-800 uppercase tracking-wider block">Izin</span>
                <span class="text-3xl font-black text-blue-700 mt-1 block" data-counter="{{ $permissionCount }}">0</span>
            </div>
            <div class="bg-purple-50 border border-purple-100 p-4 rounded-3xl shadow-sm text-center flex flex-col justify-center">
                <span class="text-[10px] font-bold text-purple-800 uppercase tracking-wider block">Sakit</span>
                <span class="text-3xl font-black text-purple-700 mt-1 block" data-counter="{{ $sickCount }}">0</span>
            </div>
            <div class="bg-red-50 border border-red-100 p-4 rounded-3xl shadow-sm text-center flex flex-col justify-center">
                <span class="text-[10px] font-bold text-red-800 uppercase tracking-wider block">Alpa</span>
                <span class="text-3xl font-black text-red-700 mt-1 block" data-counter="{{ $absentCount }}">0</span>
            </div>
        </div>
    </div>

    {{-- Content Grid: Upcoming Schedules & Recent Attendance --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Upcoming Schedules --}}
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-[#101D3B]">Jadwal Latihan Marching Band</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Agenda kegiatan, dresscode & jadwal latihan</p>
                </div>
                <a href="{{ route('portal.schedules') }}" class="text-xs font-bold text-[#C6A044] hover:underline flex items-center gap-1">
                    Semua &rarr;
                </a>
            </div>

            <div class="space-y-3">
                @forelse($upcomingSchedules as $sched)
                <div class="p-4 rounded-2xl border border-gray-100 hover:border-[#C6A044]/40 hover:bg-amber-50/20 transition flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="space-y-1.5 min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <h4 class="text-xs sm:text-sm font-bold text-[#101D3B]">{{ $sched->title }}</h4>
                            <span class="text-[9px] font-bold px-2 py-0.5 rounded-full uppercase {{ $sched->type === 'competition' ? 'bg-red-100 text-red-700' : ($sched->type === 'additional' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700') }}">
                                {{ $sched->type_label }}
                            </span>
                        </div>
                        <div class="text-[11px] text-gray-500 flex flex-wrap items-center gap-2.5">
                            <span class="font-bold text-[#101D3B]">{{ $sched->date->format('d M Y') }}</span>
                            <span>•</span>
                            <span class="font-mono">{{ substr($sched->start_time, 0, 5) }} - {{ substr($sched->end_time, 0, 5) }} WIB</span>
                            <span>•</span>
                            <span>📍 {{ $sched->location }}</span>
                        </div>
                        @if($sched->dress_code)
                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-lg bg-amber-50 border border-amber-200/60 text-[10px] font-bold text-[#101D3B]">
                            <span>👕 Baju:</span>
                            <span class="text-amber-800">{{ $sched->dress_code }}</span>
                        </div>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 self-end sm:self-center flex-shrink-0">
                        <a href="{{ route('permission-requests.create') }}" class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-bold text-gray-600 bg-gray-100 hover:bg-gray-200 transition">
                            Minta Izin
                        </a>
                    </div>
                </div>
                @empty
                <p class="text-xs text-gray-400 py-6 text-center">Belum ada jadwal latihan mendatang.</p>
                @endforelse
            </div>
        </div>

        {{-- Recent Attendance History --}}
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-[#101D3B]">Riwayat Presensi Terakhir</h3>
                    <p class="text-xs text-gray-400 mt-0.5">5 catatan kehadiran latihan terakhir Anda</p>
                </div>
                <a href="{{ route('portal.attendance') }}" class="text-xs font-bold text-[#C6A044] hover:underline">
                    Semua Riwayat &rarr;
                </a>
            </div>

            <div class="space-y-3">
                @forelse($recentAttendances as $att)
                <div class="p-3.5 rounded-2xl bg-gray-50 flex items-center justify-between gap-3 text-xs">
                    <div>
                        <span class="font-bold text-[#101D3B] block">
                            {{ $att->attendanceSession->practiceSchedule->title ?? 'Sesi Latihan' }}
                        </span>
                        <span class="text-[11px] text-gray-400">
                            {{ $att->attendanceSession->practiceSchedule->date->format('d M Y') ?? '-' }}
                            @if($att->check_in_time)
                                • Masuk pukul {{ $att->check_in_time->format('H:i') }} WIB
                            @endif
                        </span>
                    </div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase
                        {{ $att->status === 'present' ? 'bg-emerald-100 text-emerald-800' :
                           ($att->status === 'late' ? 'bg-amber-100 text-amber-800' :
                           ($att->status === 'permission' ? 'bg-blue-100 text-blue-800' :
                           ($att->status === 'sick' ? 'bg-purple-100 text-purple-800' : 'bg-red-100 text-red-800'))) }}">
                        {{ $att->status_label }}
                    </span>
                </div>
                @empty
                <p class="text-xs text-gray-400 py-6 text-center">Belum ada riwayat presensi tercatat.</p>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection
