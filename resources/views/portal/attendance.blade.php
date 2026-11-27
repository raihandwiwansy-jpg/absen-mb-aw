@extends('layouts.app')

@section('title', 'Riwayat Absensi Saya')
@section('breadcrumb')
    <a href="{{ route('portal.dashboard') }}" class="hover:underline">Portal</a> &gt; Riwayat Absensi
@endsection

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[#101D3B]">Riwayat Kehadiran Saya</h2>
            <p class="text-sm text-gray-500 mt-1">Catatan lengkap presensi latihan Anda di Symphony Marching Band</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('attendance.scan') }}" class="btn-gold inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-bold text-[#101D3B] bg-[#C6A044] hover:bg-[#b59239] transition shadow-md">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                Scan QR Presensi
            </a>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-wrap items-center justify-between gap-4">
        <form action="{{ route('portal.attendance') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <select name="status" onchange="this.form.submit()" class="form-select rounded-xl border-gray-200 text-xs py-2 px-3 focus:border-[#C6A044] focus:ring-[#C6A044]">
                <option value="">Semua Status</option>
                <option value="present" {{ request('status') === 'present' ? 'selected' : '' }}>Hadir</option>
                <option value="late" {{ request('status') === 'late' ? 'selected' : '' }}>Terlambat</option>
                <option value="permission" {{ request('status') === 'permission' ? 'selected' : '' }}>Izin</option>
                <option value="sick" {{ request('status') === 'sick' ? 'selected' : '' }}>Sakit</option>
                <option value="absent" {{ request('status') === 'absent' ? 'selected' : '' }}>Alpa</option>
            </select>
            @if(request('status'))
            <a href="{{ route('portal.attendance') }}" class="text-xs text-gray-400 hover:text-red-500 underline">Reset</a>
            @endif
        </form>

        <span class="text-xs font-semibold text-gray-500">
            Total {{ $attendances->total() }} Catatan
        </span>
    </div>

    {{-- Attendance Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                        <th class="p-4">Tanggal Latihan</th>
                        <th class="p-4">Agenda / Kegiatan</th>
                        <th class="p-4">Lokasi</th>
                        <th class="p-4">Status Kehadiran</th>
                        <th class="p-4">Waktu Check-In</th>
                        <th class="p-4">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($attendances as $att)
                    <tr class="hover:bg-gray-50/50">
                        <td class="p-4 font-mono font-medium whitespace-nowrap">
                            {{ $att->attendanceSession?->practiceSchedule?->date?->format('d/m/Y') ?? '-' }}
                        </td>
                        <td class="p-4 font-bold text-[#101D3B]">
                            {{ $att->attendanceSession?->practiceSchedule?->title ?? 'Sesi Latihan' }}
                        </td>
                        <td class="p-4 text-gray-500">
                            {{ $att->attendanceSession?->practiceSchedule?->location ?? '-' }}
                        </td>
                        <td class="p-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase
                                {{ $att->status === 'present' ? 'bg-emerald-100 text-emerald-800' :
                                   ($att->status === 'late' ? 'bg-amber-100 text-amber-800' :
                                   ($att->status === 'permission' ? 'bg-blue-100 text-blue-800' :
                                   ($att->status === 'sick' ? 'bg-purple-100 text-purple-800' : 'bg-red-100 text-red-800'))) }}">
                                {{ $att->status_label }}
                            </span>
                        </td>
                        <td class="p-4 font-mono text-gray-600">
                            {{ $att->check_in_time ? $att->check_in_time->format('H:i:s') : '-' }}
                        </td>
                        <td class="p-4 text-gray-400">
                            {{ $att->notes ?? '-' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-400">Belum ada rekaman kehadiran untuk ditampilkan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attendances->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $attendances->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

