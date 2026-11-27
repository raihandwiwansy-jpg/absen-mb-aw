@extends('layouts.app')

@section('title', 'Dashboard')
@section('breadcrumb', 'Ringkasan & Statistik Organisasi')

@section('content')
<div class="space-y-6">

    {{-- Welcome & Banner with Live Clock & 2026 Aesthetics --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-[#101D3B] via-[#202B40] to-[#101D3B] p-6 lg:p-8 text-white shadow-2xl border border-[#C6A044]/40">
        {{-- Decorative background glow --}}
        <div class="absolute -right-20 -top-20 w-80 h-80 bg-[#C6A044]/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full border-2 border-[#C6A044] p-1 bg-white/10 shadow-xl flex-shrink-0">
                    <img src="{{ asset('logo.jpg') }}" alt="Symphony Logo" class="w-full h-full rounded-full object-cover">
                </div>
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#C6A044]/20 border border-[#C6A044]/40 text-[#C6A044] text-xs font-bold tracking-wider uppercase mb-1.5">
                        <span class="w-2 h-2 rounded-full bg-[#C6A044] animate-ping"></span>
                        Symphony Marching Band • 2026
                    </div>
                    <h2 class="text-2xl lg:text-3xl font-black tracking-tight text-white">
                        Selamat Datang, <span class="text-[#C6A044]">{{ Auth::user()->name }}</span>
                    </h2>
                    <p class="text-xs sm:text-sm text-gray-300 mt-1 flex flex-wrap items-center gap-2 sm:gap-3">
                        <span>SMK Al Wasliyah Perdagangan</span>
                        <span>•</span>
                        <span class="font-mono text-[#C6A044] font-bold" id="dashboard-live-clock">--:--:-- WIB</span>
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 sm:gap-3">
                <a href="{{ route('attendance.scan') }}"
                   class="btn-gold inline-flex items-center gap-2 px-5 py-3 rounded-2xl font-bold text-xs sm:text-sm transition shadow-xl hover:shadow-2xl pulse-ring-gold"
                   style="background-color: #C6A044; color: #101D3B;">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-[#101D3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                    </svg>
                    Scan QR Absen
                </a>
                <a href="{{ route('schedules.create') }}" class="inline-flex items-center gap-2 px-4 py-3 rounded-2xl font-semibold text-xs sm:text-sm bg-white/10 hover:bg-white/20 text-white transition border border-white/20">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5 text-[#C6A044]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Jadwal Baru
                </a>
            </div>
        </div>

        @if($pendingPermissionsCount > 0)
        <div class="mt-4 pt-4 border-t border-white/10 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-xs text-amber-300">
            <div class="flex items-center gap-2">
                <span class="flex h-2 w-2 rounded-full bg-amber-400 animate-ping flex-shrink-0"></span>
                <span>Ada <strong>{{ $pendingPermissionsCount }}</strong> permohonan izin/sakit yang belum ditinjau.</span>
            </div>
            <a href="{{ route('permission-requests.index') }}" class="underline hover:text-white font-bold self-start sm:self-auto">Tinjau Sekarang &rarr;</a>
        </div>
        @endif
    </div>

    {{-- Main Stat Cards Grid with Animated Counter Numbers & Hover Lift --}}
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-4 gap-4 lg:gap-5">
        {{-- Total Members --}}
        <div class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Anggota</span>
                <div class="w-10 h-10 rounded-2xl bg-[#101D3B]/10 text-[#101D3B] flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-black text-[#101D3B]" data-counter="{{ $totalMembers }}">0</span>
                <span class="text-xs text-gray-400 font-medium">terdaftar</span>
            </div>
            <div class="mt-2 text-xs text-emerald-600 font-bold flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                <span>{{ $activeMembers }} aktif berpartisipasi</span>
            </div>
        </div>

        {{-- Present Today --}}
        <div class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Hadir Hari Ini</span>
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-black text-emerald-600" data-counter="{{ $presentToday }}">0</span>
                <span class="text-xs text-gray-400 font-medium">orang</span>
            </div>
            <div class="mt-2 text-xs text-amber-600 font-medium">
                + {{ $lateToday }} terlambat
            </div>
        </div>

        {{-- Permission / Sick Today --}}
        <div class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Izin & Sakit</span>
                <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-2">
                <span class="text-3xl font-black text-blue-600" data-counter="{{ $permissionToday + $sickToday }}">0</span>
                <span class="text-xs text-gray-400 font-medium">orang</span>
            </div>
            <div class="mt-2 text-xs text-gray-400">
                {{ $permissionToday }} izin / {{ $sickToday }} sakit
            </div>
        </div>

        {{-- Attendance Percentage --}}
        <div class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tingkat Kehadiran</span>
                <div class="w-10 h-10 rounded-2xl bg-[#C6A044]/15 text-[#C6A044] flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                </div>
            </div>
            <div class="mt-4 flex items-baseline gap-1">
                <span class="text-3xl font-black text-[#101D3B]" data-counter="{{ $overallPercentage }}" data-counter-suffix="%">0%</span>
            </div>
            <div class="mt-2 w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                <div class="bg-gradient-to-r from-[#101D3B] to-[#C6A044] h-2 rounded-full transition-all duration-1000" style="width: {{ $overallPercentage }}%"></div>
            </div>
        </div>
    </div>

    {{-- Charts Section with Interactive Time-Range Toggler --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6" x-data="{ chartRange: 'weekly' }">
        {{-- Attendance Chart (Toggleable: 7 Days / 6 Months) --}}
        <div class="lg:col-span-2 bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex flex-col justify-between">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <div>
                    <h3 class="text-base font-bold text-[#101D3B]">Tren Kehadiran Organisasi</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Analisis visual kehadiran latihan Symphony</p>
                </div>

                {{-- Chart Range Selector --}}
                <div class="flex items-center gap-1.5 p-1 bg-gray-100 rounded-xl text-xs">
                    <button type="button"
                            @click="chartRange = 'weekly'; switchChart('weekly')"
                            :class="chartRange === 'weekly' ? 'bg-white text-[#101D3B] font-bold shadow-xs' : 'text-gray-500 hover:text-gray-900'"
                            class="px-3 py-1 rounded-lg transition">
                        7 Hari Terakhir
                    </button>
                    <button type="button"
                            @click="chartRange = 'monthly'; switchChart('monthly')"
                            :class="chartRange === 'monthly' ? 'bg-white text-[#101D3B] font-bold shadow-xs' : 'text-gray-500 hover:text-gray-900'"
                            class="px-3 py-1 rounded-lg transition">
                        6 Bulan Terakhir
                    </button>
                </div>
            </div>

            <div class="h-64 sm:h-72 relative my-auto">
                <canvas id="attendanceChartCanvas"></canvas>
            </div>

            <div class="mt-4 pt-3 border-t border-gray-100 flex flex-wrap items-center justify-between text-xs text-gray-500">
                <div class="flex items-center gap-4">
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-[#101D3B]"></span> Hadir</span>
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-[#C6A044]"></span> Terlambat</span>
                    <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-blue-400"></span> Izin / Sakit</span>
                </div>
                <span class="text-[11px] text-gray-400">Dihitung otomatis dari database</span>
            </div>
        </div>

        {{-- Division Distribution Doughnut --}}
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex flex-col justify-between">
            <div class="mb-4">
                <h3 class="text-base font-bold text-[#101D3B]">Anggota per Divisi</h3>
                <p class="text-xs text-gray-400 mt-0.5">Komposisi seksi instrumen</p>
            </div>

            <div class="h-52 relative my-auto">
                <canvas id="divisionDoughnutChart"></canvas>
            </div>

            <div class="mt-4 space-y-2 text-xs">
                @foreach($divisionStats as $stat)
                <div class="flex items-center justify-between py-1.5 border-b border-gray-50 last:border-0">
                    <span class="flex items-center gap-2 text-gray-700">
                        <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $stat['color'] }}"></span>
                        {{ $stat['name'] }}
                    </span>
                    <span class="font-bold text-[#101D3B]">{{ $stat['active_members'] }} org <span class="text-gray-400 font-normal">({{ $stat['percentage'] }}%)</span></span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Bottom Grid: Top Disciplined + Upcoming Schedules + Recent Activities --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Top Disciplined Members --}}
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-[#101D3B]">Peringkat Terdisiplin</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Persentase kehadiran tertinggi</p>
                </div>
                <span class="p-2 rounded-xl bg-[#C6A044]/15 text-[#C6A044]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                </span>
            </div>

            <div class="space-y-3">
                @forelse($topMembers as $index => $item)
                <div class="flex items-center gap-3 p-3 rounded-2xl hover:bg-amber-50/20 transition border border-gray-50">
                    <div class="w-7 h-7 rounded-xl flex items-center justify-center font-black text-xs {{ $index === 0 ? 'bg-[#C6A044] text-[#101D3B] shadow-sm' : ($index === 1 ? 'bg-gray-300 text-gray-700' : 'bg-gray-100 text-gray-600') }}">
                        {{ $index + 1 }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-[#101D3B] truncate">{{ $item['member']->full_name }}</p>
                        <p class="text-[11px] text-gray-400 truncate">{{ $item['member']->division->name ?? 'Tanpa Divisi' }} • {{ $item['member']->member_code }}</p>
                    </div>
                    <div class="text-right">
                        <span class="inline-block px-2.5 py-1 rounded-lg text-xs font-black text-emerald-800 bg-emerald-50">
                            {{ $item['percentage'] }}%
                        </span>
                    </div>
                </div>
                @empty
                <p class="text-xs text-gray-400 py-6 text-center">Belum ada data kehadiran tercatat</p>
                @endforelse
            </div>
        </div>

        {{-- Upcoming Practice Schedules --}}
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-[#101D3B]">Jadwal Mendatang</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Sesi latihan & persiapan lomba</p>
                </div>
                <a href="{{ route('schedules.index') }}" class="text-xs text-[#C6A044] hover:underline font-bold">Semua Jadwal &rarr;</a>
            </div>

            <div class="space-y-3">
                @forelse($upcomingSchedules as $sched)
                <div class="p-3.5 rounded-2xl border border-gray-100 hover:border-[#C6A044]/40 hover:bg-amber-50/20 transition">
                    <div class="flex items-center justify-between gap-2">
                        <h4 class="text-xs font-bold text-[#101D3B] truncate">{{ $sched->title }}</h4>
                        <span class="text-[9px] px-2 py-0.5 rounded-full font-bold uppercase {{ $sched->type === 'competition' ? 'bg-red-100 text-red-700' : ($sched->type === 'additional' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700') }}">
                            {{ $sched->type_label }}
                        </span>
                    </div>
                    <div class="mt-2 flex items-center gap-3 text-[11px] text-gray-500">
                        <span class="flex items-center gap-1 font-medium">
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ $sched->date->format('d M Y') }}
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            {{ substr($sched->start_time, 0, 5) }} - {{ substr($sched->end_time, 0, 5) }}
                        </span>
                    </div>
                </div>
                @empty
                <p class="text-xs text-gray-400 py-6 text-center">Tidak ada jadwal latihan terdekat</p>
                @endforelse
            </div>
        </div>

        {{-- Recent Activities --}}
        <div class="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-[#101D3B]">Aktivitas Terkini</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Log perubahan sistem & presensi</p>
                </div>
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
            </div>

            <div class="space-y-3 overflow-y-auto max-h-72 pr-1">
                @forelse($recentActivities as $activity)
                <div class="flex items-start gap-2.5 text-xs pb-3 border-b border-gray-50 last:border-0">
                    <div class="w-7 h-7 rounded-xl bg-gray-100 flex items-center justify-center text-gray-500 flex-shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-gray-800 font-semibold leading-snug">{{ $activity->description }}</p>
                        <p class="text-[10px] text-gray-400 mt-0.5">{{ $activity->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                @empty
                <p class="text-xs text-gray-400 py-6 text-center">Belum ada aktivitas tercatat</p>
                @endforelse
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let attendanceChartInstance = null;

const weeklyData = {
    labels: @json($weeklyLabels),
    datasets: [
        {
            label: 'Hadir',
            data: @json($weeklyPresent),
            backgroundColor: '#101D3B',
            borderRadius: 8,
        },
        {
            label: 'Terlambat',
            data: @json($weeklyLate),
            backgroundColor: '#C6A044',
            borderRadius: 8,
        },
        {
            label: 'Izin / Sakit',
            data: @json($weeklyPermission),
            backgroundColor: '#60a5fa',
            borderRadius: 8,
        }
    ]
};

const monthlyData = {
    labels: @json($monthlyLabels),
    datasets: [
        {
            label: 'Hadir',
            data: @json($monthlyPresent),
            backgroundColor: '#101D3B',
            borderRadius: 8,
        },
        {
            label: 'Alpa / Tidak Hadir',
            data: @json($monthlyAbsent),
            backgroundColor: '#f87171',
            borderRadius: 8,
        }
    ]
};

function switchChart(range) {
    if (!attendanceChartInstance) return;
    const dataToUse = range === 'monthly' ? monthlyData : weeklyData;
    attendanceChartInstance.data.labels = dataToUse.labels;
    attendanceChartInstance.data.datasets = dataToUse.datasets;
    attendanceChartInstance.update();
}

document.addEventListener('DOMContentLoaded', function () {
    // Start live clock
    window.initDigitalClock('dashboard-live-clock');

    // Run smooth number counter
    window.animateAllCounters();

    // 1. Dynamic Attendance Bar Chart
    const ctx = document.getElementById('attendanceChartCanvas').getContext('2d');
    attendanceChartInstance = new Chart(ctx, {
        type: 'bar',
        data: weeklyData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { grid: { display: false } },
                y: { beginAtZero: true, ticks: { precision: 0 } }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#101D3B',
                    titleColor: '#C6A044',
                    bodyColor: '#ffffff',
                    padding: 12,
                    cornerRadius: 12,
                    borderColor: '#C6A044',
                    borderWidth: 1
                }
            }
        }
    });

    // 2. Division Doughnut Chart
    const ctxDivision = document.getElementById('divisionDoughnutChart').getContext('2d');
    const divisionLabels = @json($divisionStats->pluck('name'));
    const divisionCounts = @json($divisionStats->pluck('active_members'));
    const divisionColors = @json($divisionStats->pluck('color'));

    new Chart(ctxDivision, {
        type: 'doughnut',
        data: {
            labels: divisionLabels,
            datasets: [{
                data: divisionCounts,
                backgroundColor: divisionColors,
                borderWidth: 3,
                borderColor: '#ffffff',
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#101D3B',
                    titleColor: '#C6A044',
                    bodyColor: '#ffffff',
                    cornerRadius: 12,
                    padding: 12,
                    borderWidth: 1,
                    borderColor: '#C6A044'
                }
            },
            cutout: '68%'
        }
    });
});
</script>
@endpush
