@extends('layouts.app')

@section('title', 'Jadwal Latihan Marching Band')
@section('breadcrumb', 'Jadwal & Agenda Latihan')

@push('styles')
<style>
    /* FullCalendar Symphony Custom Styling */
    .fc .fc-toolbar-title {
        font-size: 1.1rem !important;
        font-weight: 800 !important;
        color: #101D3B !important;
    }
    .fc .fc-button-primary {
        background-color: #101D3B !important;
        border-color: #101D3B !important;
        font-size: 0.75rem !important;
        font-weight: 700 !important;
        border-radius: 0.75rem !important;
        padding: 0.4rem 0.8rem !important;
    }
    .fc .fc-button-primary:hover {
        background-color: #1a2d57 !important;
    }
    .fc .fc-button-active {
        background-color: #C6A044 !important;
        border-color: #C6A044 !important;
        color: #101D3B !important;
    }
    .fc .fc-col-header-cell {
        background-color: #F7F5EF !important;
        font-size: 0.75rem !important;
        font-weight: 700 !important;
        color: #101D3B !important;
        padding: 0.5rem 0 !important;
    }
    .fc .fc-event {
        border-radius: 0.5rem !important;
        padding: 2px 6px !important;
        font-size: 0.75rem !important;
        font-weight: 600 !important;
        cursor: pointer !important;
        transition: transform 0.15s ease !important;
    }
    .fc .fc-event:hover {
        transform: scale(1.02);
    }
</style>
@endpush

@section('content')
<div class="space-y-6" x-data="{
    activeTab: 'list', // 'list' or 'calendar'
    calendarInitialized: false,
    selectedEvent: null,
    showEventModal: false,

    switchTab(tab) {
        this.activeTab = tab;
        if (tab === 'calendar' && !this.calendarInitialized) {
            this.$nextTick(() => {
                initCalendar();
                this.calendarInitialized = true;
            });
        }
    }
}">

    @if($activeSession ?? null)
    {{-- Active Attendance Session Alert --}}
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

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-[#101D3B]">Jadwal Latihan Marching Band</h2>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Agenda resmi latihan rutin, instruksi pakaian / dresscode & kalender kegiatan</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            {{-- Tab Switcher --}}
            <div class="p-1 rounded-xl bg-gray-100 flex items-center gap-1 text-xs">
                <button type="button"
                        @click="switchTab('list')"
                        :class="activeTab === 'list' ? 'bg-white text-[#101D3B] font-bold shadow-xs' : 'text-gray-500 hover:text-gray-900'"
                        class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    Daftar Jadwal
                </button>
                <button type="button"
                        @click="switchTab('calendar')"
                        :class="activeTab === 'calendar' ? 'bg-white text-[#101D3B] font-bold shadow-xs' : 'text-gray-500 hover:text-gray-900'"
                        class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-[#C6A044]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Kalender Interaktif
                </button>
            </div>

            <a href="{{ route('attendance.scan') }}" class="btn-primary inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-[#101D3B] hover:bg-[#1a2d57] transition shadow-md">
                <svg class="w-4 h-4 text-[#C6A044]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                Scan QR Presensi
            </a>
        </div>
    </div>

    {{-- TAB 1: LIST / TABLE VIEW --}}
    <div x-show="activeTab === 'list'" class="space-y-4">

        {{-- Filter Bar --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4">
            <form action="{{ route('portal.schedules') }}" method="GET" class="flex flex-wrap items-center gap-3">
                <div class="w-full sm:w-auto">
                    <select name="type" onchange="this.form.submit()" class="w-full rounded-xl border-gray-200 text-xs focus:ring-[#C6A044] focus:border-[#C6A044]">
                        <option value="">Semua Jenis Latihan</option>
                        <option value="regular" {{ request('type') === 'regular' ? 'selected' : '' }}>Latihan Rutin</option>
                        <option value="additional" {{ request('type') === 'additional' ? 'selected' : '' }}>Latihan Tambahan</option>
                        <option value="competition" {{ request('type') === 'competition' ? 'selected' : '' }}>Persiapan Lomba</option>
                    </select>
                </div>
                <div class="w-full sm:w-auto">
                    <input type="month" name="month" value="{{ request('month') }}" onchange="this.form.submit()" class="w-full rounded-xl border-gray-200 text-xs focus:ring-[#C6A044] focus:border-[#C6A044]">
                </div>
                @if(request()->hasAny(['type', 'month']))
                    <a href="{{ route('portal.schedules') }}" class="text-xs text-red-600 hover:underline font-semibold ml-auto">
                        Reset Filter
                    </a>
                @endif
            </form>
        </div>

        {{-- Schedules Cards & Table --}}
        <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                            <th class="p-4">Tanggal & Waktu</th>
                            <th class="p-4">Jenis</th>
                            <th class="p-4">Agenda Latihan</th>
                            <th class="p-4">Dresscode (Pakaian)</th>
                            <th class="p-4">Lokasi</th>
                            <th class="p-4">Pelatih / Petugas</th>
                            <th class="p-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($schedules as $sched)
                        <tr class="hover:bg-gray-50/60 transition">
                            <td class="p-4">
                                <div class="font-bold text-[#101D3B] text-xs">
                                    {{ $sched->date->isoFormat('dddd, D MMMM Y') }}
                                </div>
                                <div class="font-mono text-gray-500 text-[11px] mt-0.5">
                                    {{ substr($sched->start_time, 0, 5) }} - {{ substr($sched->end_time, 0, 5) }} WIB
                                </div>
                                @if($sched->date->isToday())
                                    <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-emerald-100 text-emerald-800">
                                        Hari Ini
                                    </span>
                                @elseif($sched->date->isTomorrow())
                                    <span class="inline-block mt-1 px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-blue-100 text-blue-800">
                                        Besok
                                    </span>
                                @endif
                            </td>
                            <td class="p-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase
                                    {{ $sched->type === 'competition' ? 'bg-red-100 text-red-800' : ($sched->type === 'additional' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ $sched->type_label }}
                                </span>
                            </td>
                            <td class="p-4">
                                <div class="font-bold text-gray-900 text-xs">{{ $sched->title }}</div>
                                @if($sched->description)
                                    <p class="text-gray-400 text-[11px] mt-0.5 line-clamp-1">{{ $sched->description }}</p>
                                @endif
                            </td>
                            <td class="p-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-xs font-bold bg-amber-50 text-[#101D3B] border border-amber-200/80">
                                    👕 {{ $sched->dress_code ?? 'Seragam Latihan' }}
                                </span>
                            </td>
                            <td class="p-4">
                                <div class="flex items-center gap-1 text-gray-700 font-medium">
                                    <svg class="w-3.5 h-3.5 text-[#C6A044] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>{{ $sched->location }}</span>
                                </div>
                            </td>
                            <td class="p-4">
                                <div class="font-medium text-gray-900">{{ $sched->officer->name ?? 'Petugas Latihan' }}</div>
                                <div class="text-[10px] text-gray-400">{{ $sched->officer->email ?? '' }}</div>
                            </td>
                            <td class="p-4 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <a href="{{ route('permission-requests.create') }}" class="px-3 py-1.5 rounded-xl text-xs font-bold text-[#101D3B] bg-gray-100 hover:bg-[#C6A044] hover:text-[#101D3B] transition shadow-xs whitespace-nowrap">
                                        Minta Izin
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-gray-400">
                                Belum ada jadwal latihan yang terdaftar.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($schedules->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $schedules->links() }}
            </div>
            @endif
        </div>
    </div>

    {{-- TAB 2: INTERACTIVE FULLCALENDAR VIEW --}}
    <div x-show="activeTab === 'calendar'" style="display: none;" class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-[#101D3B]">Kalender Sesi Latihan Symphony</h3>
                <p class="text-xs text-gray-400 mt-0.5">Klik pada jadwal untuk melihat detail waktu & lokasi latihan</p>
            </div>
            <div class="flex items-center gap-3 text-xs">
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-[#101D3B]"></span> Rutin</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-[#C6A044]"></span> Tambahan</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-red-600"></span> Kompetisi</span>
            </div>
        </div>

        <div id="fullcalendar-container" class="min-h-[550px]"></div>
    </div>

    {{-- Calendar Event Detail Modal --}}
    <div x-show="showEventModal"
         x-transition
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         style="display: none;">
        <div @click.outside="showEventModal = false"
             class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl border border-gray-100 animate-scale-in">
            <div class="flex items-center justify-between mb-4">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-[#C6A044]/15 text-[#C6A044]"
                      x-text="selectedEvent ? selectedEvent.extendedProps.type : ''"></span>
                <button type="button" @click="showEventModal = false" class="text-gray-400 hover:text-gray-600 p-1 rounded-full">✕</button>
            </div>

            <h3 class="text-lg font-black text-[#101D3B]" x-text="selectedEvent ? selectedEvent.title : ''"></h3>

            <div class="my-4 space-y-2 text-xs text-gray-600 bg-gray-50 p-4 rounded-2xl">
                <p class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#C6A044]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span x-text="selectedEvent ? (new Date(selectedEvent.start)).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) : ''"></span>
                </p>
                <p class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-[#C6A044]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                    <span x-text="selectedEvent ? selectedEvent.extendedProps.location : ''"></span>
                </p>
                <template x-if="selectedEvent && selectedEvent.extendedProps.dress_code">
                    <p class="flex items-center gap-2 font-bold text-[#101D3B]">
                        <span>👕</span>
                        <span x-text="'Dresscode: ' + selectedEvent.extendedProps.dress_code"></span>
                    </p>
                </template>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" @click="showEventModal = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 transition">
                    Tutup
                </button>
                <a href="{{ route('permission-requests.create') }}" class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-[#101D3B] hover:bg-[#1a2d57] transition shadow-md">
                    Ajukan Izin &rarr;
                </a>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
<script>
let calendarInstance = null;

function initCalendar() {
    const calendarEl = document.getElementById('fullcalendar-container');
    if (!calendarEl || calendarInstance) return;

    calendarInstance = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'id',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listMonth'
        },
        buttonText: {
            today: 'Hari Ini',
            month: 'Bulan',
            week: 'Minggu',
            list: 'Daftar'
        },
        events: '{{ route('schedules.calendar') }}',
        eventClick: function(info) {
            info.jsEvent.preventDefault();
            const alpineScope = document.querySelector('[x-data]').__x;
            if (alpineScope) {
                alpineScope.$data.selectedEvent = info.event;
                alpineScope.$data.showEventModal = true;
            }
        }
    });

    calendarInstance.render();
}
</script>
@endpush
