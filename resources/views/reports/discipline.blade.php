@extends('layouts.app')

@section('title', 'Peringkat Kedisiplinan Anggota')
@section('breadcrumb')
    <a href="{{ route('reports.index') }}" class="hover:underline">Laporan</a> &gt; Kedisiplinan
@endsection

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[#101D3B]">Peringkat Kedisiplinan Anggota</h2>
            <p class="text-sm text-gray-500 mt-1">Evaluasi performa komitmen kehadiran seluruh personil marching band</p>
        </div>
        <div>
            <form action="{{ route('reports.discipline') }}" method="GET" class="flex items-center gap-3">
                <select name="division_id" onchange="this.form.submit()" class="form-select rounded-xl border-gray-200 text-xs py-2 px-3 focus:border-[#C6A044] focus:ring-[#C6A044]">
                    <option value="">Semua Divisi</option>
                    @foreach($divisions as $div)
                    <option value="{{ $div->id }}" {{ request('division_id') == $div->id ? 'selected' : '' }}>{{ $div->name }}</option>
                    @endforeach
                </select>
                @if(request('division_id'))
                <a href="{{ route('reports.discipline') }}" class="text-xs text-gray-400 hover:text-red-500 underline">Reset</a>
                @endif
            </form>
        </div>
    </div>

    {{-- Discipline Ranking Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                        <th class="p-4" style="width: 50px;">Rank</th>
                        <th class="p-4">Anggota</th>
                        <th class="p-4">Divisi & Instrumen</th>
                        <th class="p-4 text-center">Total Sesi</th>
                        <th class="p-4 text-center">Hadir</th>
                        <th class="p-4 text-center">Telat</th>
                        <th class="p-4 text-center">Izin/Sakit</th>
                        <th class="p-4 text-center">Alpa</th>
                        <th class="p-4" style="width: 180px;">Tingkat Kehadiran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($members as $index => $item)
                    <tr class="hover:bg-gray-50/50">
                        <td class="p-4">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center font-black text-xs
                                {{ $index === 0 ? 'bg-[#C6A044] text-white shadow' :
                                   ($index === 1 ? 'bg-gray-300 text-gray-800' :
                                   ($index === 2 ? 'bg-amber-700 text-white' : 'bg-gray-100 text-gray-500')) }}">
                                {{ $index + 1 }}
                            </div>
                        </td>
                        <td class="p-4">
                            <div class="font-bold text-[#101D3B]">{{ $item->member->full_name }}</div>
                            <div class="text-[10px] text-gray-400 font-mono">{{ $item->member->member_code }}</div>
                        </td>
                        <td class="p-4">
                            <div class="font-medium text-gray-900">{{ $item->member->division->name ?? '-' }}</div>
                            <div class="text-[11px] text-gray-400">{{ $item->member->instrument->name ?? '-' }}</div>
                        </td>
                        <td class="p-4 text-center font-bold text-gray-800">
                            {{ $item->total_sessions }}
                        </td>
                        <td class="p-4 text-center font-semibold text-emerald-600">
                            {{ $item->present }}
                        </td>
                        <td class="p-4 text-center font-semibold text-amber-600">
                            {{ $item->late }}
                        </td>
                        <td class="p-4 text-center font-semibold text-blue-600">
                            {{ $item->permission + $item->sick }}
                        </td>
                        <td class="p-4 text-center font-semibold text-red-600">
                            {{ $item->absent }}
                        </td>
                        <td class="p-4">
                            <div class="flex items-center gap-2">
                                <div class="flex-1 bg-gray-100 rounded-full h-2 overflow-hidden">
                                    <div class="h-2 rounded-full {{ $item->percentage >= 80 ? 'bg-emerald-500' : ($item->percentage >= 60 ? 'bg-[#C6A044]' : 'bg-red-500') }}"
                                         style="width: {{ $item->percentage }}%"></div>
                                </div>
                                <span class="font-black text-xs text-[#101D3B] w-12 text-right">
                                    {{ $item->percentage }}%
                                </span>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="p-8 text-center text-gray-400">Belum ada anggota yang terdata.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

