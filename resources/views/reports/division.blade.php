@extends('layouts.app')

@section('title', 'Laporan Kehadiran per Divisi')
@section('breadcrumb')
    <a href="{{ route('reports.index') }}" class="hover:underline">Laporan</a> &gt; Divisi
@endsection

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div>
        <h2 class="text-2xl font-bold text-[#101D3B]">Performa & Kehadiran per Divisi</h2>
        <p class="text-sm text-gray-500 mt-1">Perbandingan tingkat partisipasi personil di setiap bagian marching band</p>
    </div>

    {{-- Division Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($divisions as $div)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 overflow-hidden flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="w-3 h-3 rounded-full" style="background-color: {{ $div->division->color ?? '#101D3B' }}"></span>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">{{ $div->active_members }} Anggota Aktif</span>
                </div>
                <h3 class="text-xl font-bold text-[#101D3B]">{{ $div->division->name }}</h3>
                <p class="text-xs text-gray-400 mt-0.5">{{ $div->division->description ?? 'Seksi Instrumen' }}</p>

                <div class="my-5 p-4 rounded-xl bg-gray-50 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Tingkat Kehadiran</span>
                        <span class="text-2xl font-black text-[#101D3B] mt-1 block">{{ $div->percentage }}%</span>
                    </div>
                    <div class="w-16 h-16 rounded-full border-4 border-gray-200 flex items-center justify-center font-bold text-xs" style="border-color: {{ $div->division->color ?? '#C6A044' }}">
                        {{ $div->percentage }}%
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-2 text-center text-xs">
                    <div class="p-2 rounded-lg bg-emerald-50 text-emerald-800">
                        <span class="font-bold text-sm block">{{ $div->present }}</span>
                        <span class="text-[10px]">Hadir</span>
                    </div>
                    <div class="p-2 rounded-lg bg-amber-50 text-amber-800">
                        <span class="font-bold text-sm block">{{ $div->late }}</span>
                        <span class="text-[10px]">Telat</span>
                    </div>
                    <div class="p-2 rounded-lg bg-red-50 text-red-800">
                        <span class="font-bold text-sm block">{{ $div->absent }}</span>
                        <span class="text-[10px]">Alpa</span>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-between text-xs">
                <span class="text-gray-400">{{ $div->total_attendances }} total rekaman</span>
                <a href="{{ route('divisions.show', $div->division) }}" class="font-bold text-[#C6A044] hover:underline">
                    Rincian Seksi &rarr;
                </a>
            </div>
        </div>
        @endforeach
    </div>

</div>
@endsection

