@extends('layouts.app')

@section('title', 'Pengaturan Aplikasi')
@section('breadcrumb', 'Konfigurasi Sistem')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    {{-- Brand & Identity Header Card --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:p-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-5">
            <div class="w-20 h-20 rounded-full border-3 border-[#C6A044] p-1 shadow-md bg-[#101D3B] flex-shrink-0">
                <img src="{{ asset('logo.jpg') }}" alt="Symphony Logo" class="w-full h-full rounded-full object-cover">
            </div>
            <div>
                <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-[#C6A044]/15 text-[#92670a] uppercase tracking-wider mb-1">
                    Identitas Resmi Organisasi
                </span>
                <h2 class="text-xl font-black text-[#101D3B]">SYMPHONY MARCHING BAND</h2>
                <p class="text-xs text-gray-500 font-semibold mt-0.5">SMK AL WASLIYAH PERDAGANGAN • 2026</p>
                <p class="text-[11px] text-gray-400 mt-1">Aplikasi Sistem Manajemen Presensi & Keanggotaan Terpadu</p>
            </div>
        </div>

        <div class="flex flex-col gap-2 text-xs text-gray-500 bg-gray-50 p-4 rounded-xl border border-gray-100">
            <div><strong>Host Server:</strong> {{ request()->getSchemeAndHttpHost() }}</div>
            <div><strong>Versi Laravel:</strong> {{ app()->version() }}</div>
            <div><strong>Versi PHP:</strong> {{ PHP_VERSION }}</div>
            <div><strong>Basis Data:</strong> MySQL (absensi)</div>
        </div>
    </div>

    {{-- Organizational Period Management --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:p-8 space-y-6">
        <div class="flex items-center justify-between pb-4 border-b border-gray-100">
            <div>
                <h3 class="text-base font-bold text-[#101D3B]">Periode Kepengurusan & Tahun Ajaran</h3>
                <p class="text-xs text-gray-400 mt-0.5">Atur periode kepengurusan aktif untuk pengelompokan jadwal dan presensi</p>
            </div>
        </div>

        {{-- Add New Period Form --}}
        <form action="{{ route('settings.periods.store') }}" method="POST" class="p-4 rounded-2xl bg-gray-50 border border-gray-100 space-y-4">
            @csrf
            <h4 class="text-xs font-bold text-[#101D3B] uppercase tracking-wider">Tambah Periode Baru</h4>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Nama Periode <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="Contoh: Periode 2026/2027" class="form-input w-full rounded-xl border-gray-200 text-xs py-2 px-3 focus:border-[#C6A044] focus:ring-[#C6A044]">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Tanggal Mulai <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" required class="form-input w-full rounded-xl border-gray-200 text-xs py-2 px-3 focus:border-[#C6A044] focus:ring-[#C6A044]">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">Tanggal Selesai <span class="text-red-500">*</span></label>
                    <input type="date" name="end_date" required class="form-input w-full rounded-xl border-gray-200 text-xs py-2 px-3 focus:border-[#C6A044] focus:ring-[#C6A044]">
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pt-2">
                <label class="flex items-center gap-2 text-xs text-gray-600 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" class="rounded accent-[#C6A044]">
                    <span>Tetapkan langsung sebagai periode aktif</span>
                </label>
                <button type="submit" class="btn-primary py-2 px-4 rounded-xl text-xs font-bold text-white bg-[#101D3B] hover:bg-[#1a2d57] transition shadow-sm justify-center">
                    Simpan Periode
                </button>
            </div>
        </form>

        {{-- Existing Periods Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                        <th class="p-3">Nama Periode</th>
                        <th class="p-3">Tanggal Mulai</th>
                        <th class="p-3">Tanggal Selesai</th>
                        <th class="p-3">Status</th>
                        <th class="p-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($periods as $prd)
                    <tr class="hover:bg-gray-50/50">
                        <td class="p-3 font-bold text-[#101D3B]">{{ $prd->name }}</td>
                        <td class="p-3 font-mono text-gray-500">{{ $prd->start_date ? $prd->start_date->format('d/m/Y') : '-' }}</td>
                        <td class="p-3 font-mono text-gray-500">{{ $prd->end_date ? $prd->end_date->format('d/m/Y') : '-' }}</td>
                        <td class="p-3">
                            @if($prd->is_active)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    AKTIF SEKARANG
                                </span>
                            @else
                                <span class="text-gray-400 text-[11px]">Non-aktif</span>
                            @endif
                        </td>
                        <td class="p-3 text-right">
                            @if(! $prd->is_active)
                            <form action="{{ route('settings.periods.set-active', $prd) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-xs font-bold text-[#C6A044] hover:underline">
                                    Aktifkan
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-gray-400">Belum ada periode yang terdata.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

