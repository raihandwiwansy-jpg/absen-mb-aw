@extends('layouts.app')

@section('title', $schedule->title)
@section('breadcrumb')
    <a href="{{ route('schedules.index') }}" class="hover:underline">Jadwal</a> &gt; {{ $schedule->title }}
@endsection

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase
                    {{ $schedule->type === 'competition' ? 'bg-red-100 text-red-800' : ($schedule->type === 'additional' ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-800') }}">
                    {{ $schedule->type_label }}
                </span>
                <span class="text-xs text-gray-400 font-medium">Periode: {{ $schedule->organizationalPeriod->name ?? 'Umum' }}</span>
            </div>
            <h2 class="text-2xl font-bold text-[#101D3B] mt-2">{{ $schedule->title }}</h2>
            <p class="text-xs text-gray-500 mt-1 flex items-center gap-2">
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                {{ $schedule->location }}
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if($schedule->activeSession)
                <a href="{{ route('attendance.sessions.show', $schedule->activeSession) }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition shadow-md">
                    <span class="w-2 h-2 rounded-full bg-white animate-ping"></span>
                    Buka Lembar Presensi Aktif
                </a>
            @else
                <form action="{{ route('attendance.sessions.store') }}" method="POST" class="inline">
                    @csrf
                    <input type="hidden" name="practice_schedule_id" value="{{ $schedule->id }}">
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-[#101D3B] bg-[#C6A044] hover:bg-[#b59239] transition shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                        Buka Sesi Presensi & QR
                    </button>
                </form>
            @endif

            <a href="{{ route('schedules.edit', $schedule) }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 transition">
                Edit Jadwal
            </a>
            <a href="{{ route('schedules.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-medium text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 transition">
                Kembali
            </a>
        </div>
    </div>

    {{-- Details & Info Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Waktu & Tanggal Card --}}
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-2">Waktu Pelaksanaan</span>
            <div class="text-lg font-extrabold text-[#101D3B]">{{ $schedule->date->format('l, d F Y') }}</div>
            <div class="text-sm font-mono text-[#C6A044] font-bold mt-1">
                {{ substr($schedule->start_time, 0, 5) }} &mdash; {{ substr($schedule->end_time, 0, 5) }} WIB
            </div>
            <p class="text-xs text-gray-400 mt-2">
                {{ $schedule->date->isFuture() ? 'Akan berlangsung ' . $schedule->date->diffForHumans() : 'Telah selesai' }}
            </p>
        </div>

        {{-- Penanggung Jawab --}}
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-2">Penanggung Jawab</span>
            @if($schedule->officer)
                <div class="text-lg font-extrabold text-[#101D3B]">{{ $schedule->officer->name }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ $schedule->officer->email ?? '-' }}</div>
                <span class="inline-block mt-2 px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-700">
                    {{ $schedule->officer->getRoleNames()->first() ?? 'Petugas' }}
                </span>
            @else
                <div class="text-base font-bold text-gray-500">Belum Ditentukan</div>
                <div class="text-xs text-gray-400 mt-1">Tidak ada petugas khusus</div>
                <span class="inline-block mt-2 px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-400">
                    Petugas Umum
                </span>
            @endif
        </div>

        {{-- Instruksi / Catatan --}}
        <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-2">Dresscode & Instruksi</span>
            <div class="mb-2">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Seragam / Pakaian:</span>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-50 text-[#101D3B] border border-amber-200 mt-1">
                    👕 {{ $schedule->dress_code ?? 'Seragam Latihan Standar' }}
                </span>
            </div>
            <p class="text-xs text-gray-600 leading-relaxed mt-2">
                {{ $schedule->description ?? 'Tidak ada instruksi khusus yang dicantumkan untuk jadwal latihan ini.' }}
            </p>
        </div>

    </div>

    {{-- Attendance Sessions for this Schedule --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-[#101D3B]">Riwayat Sesi Absensi Jadwal Ini</h3>
                <p class="text-xs text-gray-400 mt-0.5">Sesi pencatatan QR dan manual yang pernah dibuka</p>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-gray-100 text-gray-600">
                {{ $schedule->attendanceSessions->count() }} Sesi
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                        <th class="p-4">Dibuka Pada</th>
                        <th class="p-4">Ditutup Pada</th>
                        <th class="p-4">Dibuka Oleh</th>
                        <th class="p-4">Status Sesi</th>
                        <th class="p-4">Kehadiran</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($schedule->attendanceSessions as $session)
                    <tr class="hover:bg-gray-50/50">
                        <td class="p-4 font-mono font-medium">
                            {{ $session->opened_at->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="p-4 font-mono text-gray-500">
                            {{ $session->closed_at ? $session->closed_at->format('d/m/Y H:i:s') : '-' }}
                        </td>
                        <td class="p-4 font-medium text-gray-900">
                            {{ $session->openedBy->name ?? '-' }}
                        </td>
                        <td class="p-4">
                            @if($session->is_active)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-gray-100 text-gray-600">
                                    Ditutup
                                </span>
                            @endif
                        </td>
                        <td class="p-4">
                            <span class="font-bold text-emerald-600">{{ $session->present_count }} Hadir</span> /
                            <span class="text-amber-600">{{ $session->late_count }} Telat</span> /
                            <span class="text-red-500">{{ $session->absent_count }} Alpa</span>
                        </td>
                        <td class="p-4 text-right">
                            <a href="{{ route('attendance.sessions.show', $session) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-[#101D3B] hover:bg-[#1a2d57] transition shadow-sm">
                                Lembar Absen &rarr;
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-400">Belum ada sesi absensi yang dibuka untuk jadwal ini.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

