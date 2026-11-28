@extends('layouts.app')

@section('title', 'Lembar Presensi - ' . ($session->practiceSchedule->title ?? 'Sesi'))
@section('breadcrumb')
    <a href="{{ route('attendance.sessions.index') }}" class="hover:underline">Sesi Absensi</a> &gt; #{{ $session->id }}
@endsection

@section('content')
<div class="space-y-6"
     x-data="attendanceSheet({
         sessionId: {{ $session->id }},
         isActive: {{ $session->is_active ? 'true' : 'false' }},
         stats: {
             total_members: {{ $stats['total_members'] }},
             present: {{ $stats['present'] }},
             late: {{ $stats['late'] }},
             permission: {{ $stats['permission'] }},
             sick: {{ $stats['sick'] }},
             absent: {{ $stats['absent'] }}
         },
         qrUrl: '{{ route('attendance.sessions.qr', $session) }}',
         liveDataUrl: '{{ route('attendance.sessions.live-data', $session) }}',
         manualUrl: '{{ route('attendance.manual') }}',
         bulkUrl: '{{ route('attendance.sessions.bulk-mark', $session) }}',
         refreshQrUrl: '{{ route('attendance.sessions.refresh', $session) }}'
     })">

    {{-- Top Session Control Card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
        <div>
            <div class="flex items-center gap-3">
                @if($session->is_active)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                        SESI AKTIF
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                        SESI DITUTUP ({{ $session->closed_at ? $session->closed_at->format('d/m/Y H:i') : '' }})
                    </span>
                @endif
                <span class="text-xs text-gray-400">Dibuka oleh: <strong>{{ $session->openedBy->name ?? '-' }}</strong></span>
            </div>

            <h2 class="text-2xl font-bold text-[#101D3B] mt-2">
                {{ $session->practiceSchedule->title ?? 'Latihan Marching Band' }}
            </h2>
            <p class="text-xs text-gray-500 mt-1 flex flex-wrap items-center gap-2 sm:gap-3">
                <span>{{ $session->practiceSchedule->date->format('l, d F Y') }}</span>
                <span>•</span>
                <span>{{ substr($session->practiceSchedule->start_time, 0, 5) }} - {{ substr($session->practiceSchedule->end_time, 0, 5) }} WIB</span>
                <span>•</span>
                <span>{{ $session->practiceSchedule->location }}</span>
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            @if($session->is_active)
                {{-- Show Projector / QR Modal Button --}}
                <button type="button" @click="openProjector()" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-[#101D3B] bg-[#C6A044] hover:bg-[#b59239] transition shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Mode Proyektor QR Code
                </button>

                {{-- Bulk Action Dropdown --}}
                <div class="relative" x-data="{ openBulk: false }">
                    <button type="button" @click="openBulk = !openBulk" @click.outside="openBulk = false" class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 transition">
                        <span>Aksi Massal</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="openBulk" x-transition class="dropdown-menu absolute right-0 top-full mt-2 w-56 p-1">
                        <button type="button" @click="openBulk = false; executeBulkMark('present', 'all_absent')" class="dropdown-item text-xs w-full text-left font-medium">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 mr-1.5"></span>
                            Tandai Semua Sisa Hadir
                        </button>
                        <button type="button" @click="openBulk = false; executeBulkMark('late', 'all_absent')" class="dropdown-item text-xs w-full text-left font-medium">
                            <span class="w-2 h-2 rounded-full bg-amber-500 mr-1.5"></span>
                            Tandai Sisa Terlambat
                        </button>
                        <button type="button" @click="openBulk = false; executeBulkMark('absent', 'all')" class="dropdown-item text-xs w-full text-left font-medium danger">
                            <span class="w-2 h-2 rounded-full bg-red-500 mr-1.5"></span>
                            Reset Semua ke Alpa
                        </button>
                    </div>
                </div>

                {{-- Close Session --}}
                <form action="{{ route('attendance.sessions.close', $session) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menutup sesi absensi ini? Anggota tidak akan bisa scan lagi.');" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 transition">
                        Tutup Sesi
                    </button>
                </form>
            @endif

            <a href="{{ route('attendance.sessions.index') }}" class="px-4 py-2.5 rounded-xl text-xs font-medium text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 transition">
                Kembali
            </a>
        </div>
    </div>

    {{-- Interactive Dynamic Stats Bar --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="bg-white p-4 rounded-xl border border-gray-100 text-center shadow-xs">
            <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Total Anggota</span>
            <span class="text-2xl font-black text-[#101D3B] mt-1 block" x-text="stats.total_members"></span>
        </div>
        <div class="bg-emerald-50/70 border border-emerald-200/50 p-4 rounded-xl text-center shadow-xs">
            <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider block">Hadir</span>
            <span class="text-2xl font-black text-emerald-700 mt-1 block" x-text="stats.present"></span>
        </div>
        <div class="bg-amber-50/70 border border-amber-200/50 p-4 rounded-xl text-center shadow-xs">
            <span class="text-[10px] font-bold text-amber-800 uppercase tracking-wider block">Terlambat</span>
            <span class="text-2xl font-black text-amber-700 mt-1 block" x-text="stats.late"></span>
        </div>
        <div class="bg-blue-50/70 border border-blue-200/50 p-4 rounded-xl text-center shadow-xs">
            <span class="text-[10px] font-bold text-blue-800 uppercase tracking-wider block">Izin</span>
            <span class="text-2xl font-black text-blue-700 mt-1 block" x-text="stats.permission"></span>
        </div>
        <div class="bg-purple-50/70 border border-purple-200/50 p-4 rounded-xl text-center shadow-xs">
            <span class="text-[10px] font-bold text-purple-800 uppercase tracking-wider block">Sakit</span>
            <span class="text-2xl font-black text-purple-700 mt-1 block" x-text="stats.sick"></span>
        </div>
        <div class="bg-red-50/70 border border-red-200/50 p-4 rounded-xl text-center shadow-xs">
            <span class="text-[10px] font-bold text-red-800 uppercase tracking-wider block">Alpa / Belum</span>
            <span class="text-2xl font-black text-red-700 mt-1 block" x-text="stats.absent"></span>
        </div>
    </div>

    {{-- Attendance Sheet Table with Live Filter & Instant Async Status Toggle --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        {{-- Table Toolbar --}}
        <div class="p-5 border-b border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 flex-wrap">
                    <h3 class="text-base font-bold text-[#101D3B]">Lembar Presensi Kehadiran Realtime</h3>
                    <template x-if="isActive">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Live Auto-Sync (No Refresh)
                        </span>
                    </template>
                </div>
                <p class="text-xs text-gray-400 mt-1 flex items-center gap-2">
                    <span>Ubah status instan atau pantau hasil scan QR secara langsung.</span>
                    <span class="hidden sm:inline">•</span>
                    <span class="text-[11px] text-gray-400">Sinkronisasi: <span class="font-mono text-navy font-semibold" x-text="lastSyncTime"></span></span>
                </p>
            </div>

            {{-- Live Search & Division Filter --}}
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative min-w-[200px] flex-1 sm:flex-none">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                    <input type="text"
                           x-model="searchQuery"
                           placeholder="Cari nama atau kode..."
                           class="w-full text-xs rounded-xl border-gray-200 pl-9 pr-3 py-2 focus:border-[#C6A044] focus:ring-[#C6A044]">
                </div>

                <select x-model="selectedDivision" class="text-xs rounded-xl border-gray-200 py-2 px-3 focus:border-[#C6A044] focus:ring-[#C6A044]">
                    <option value="">Semua Divisi</option>
                    @foreach($attendances->pluck('member.division.name')->filter()->unique() as $divName)
                        <option value="{{ $divName }}">{{ $divName }}</option>
                    @endforeach
                </select>

                <select x-model="selectedStatus" class="text-xs rounded-xl border-gray-200 py-2 px-3 focus:border-[#C6A044] focus:ring-[#C6A044]">
                    <option value="">Semua Status</option>
                    <option value="present">Hadir</option>
                    <option value="late">Terlambat</option>
                    <option value="permission">Izin</option>
                    <option value="sick">Sakit</option>
                    <option value="absent">Alpa</option>
                </select>
            </div>
        </div>

        {{-- Table with Responsive Wrapper --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs min-w-[640px]">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                        <th class="p-4" style="width: 45px;">No</th>
                        <th class="p-4">Anggota</th>
                        <th class="p-4">Divisi & Instrumen</th>
                        <th class="p-4" style="width: 200px;">Status Presensi (Instan)</th>
                        <th class="p-4">Waktu Check-in</th>
                        <th class="p-4">Dicatat Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($attendances as $index => $att)
                    @if($att->member)
                    <tr class="hover:bg-amber-50/20 transition-all duration-300"
                        data-name="{{ strtolower($att->member->full_name) }}"
                        data-code="{{ strtolower($att->member->member_code) }}"
                        data-division="{{ $att->member->division->name ?? '' }}"
                        x-show="matchesFilter($el.dataset.name, $el.dataset.code, $el.dataset.division, rows[{{ $att->member_id }}].status)"
                        :class="{ 'live-updated-row': rows[{{ $att->member_id }}].justUpdated }">
                        <td class="p-4 text-gray-400 font-mono">{{ $index + 1 }}</td>
                        <td class="p-4">
                            <div class="font-bold text-[#101D3B]">{{ $att->member->full_name }}</div>
                            <div class="text-[11px] text-gray-400 font-mono">{{ $att->member->member_code }}</div>
                        </td>
                        <td class="p-4">
                            <div class="text-gray-900 font-medium">{{ $att->member->division->name ?? '-' }}</div>
                            <div class="text-[11px] text-gray-400">{{ $att->member->instrument->name ?? '-' }}</div>
                        </td>
                        <td class="p-4">
                            {{-- Interactive Instant Status Selector with Loading Indicator --}}
                            <div class="flex items-center gap-2">
                                <select @change="changeStatus({{ $att->member_id }}, $event.target.value)"
                                        :value="rows[{{ $att->member_id }}].status"
                                        :disabled="rows[{{ $att->member_id }}].isSaving"
                                        :class="getStatusSelectClass(rows[{{ $att->member_id }}].status)"
                                        class="form-select text-xs py-1.5 px-3 rounded-xl font-bold cursor-pointer transition">
                                    <option value="present">Hadir</option>
                                    <option value="late">Terlambat</option>
                                    <option value="permission">Izin</option>
                                    <option value="sick">Sakit</option>
                                    <option value="absent">Alpa</option>
                                </select>

                                <template x-if="rows[{{ $att->member_id }}].isSaving">
                                    <svg class="animate-spin h-4 w-4 text-[#C6A044]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                </template>
                            </div>
                        </td>
                        <td class="p-4 font-mono text-gray-600">
                            <span x-text="rows[{{ $att->member_id }}].check_in_time"></span>
                        </td>
                        <td class="p-4 text-gray-500 text-[11px]">
                            <span x-text="rows[{{ $att->member_id }}].recorded_by"></span>
                        </td>
                    </tr>
                    @endif
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-400">Belum ada anggota yang terdata dalam sesi ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ============================================================
         FULLSCREEN "MODE PROYEKTOR / HALL PRESENTER"
         ============================================================ --}}
    <div x-show="showProjectorModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-[100] bg-[#101D3B] text-white flex flex-col justify-between p-6 lg:p-12 overflow-y-auto"
         style="display: none;">

        {{-- Top Projector Bar --}}
        <div class="flex flex-wrap items-center justify-between gap-4 border-b border-white/10 pb-5 sm:pb-6">
            <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                <img src="{{ asset('logo.jpg') }}" alt="Symphony Logo" class="w-12 h-12 sm:w-16 sm:h-16 rounded-full border-2 border-[#C6A044] p-0.5 bg-white/10 flex-shrink-0">
                <div class="min-w-0">
                    <span class="inline-flex items-center gap-2 px-3 py-0.5 rounded-full bg-[#C6A044]/20 border border-[#C6A044]/40 text-[#C6A044] text-[10px] font-bold tracking-widest uppercase">
                        Symphony Marching Band • Live Projector
                    </span>
                    <h1 class="text-lg sm:text-xl lg:text-2xl font-black text-white mt-1 truncate">
                        {{ $session->practiceSchedule->title ?? 'Sesi Latihan' }}
                    </h1>
                </div>
            </div>

            <div class="flex items-center gap-4 sm:gap-6 ml-auto">
                {{-- Live Clock --}}
                <div class="text-right">
                    <p class="text-[10px] sm:text-xs text-gray-400 uppercase tracking-widest">Waktu Sekarang</p>
                    <p class="text-lg sm:text-2xl font-black text-[#C6A044] font-mono" x-text="projectorClock">--:--:-- WIB</p>
                </div>

                {{-- Close Projector Button --}}
                <button type="button" @click="closeProjector()" class="p-3 rounded-2xl bg-white/10 hover:bg-white/20 text-white transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>

        {{-- Projector Body: Big QR Center + Live Attendee Stream Right --}}
        <div class="my-auto py-8 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">

            {{-- Big QR Code Center --}}
            <div class="lg:col-span-7 flex flex-col items-center justify-center text-center">
                <div class="relative p-4 sm:p-6 rounded-3xl bg-[#F7F5EF] border-4 border-[#C6A044] shadow-2xl pulse-ring-gold">
                    <img :src="currentQrUrl" alt="QR Code Presensi" class="w-56 h-56 sm:w-72 sm:h-72 lg:w-80 lg:h-80 object-contain mx-auto">
                    {{-- Logo in center of QR decorative overlay --}}
                    <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full border-2 border-[#C6A044] p-0.5 bg-white shadow-md">
                            <img src="{{ asset('logo.jpg') }}" alt="Center Logo" class="w-full h-full rounded-full object-cover">
                        </div>
                    </div>
                </div>

                <p class="text-xs sm:text-sm text-gray-300 font-medium mt-5 sm:mt-6">
                    Arahkan kamera smartphone Anda ke QR Code untuk mencatat kehadiran secara otomatis.
                </p>

                <div class="mt-4 flex flex-wrap items-center justify-center gap-3 sm:gap-4">
                    <button type="button" @click="refreshQr()" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-navy bg-[#C6A044] hover:bg-[#b59239] transition shadow-md">
                        <svg class="w-4 h-4 text-navy" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        Refresh Token QR
                    </button>
                    <span class="text-xs text-gray-400">Token diperbarui otomatis setiap beberapa menit</span>
                </div>
            </div>

            {{-- Live Check-in Feed --}}
            <div class="lg:col-span-5 bg-white/5 border border-white/10 rounded-3xl p-6 backdrop-blur-xl">
                <div class="flex items-center justify-between border-b border-white/10 pb-3 mb-4">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                        <h3 class="text-sm font-bold uppercase tracking-wider text-white">Live Check-in</h3>
                    </div>
                    <span class="text-xs text-[#C6A044] font-bold" x-text="stats.present + ' Hadir'"></span>
                </div>

                <div class="space-y-2.5 max-h-80 overflow-y-auto pr-1">
                    <template x-for="item in recentCheckIns" :key="item.member_code + item.check_in_time">
                        <div class="flex items-center justify-between p-3 rounded-xl bg-white/10 border border-white/5 text-xs animate-fade-in">
                            <div class="flex items-center gap-3">
                                <div class="w-7 h-7 rounded-full bg-[#C6A044] text-[#101D3B] flex items-center justify-center font-bold text-xs">
                                    ✓
                                </div>
                                <div>
                                    <p class="font-bold text-white text-xs" x-text="item.member_name"></p>
                                    <p class="text-[10px] text-gray-300" x-text="item.division + ' • ' + item.instrument"></p>
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-[11px] font-mono text-emerald-300 font-bold block" x-text="item.check_in_time"></span>
                                <span class="text-[9px] text-gray-400" x-text="item.time_ago"></span>
                            </div>
                        </div>
                    </template>
                    <div x-show="recentCheckIns.length === 0" class="py-12 text-center text-xs text-gray-400">
                        Menunggu scan kehadiran pertama anggota...
                    </div>
                </div>
            </div>

        </div>

        {{-- Bottom Projector Stats Bar --}}
        <div class="pt-6 border-t border-white/10 grid grid-cols-3 sm:grid-cols-6 gap-4 text-center">
            <div>
                <p class="text-[10px] uppercase tracking-wider text-gray-400">Total</p>
                <p class="text-xl font-bold text-white mt-0.5" x-text="stats.total_members"></p>
            </div>
            <div>
                <p class="text-[10px] uppercase tracking-wider text-emerald-400">Hadir</p>
                <p class="text-xl font-bold text-emerald-400 mt-0.5" x-text="stats.present"></p>
            </div>
            <div>
                <p class="text-[10px] uppercase tracking-wider text-amber-400">Terlambat</p>
                <p class="text-xl font-bold text-amber-400 mt-0.5" x-text="stats.late"></p>
            </div>
            <div>
                <p class="text-[10px] uppercase tracking-wider text-blue-400">Izin</p>
                <p class="text-xl font-bold text-blue-400 mt-0.5" x-text="stats.permission"></p>
            </div>
            <div>
                <p class="text-[10px] uppercase tracking-wider text-purple-400">Sakit</p>
                <p class="text-xl font-bold text-purple-400 mt-0.5" x-text="stats.sick"></p>
            </div>
            <div>
                <p class="text-[10px] uppercase tracking-wider text-red-400">Belum Hadir</p>
                <p class="text-xl font-bold text-red-400 mt-0.5" x-text="stats.absent"></p>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
function attendanceSheet(config) {
    return {
        sessionId: config.sessionId,
        isActive: config.isActive,
        stats: config.stats,
        currentQrUrl: config.qrUrl,
        showProjectorModal: false,
        searchQuery: '',
        selectedDivision: '',
        selectedStatus: '',
        recentCheckIns: [],
        autoSyncInterval: null,
        projectorInterval: null,
        lastSyncTime: new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' }),
        isPolling: false,
        projectorClock: '--:--:-- WIB',

        // Initialize state map for each member
        rows: {
            @foreach($attendances as $att)
            @if($att->member)
            {{ $att->member_id }}: {
                status: @json($att->status),
                check_in_time: @json($att->check_in_time ? $att->check_in_time->format('H:i:s') : '-'),
                recorded_by: @json($att->recordedBy?->name ?? 'Sistem'),
                isSaving: false,
                justUpdated: false,
            },
            @endif
            @endforeach
        },

        updateClock() {
            const now = new Date();
            const h = String(now.getHours()).padStart(2, '0');
            const m = String(now.getMinutes()).padStart(2, '0');
            const s = String(now.getSeconds()).padStart(2, '0');
            this.projectorClock = `${h}:${m}:${s} WIB`;
        },

        init() {
            this.updateClock();
            setInterval(() => this.updateClock(), 1000);

            // Start continuous background auto-sync without refresh if session is active
            if (this.isActive) {
                this.startAutoSync();
            }
        },

        startAutoSync() {
            if (this.autoSyncInterval) return;
            // Poll every 3 seconds for seamless instant updates
            this.autoSyncInterval = setInterval(() => {
                this.pollSync();
            }, 3000);
        },

        pollSync() {
            if (this.isPolling) return;
            this.isPolling = true;

            fetch(config.liveDataUrl, {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                this.isPolling = false;
                this.lastSyncTime = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });

                if (typeof data.is_active === 'boolean' && !data.is_active) {
                    this.isActive = false;
                    if (this.autoSyncInterval) {
                        clearInterval(this.autoSyncInterval);
                        this.autoSyncInterval = null;
                    }
                }

                if (data.stats) {
                    this.stats = data.stats;
                }

                if (data.recent_check_ins) {
                    const prevCount = this.recentCheckIns.length;
                    this.recentCheckIns = data.recent_check_ins;
                    if (this.showProjectorModal && data.recent_check_ins.length > prevCount && prevCount > 0) {
                        window.playAudioFeedback('success');
                    }
                }

                // Check all members for any new check-in or status change from QR scan or other admin
                if (data.all_attendances && Array.isArray(data.all_attendances)) {
                    data.all_attendances.forEach(item => {
                        const row = this.rows[item.member_id];
                        if (row) {
                            if (row.status !== item.status) {
                                row.status = item.status;
                                row.check_in_time = item.check_in_time || '-';
                                row.recorded_by = item.recorded_by || 'Sistem';
                                row.justUpdated = true;
                                setTimeout(() => { row.justUpdated = false; }, 2800);

                                if (['present', 'late'].includes(item.status)) {
                                    window.playAudioFeedback('success');
                                    window.showToast(`${item.member_name} tercatat ${item.status_label}!`, 'success');
                                }
                            } else {
                                if (item.check_in_time && row.check_in_time !== item.check_in_time) {
                                    row.check_in_time = item.check_in_time;
                                }
                                if (item.recorded_by) {
                                    row.recorded_by = item.recorded_by;
                                }
                            }
                        }
                    });
                }
            })
            .catch(() => {
                this.isPolling = false;
            });
        },

        matchesFilter(name, code, division, status) {
            const matchesSearch = !this.searchQuery ||
                name.includes(this.searchQuery.toLowerCase()) ||
                code.includes(this.searchQuery.toLowerCase());

            const matchesDiv = !this.selectedDivision || division === this.selectedDivision;
            const matchesSt = !this.selectedStatus || status === this.selectedStatus;

            return matchesSearch && matchesDiv && matchesSt;
        },

        getStatusSelectClass(status) {
            switch(status) {
                case 'present':
                    return 'bg-emerald-50 text-emerald-800 border-emerald-300';
                case 'late':
                    return 'bg-amber-50 text-amber-800 border-amber-300';
                case 'permission':
                    return 'bg-blue-50 text-blue-800 border-blue-300';
                case 'sick':
                    return 'bg-purple-50 text-purple-800 border-purple-300';
                default:
                    return 'bg-red-50 text-red-800 border-red-300';
            }
        },

        changeStatus(memberId, newStatus) {
            const row = this.rows[memberId];
            if (!row) return;

            row.isSaving = true;

            fetch(config.manualUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({
                    attendance_session_id: this.sessionId,
                    member_id: memberId,
                    status: newStatus
                })
            })
            .then(res => res.json())
            .then(data => {
                row.isSaving = false;
                if (data.success) {
                    row.status = newStatus;
                    row.check_in_time = data.check_in_time;
                    row.recorded_by = data.recorded_by;
                    row.justUpdated = true;
                    setTimeout(() => { row.justUpdated = false; }, 2800);

                    // Update stats
                    if (data.stats) {
                        this.stats = data.stats;
                    }

                    window.playAudioFeedback('success');
                    window.showToast(data.message, 'success');
                } else {
                    window.showToast(data.message || 'Gagal mengubah status', 'error');
                }
            })
            .catch(err => {
                row.isSaving = false;
                window.showToast('Gagal menghubungi server.', 'error');
            });
        },

        executeBulkMark(status, target) {
            if (!confirm(`Konfirmasi: Ubah status presensi menjadi ${status.toUpperCase()}?`)) return;

            fetch(config.bulkUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({
                    status: status,
                    target: target
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    this.stats = data.stats;
                    // Update rows client side
                    for (const id in this.rows) {
                        if (target === 'all' || this.rows[id].status === 'absent') {
                            this.rows[id].status = status;
                            this.rows[id].justUpdated = true;
                            if (['present', 'late'].includes(status)) {
                                const now = new Date();
                                this.rows[id].check_in_time = String(now.getHours()).padStart(2,'0') + ':' + String(now.getMinutes()).padStart(2,'0') + ':' + String(now.getSeconds()).padStart(2,'0');
                            }
                        }
                    }
                    setTimeout(() => {
                        for (const id in this.rows) {
                            this.rows[id].justUpdated = false;
                        }
                    }, 2800);
                    window.playAudioFeedback('success');
                    window.showToast(data.message, 'success');
                }
            })
            .catch(err => {
                window.showToast('Terjadi kesalahan koneksi server.', 'error');
            });
        },

        openProjector() {
            this.showProjectorModal = true;
            this.refreshQr();
            this.pollSync();
        },

        closeProjector() {
            this.showProjectorModal = false;
        },

        refreshQr() {
            this.currentQrUrl = config.qrUrl + '?t=' + new Date().getTime();
        }
    };
}
</script>
@endpush
