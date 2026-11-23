@extends('layouts.app')

@section('title', 'Sesi Absensi')
@section('breadcrumb', 'Pusat Sesi Presensi')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[#101D3B]">Sesi Presensi Latihan</h2>
            <p class="text-sm text-gray-500 mt-1">Daftar sesi absensi QR Code & manual per jadwal kegiatan</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('attendance.scan') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-[#C6A044] hover:bg-[#b59239] transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                Scan QR Presensi
            </a>
            <a href="{{ route('attendance.sessions.create') }}" class="btn-primary inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-[#101D3B] hover:bg-[#1a2d57] transition shadow-md">
                <svg class="w-4 h-4 text-[#C6A044]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Buka Sesi Baru
            </a>
        </div>
    </div>

    {{-- Sessions Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs min-w-[640px]">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                        <th class="p-4">Status</th>
                        <th class="p-4">Kegiatan Latihan</th>
                        <th class="p-4">Tanggal & Jam Buka</th>
                        <th class="p-4">Petugas Pembuka</th>
                        <th class="p-4">Rekap Presensi</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($sessions as $session)
                    <tr class="hover:bg-gray-50/50">
                        <td class="p-4">
                            @if($session->is_active)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                                    AKTIF
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold bg-gray-100 text-gray-600">
                                    DITUTUP
                                </span>
                            @endif
                        </td>
                        <td class="p-4">
                            <a href="{{ route('attendance.sessions.show', $session) }}" class="font-bold text-[#101D3B] hover:text-[#C6A044] block">
                                {{ $session->practiceSchedule->title ?? 'Sesi Latihan' }}
                            </a>
                            <div class="text-[11px] text-gray-400">{{ $session->practiceSchedule->location ?? '' }}</div>
                        </td>
                        <td class="p-4 font-mono whitespace-nowrap">
                            <div class="font-bold text-gray-800">{{ $session->opened_at->format('d/m/Y') }}</div>
                            <div class="text-[11px] text-gray-400">{{ $session->opened_at->format('H:i') }} WIB</div>
                        </td>
                        <td class="p-4">
                            <span class="font-medium text-gray-900">{{ $session->openedBy->name ?? '-' }}</span>
                        </td>
                        <td class="p-4">
                            <div class="flex items-center gap-2">
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-700">{{ $session->present_count }} Hadir</span>
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-amber-50 text-amber-700">{{ $session->late_count }} Telat</span>
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold bg-blue-50 text-blue-700">{{ $session->permission_count + $session->sick_count }} Izin</span>
                            </div>
                        </td>
                        <td class="p-4 text-right">
                            <a href="{{ route('attendance.sessions.show', $session) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold text-white bg-[#101D3B] hover:bg-[#1a2d57] transition shadow-sm">
                                <span>Lembar Absen</span>
                                <svg class="w-3.5 h-3.5 text-[#C6A044]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-400">Belum ada sesi absensi yang dibuat.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sessions->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $sessions->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

