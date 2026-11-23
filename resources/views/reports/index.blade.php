@extends('layouts.app')

@section('title', 'Laporan & Analitik')
@section('breadcrumb', 'Pusat Laporan Organisasi')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div>
        <h2 class="text-2xl font-bold text-[#101D3B]">Pusat Laporan & Analitik</h2>
        <p class="text-sm text-gray-500 mt-1">Akses rekapitulasi data presensi, pemeringkatan kedisiplinan, dan ekspor dokumen resmi</p>
    </div>

    {{-- Report Selection Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- 1. Rekapitulasi Presensi --}}
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
                <h3 class="text-lg font-bold text-[#101D3B]">Laporan Presensi Terpadu</h3>
                <p class="text-xs text-gray-500 mt-2 leading-relaxed">
                    Filter catatan kehadiran berdasarkan rentang tanggal, status, divisi, dan ekspor ke format PDF siap cetak.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-gray-100">
                <a href="{{ route('reports.attendance') }}" class="btn-primary w-full justify-center py-2.5 rounded-xl text-xs font-bold text-white bg-[#101D3B] hover:bg-[#1a2d57] transition shadow-sm">
                    Buka Laporan Presensi &rarr;
                </a>
            </div>
        </div>

        {{-- 2. Ranking Kedisiplinan --}}
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-[#C6A044] flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-[#101D3B]">Peringkat Kedisiplinan</h3>
                <p class="text-xs text-gray-500 mt-2 leading-relaxed">
                    Evaluasi tingkat keaktifan anggota marching band berdasarkan persentase kehadiran dan ketepatan waktu.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-gray-100">
                <a href="{{ route('reports.discipline') }}" class="btn-gold w-full justify-center py-2.5 rounded-xl text-xs font-bold text-[#101D3B] bg-[#C6A044] hover:bg-[#b59239] transition shadow-sm">
                    Lihat Ranking Anggota &rarr;
                </a>
            </div>
        </div>

        {{-- 3. Statistik per Divisi --}}
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition flex flex-col justify-between">
            <div>
                <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                </div>
                <h3 class="text-lg font-bold text-[#101D3B]">Statistik per Divisi</h3>
                <p class="text-xs text-gray-500 mt-2 leading-relaxed">
                    Perbandingan performa kehadiran antar seksi (Brass, Woodwind, Percussion, Color Guard, Pit Orchestra).
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-gray-100">
                <a href="{{ route('reports.division') }}" class="inline-flex items-center justify-center w-full py-2.5 rounded-xl text-xs font-bold text-gray-700 bg-gray-100 hover:bg-gray-200 transition">
                    Buka Laporan Divisi &rarr;
                </a>
            </div>
        </div>

    </div>

</div>
@endsection

