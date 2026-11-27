@extends('layouts.app')

@section('title', 'Riwayat Absensi')
@section('breadcrumb', 'Catatan Presensi Lengkap')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[#101D3B]">Riwayat Absensi Organisasi</h2>
            <p class="text-sm text-gray-500 mt-1">Daftar rekaman presensi seluruh anggota di setiap jadwal kegiatan</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('reports.attendance') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-[#101D3B] bg-white border border-gray-200 hover:bg-gray-50 transition shadow-sm">
                <svg class="w-4 h-4 text-[#C6A044]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Laporan & Ekspor
            </a>
            <a href="{{ route('attendance.sessions.index') }}" class="btn-primary inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-[#101D3B] hover:bg-[#1a2d57] transition shadow-md">
                Sesi Absensi Aktif
            </a>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
        <form action="{{ route('attendance.history') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Cari Anggota</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / Kode Anggota..." class="form-input w-full rounded-xl border-gray-200 text-xs py-2 px-3 focus:border-[#C6A044] focus:ring-[#C6A044]">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Status Kehadiran</label>
                <select name="status" class="form-select w-full rounded-xl border-gray-200 text-xs py-2 px-3 focus:border-[#C6A044] focus:ring-[#C6A044]">
                    <option value="">Semua Status</option>
                    <option value="present" {{ request('status') === 'present' ? 'selected' : '' }}>Hadir</option>
                    <option value="late" {{ request('status') === 'late' ? 'selected' : '' }}>Terlambat</option>
                    <option value="permission" {{ request('status') === 'permission' ? 'selected' : '' }}>Izin</option>
                    <option value="sick" {{ request('status') === 'sick' ? 'selected' : '' }}>Sakit</option>
                    <option value="absent" {{ request('status') === 'absent' ? 'selected' : '' }}>Alpa</option>
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Divisi</label>
                <select name="division_id" class="form-select w-full rounded-xl border-gray-200 text-xs py-2 px-3 focus:border-[#C6A044] focus:ring-[#C6A044]">
                    <option value="">Semua Divisi</option>
                    @foreach($divisions as $div)
                    <option value="{{ $div->id }}" {{ request('division_id') == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-input w-full rounded-xl border-gray-200 text-xs py-2 px-3 focus:border-[#C6A044] focus:ring-[#C6A044]">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="btn-primary w-full py-2 px-4 rounded-xl text-xs font-bold text-white bg-[#101D3B] hover:bg-[#1a2d57] transition">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'status', 'division_id', 'start_date']))
                <a href="{{ route('attendance.history') }}" class="p-2 text-gray-400 hover:text-red-500 transition" title="Reset">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- History Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                        <th class="p-4">Tanggal & Jam</th>
                        <th class="p-4">Anggota</th>
                        <th class="p-4">Divisi & Instrumen</th>
                        <th class="p-4">Kegiatan Latihan</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Waktu Check-In</th>
                        <th class="p-4">Dicatat Oleh</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($attendances as $att)
                    <tr class="hover:bg-gray-50/50">
                        <td class="p-4 whitespace-nowrap font-mono">
                            <div class="font-bold text-gray-900">{{ $att->attendanceSession?->practiceSchedule?->date?->format('d/m/Y') ?? '-' }}</div>
                            <div class="text-[10px] text-gray-400">{{ $att->created_at->format('H:i') }} WIB</div>
                        </td>
                        <td class="p-4">
                            <div class="font-bold text-[#101D3B]">{{ $att->member?->full_name ?? 'Anggota Dihapus' }}</div>
                            <div class="text-[10px] text-gray-400 font-mono">{{ $att->member?->member_code ?? '-' }}</div>
                        </td>
                        <td class="p-4">
                            <div class="font-medium text-gray-800">{{ $att->member?->division?->name ?? '-' }}</div>
                            <div class="text-[11px] text-gray-400">{{ $att->member?->instrument?->name ?? '-' }}</div>
                        </td>
                        <td class="p-4">
                            <div class="font-semibold text-gray-900">{{ $att->attendanceSession?->practiceSchedule?->title ?? '-' }}</div>
                            <div class="text-[11px] text-gray-400">{{ $att->attendanceSession?->practiceSchedule?->location ?? '' }}</div>
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
                        <td class="p-4 text-gray-500">
                            {{ $att->recordedBy->name ?? 'Sistem QR' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-8 text-center text-gray-400">Tidak ada data absensi yang sesuai filter.</td>
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

