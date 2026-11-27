@extends('layouts.app')

@section('title', 'Jadwal Latihan')
@section('breadcrumb', 'Agenda & Sesi Latihan')

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
    @media (max-width: 640px) {
        .fc .fc-toolbar {
            flex-wrap: wrap !important;
            gap: 0.5rem !important;
        }
        .fc .fc-toolbar-title {
            font-size: 0.95rem !important;
        }
        .fc .fc-button-primary {
            padding: 0.3rem 0.55rem !important;
            font-size: 0.7rem !important;
        }
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
}"
@calendar-event-selected.window="selectedEvent = $event.detail; showEventModal = true">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-[#101D3B]">Jadwal Latihan Marching Band</h2>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Kelola agenda latihan rutin, tambahan, dan persiapan lomba Symphony</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            {{-- Tab Switcher --}}
            <div class="p-1 rounded-xl bg-gray-100 flex items-center gap-1 text-xs">
                <button type="button"
                        @click="switchTab('list')"
                        :class="activeTab === 'list' ? 'bg-white text-[#101D3B] font-bold shadow-xs' : 'text-gray-500 hover:text-gray-900'"
                        class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                    Daftar Tabel
                </button>
                <button type="button"
                        @click="switchTab('calendar')"
                        :class="activeTab === 'calendar' ? 'bg-white text-[#101D3B] font-bold shadow-xs' : 'text-gray-500 hover:text-gray-900'"
                        class="px-3 py-1.5 rounded-lg transition flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-[#C6A044]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Kalender Interaktif
                </button>
            </div>

            <a href="{{ route('attendance.sessions.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 transition shadow-xs">
                <svg class="w-4 h-4 text-[#C6A044]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                Sesi Absensi
            </a>
            <a href="{{ route('schedules.create') }}" class="btn-primary inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-[#101D3B] hover:bg-[#1a2d57] transition shadow-md">
                <svg class="w-4 h-4 text-[#C6A044]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Jadwal
            </a>
        </div>
    </div>

    {{-- TAB 1: LIST / TABLE VIEW --}}
    <div x-show="activeTab === 'list'" class="space-y-4">
        {{-- Filter Bar --}}
        <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-wrap items-center justify-between gap-4">
            <form action="{{ route('schedules.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
                <select name="type" onchange="this.form.submit()" class="form-select rounded-xl border-gray-200 text-xs py-2 px-3 focus:border-[#C6A044] focus:ring-[#C6A044]">
                    <option value="">Semua Tipe Jadwal</option>
                    <option value="regular" {{ request('type') === 'regular' ? 'selected' : '' }}>Latihan Rutin</option>
                    <option value="additional" {{ request('type') === 'additional' ? 'selected' : '' }}>Latihan Tambahan</option>
                    <option value="competition" {{ request('type') === 'competition' ? 'selected' : '' }}>Persiapan Kompetisi</option>
                </select>
                @if(request('type'))
                <a href="{{ route('schedules.index') }}" class="text-xs text-gray-500 hover:text-red-600 underline">Reset Filter</a>
                @endif
            </form>

            <span class="text-xs font-bold text-gray-500">
                Total {{ $schedules->total() }} Jadwal Terdaftar
            </span>
        </div>

        {{-- Schedules Table --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs min-w-[680px]">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                            <th class="p-4">Tanggal & Waktu</th>
                            <th class="p-4">Agenda / Kegiatan</th>
                            <th class="p-4">Tipe</th>
                            <th class="p-4">Lokasi</th>
                            <th class="p-4">Penanggung Jawab</th>
                            <th class="p-4">Status Sesi</th>
                            <th class="p-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($schedules as $sched)
                        <tr class="hover:bg-amber-50/20 transition-colors">
                            <td class="p-4 whitespace-nowrap">
                                <div class="font-bold text-[#101D3B]">{{ $sched->date->format('d/m/Y') }}</div>
                                <div class="text-[11px] text-gray-400 font-mono">{{ substr($sched->start_time, 0, 5) }} - {{ substr($sched->end_time, 0, 5) }} WIB</div>
                            </td>
                            <td class="p-4">
                                <a href="{{ route('schedules.show', $sched) }}" class="font-bold text-[#101D3B] hover:text-[#C6A044] block">
                                    {{ $sched->title }}
                                </a>
                                <div class="text-[11px] text-gray-400 truncate max-w-xs">{{ $sched->description ?? 'Tidak ada catatan' }}</div>
                            </td>
                            <td class="p-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase
                                    {{ $sched->type === 'competition' ? 'bg-red-100 text-red-800' : ($sched->type === 'additional' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800') }}">
                                    {{ $sched->type_label }}
                                </span>
                            </td>
                            <td class="p-4 text-gray-600">
                                {{ $sched->location }}
                            </td>
                            <td class="p-4 text-gray-600">
                                {{ $sched->officer->name ?? '-' }}
                            </td>
                            <td class="p-4">
                                @if($sched->activeSession)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span>
                                        Sesi Aktif
                                    </span>
                                @elseif($sched->attendanceSessions->count() > 0)
                                    <span class="text-[11px] font-medium text-gray-500">Selesai ({{ $sched->attendanceSessions->count() }} sesi)</span>
                                @else
                                    <span class="text-[11px] text-gray-400">Belum ada sesi</span>
                                @endif
                            </td>
                            <td class="p-4 text-right">
                                <div class="inline-flex items-center gap-1">
                                    <a href="{{ route('schedules.show', $sched) }}" class="p-1.5 text-gray-400 hover:text-[#101D3B] hover:bg-gray-100 rounded-xl transition" title="Lihat">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    </a>
                                    <a href="{{ route('schedules.edit', $sched) }}" class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-xl transition" title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    <form action="{{ route('schedules.destroy', $sched) }}" method="POST" onsubmit="return confirm('Hapus jadwal latihan ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-gray-400">Belum ada jadwal latihan yang dibuat.</td>
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
    <div x-show="activeTab === 'calendar'" style="display: none;" class="bg-white rounded-3xl border border-gray-100 shadow-sm p-4 sm:p-6">
        <div class="mb-4 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-base font-bold text-[#101D3B]">Kalender Sesi Latihan Symphony</h3>
                <p class="text-xs text-gray-400 mt-0.5">Klik pada jadwal untuk melihat detail atau membuka sesi</p>
            </div>
            <div class="flex flex-wrap items-center gap-3 text-xs">
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-[#101D3B]"></span> Rutin</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-[#C6A044]"></span> Tambahan</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded-full bg-red-600"></span> Kompetisi</span>
            </div>
        </div>

        <div id="fullcalendar-container" class="min-h-[420px] sm:min-h-[550px]"></div>
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
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" @click="showEventModal = false" class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 transition">
                    Tutup
                </button>
                <a :href="selectedEvent ? selectedEvent.url : '#'" class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-[#101D3B] hover:bg-[#1a2d57] transition shadow-md">
                    Buka Detail Jadwal &rarr;
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
            window.dispatchEvent(new CustomEvent('calendar-event-selected', {
                detail: info.event
            }));
        }
    });

    calendarInstance.render();
}
</script>
@endpush
