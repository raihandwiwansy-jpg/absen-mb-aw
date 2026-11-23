@extends('layouts.app')

@section('title', 'Detail Divisi ' . $division->name)
@section('breadcrumb')
    <a href="{{ route('divisions.index') }}" class="hover:underline">Divisi</a> &gt; {{ $division->name }}
@endsection

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white font-bold text-lg shadow-sm" style="background-color: {{ $division->color }}">
                {{ strtoupper(substr($division->name, 0, 1)) }}
            </div>
            <div>
                <h2 class="text-2xl font-bold text-[#101D3B]">{{ $division->name }}</h2>
                <p class="text-xs text-gray-400 mt-0.5">{{ $division->description ?? 'Tidak ada deskripsi' }}</p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('instruments.create', ['division_id' => $division->id]) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-white bg-[#C6A044] hover:bg-[#b59239] transition shadow-sm">
                + Instrumen Baru
            </a>
            <a href="{{ route('divisions.edit', $division) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-gray-700 bg-gray-100 hover:bg-gray-200 transition">
                Edit Divisi
            </a>
            <a href="{{ route('divisions.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-medium text-gray-600 bg-white border border-gray-200 hover:bg-gray-50 transition">
                Kembali
            </a>
        </div>
    </div>

    {{-- Instruments & Members in this Division --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Instruments List --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
            <h3 class="text-sm font-bold text-[#101D3B] mb-3 flex items-center justify-between">
                <span>Daftar Instrumen</span>
                <span class="text-xs px-2 py-0.5 rounded-full bg-gray-100 text-gray-600">{{ $division->instruments->count() }} jenis</span>
            </h3>
            <div class="space-y-2">
                @forelse($division->instruments as $inst)
                <div class="p-3 rounded-xl bg-gray-50 flex items-center justify-between text-xs">
                    <div>
                        <span class="font-bold text-[#101D3B] block">{{ $inst->name }}</span>
                        <span class="text-gray-400 text-[11px]">{{ $inst->members->count() }} pemain</span>
                    </div>
                    <div class="flex items-center gap-1">
                        <a href="{{ route('instruments.edit', $inst) }}" class="p-1 text-gray-400 hover:text-[#101D3B]">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                        </a>
                    </div>
                </div>
                @empty
                <p class="text-xs text-gray-400 py-3 text-center">Belum ada instrumen terdaftar</p>
                @endforelse
            </div>
        </div>

        {{-- Members in this Division --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h3 class="text-sm font-bold text-[#101D3B]">Anggota Divisi</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Daftar pemain aktif dalam seksi ini</p>
                </div>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-gray-100 text-gray-600">
                    {{ $division->members->count() }} Anggota
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                            <th class="p-3">Nama</th>
                            <th class="p-3">Instrumen</th>
                            <th class="p-3">Jabatan</th>
                            <th class="p-3">Kehadiran</th>
                            <th class="p-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($division->members as $mem)
                        <tr class="hover:bg-gray-50/50">
                            <td class="p-3">
                                <div class="font-bold text-[#101D3B]">{{ $mem->full_name }}</div>
                                <div class="text-[10px] text-gray-400 font-mono">{{ $mem->member_code }}</div>
                            </td>
                            <td class="p-3 text-gray-600">{{ $mem->instrument->name ?? '-' }}</td>
                            <td class="p-3 text-gray-600">{{ $mem->position->name ?? 'Anggota' }}</td>
                            <td class="p-3">
                                <span class="font-bold text-emerald-700">{{ $mem->attendance_percentage }}%</span>
                            </td>
                            <td class="p-3 text-right">
                                <a href="{{ route('members.show', $mem) }}" class="text-xs font-bold text-[#C6A044] hover:underline">
                                    Lihat Profil
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-400">Belum ada anggota dalam divisi ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>
@endsection

