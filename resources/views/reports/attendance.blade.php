@extends('layouts.app')

@section('title', 'Laporan Presensi Terpadu')
@section('breadcrumb')
    <a href="{{ route('reports.index') }}" class="hover:underline">Laporan</a> &gt; Presensi
@endsection

@section('content')
<div class="space-y-6">

    {{-- Header with Export Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[#101D3B]">Laporan Presensi Terpadu</h2>
            <p class="text-sm text-gray-500 mt-1">Rekap data kehadiran latihan dengan filter dinamis dan ekspor dokumen</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5 sm:gap-3">
            <a href="{{ route('reports.export.pdf') }}?{{ http_build_query(request()->all()) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-red-600 hover:bg-red-700 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Unduh PDF Resmi
            </a>
            <a href="{{ route('reports.export.excel') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-emerald-700 hover:bg-emerald-800 transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Ekspor Excel
            </a>
        </div>
    </div>

    {{-- Filter Form --}}
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
        <form action="{{ route('reports.attendance') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Divisi / Seksi</label>
                <select name="division_id" class="form-select w-full rounded-xl border-gray-200 text-xs py-2 px-3 focus:border-[#C6A044] focus:ring-[#C6A044]">
                    <option value="">Semua Divisi</option>
                    @foreach($divisions as $div)
                    <option value="{{ $div->id }}" {{ request('division_id') == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                    @endforeach
                </select>
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
                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-input w-full rounded-xl border-gray-200 text-xs py-2 px-3 focus:border-[#C6A044] focus:ring-[#C6A044]">
            </div>

            <div>
                <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-input w-full rounded-xl border-gray-200 text-xs py-2 px-3 focus:border-[#C6A044] focus:ring-[#C6A044]">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="btn-primary w-full py-2 px-4 rounded-xl text-xs font-bold text-white bg-[#101D3B] hover:bg-[#1a2d57] transition">
                    Terapkan Filter
                </button>
                @if(request()->hasAny(['division_id', 'status', 'start_date', 'end_date']))
                <a href="{{ route('reports.attendance') }}" class="p-2 text-gray-400 hover:text-red-500 transition" title="Reset">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Stats Summary --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <div class="bg-emerald-50 border border-emerald-200 p-3.5 rounded-xl text-center">
            <span class="text-[10px] font-bold text-emerald-800 uppercase block">Hadir Tepat Waktu</span>
            <span class="text-xl font-black text-emerald-700 mt-1 block">{{ $totalPresent }}</span>
        </div>
        <div class="bg-amber-50 border border-amber-200 p-3.5 rounded-xl text-center">
            <span class="text-[10px] font-bold text-amber-800 uppercase block">Terlambat</span>
            <span class="text-xl font-black text-amber-700 mt-1 block">{{ $totalLate }}</span>
        </div>
        <div class="bg-blue-50 border border-blue-200 p-3.5 rounded-xl text-center">
            <span class="text-[10px] font-bold text-blue-800 uppercase block">Izin</span>
            <span class="text-xl font-black text-blue-700 mt-1 block">{{ $totalPermission }}</span>
        </div>
        <div class="bg-purple-50 border border-purple-200 p-3.5 rounded-xl text-center">
            <span class="text-[10px] font-bold text-purple-800 uppercase block">Sakit</span>
            <span class="text-xl font-black text-purple-700 mt-1 block">{{ $totalSick }}</span>
        </div>
        <div class="bg-red-50 border border-red-200 p-3.5 rounded-xl text-center col-span-2 sm:col-span-1">
            <span class="text-[10px] font-bold text-red-800 uppercase block">Alpa</span>
            <span class="text-xl font-black text-red-700 mt-1 block">{{ $totalAbsent }}</span>
        </div>
    </div>

    {{-- Report Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                        <th class="p-4">Tanggal Latihan</th>
                        <th class="p-4">Anggota</th>
                        <th class="p-4">Divisi & Instrumen</th>
                        <th class="p-4">Kegiatan</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Waktu Check-In</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($attendances as $att)
                    <tr class="hover:bg-gray-50/50">
                        <td class="p-4 whitespace-nowrap font-mono">
                            {{ $att->attendanceSession?->practiceSchedule?->date?->format('d/m/Y') ?? '-' }}
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
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-400">Tidak ada catatan presensi pada filter yang dipilih.</td>
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

