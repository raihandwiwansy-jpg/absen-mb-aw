@extends('layouts.app')

@section('title', 'Manajemen Divisi')
@section('breadcrumb', 'Struktur & Seksi Musik')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[#101D3B]">Divisi & Seksi Musik</h2>
            <p class="text-sm text-gray-500 mt-1">Konfigurasi struktur bagian marching band dan instrumen terkait</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('instruments.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium text-[#101D3B] bg-white border border-gray-200 hover:bg-gray-50 transition shadow-sm">
                <svg class="w-4 h-4 text-[#C6A044]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3"/></svg>
                Daftar Instrumen
            </a>
            <a href="{{ route('divisions.create') }}" class="btn-primary inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-[#101D3B] hover:bg-[#1a2d57] transition shadow-md">
                <svg class="w-4 h-4 text-[#C6A044]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Divisi
            </a>
        </div>
    </div>

    {{-- Division Cards Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($divisions as $division)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition overflow-hidden flex flex-col">
            {{-- Color Accent Bar --}}
            <div class="h-2.5 w-full" style="background-color: {{ $division->color ?? '#101D3B' }}"></div>

            <div class="p-6 flex-1 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-3 mb-2">
                        <h3 class="text-lg font-bold text-[#101D3B]">{{ $division->name }}</h3>
                        <span class="w-4 h-4 rounded-full border border-gray-200 shadow-inner" style="background-color: {{ $division->color }}"></span>
                    </div>
                    <p class="text-xs text-gray-500 line-clamp-2 mb-4">{{ $division->description ?? 'Tidak ada deskripsi' }}</p>

                    <div class="grid grid-cols-2 gap-3 py-3 px-4 rounded-xl bg-gray-50 text-xs">
                        <div>
                            <span class="text-gray-400 block text-[10px] uppercase font-bold tracking-wider">Instrumen</span>
                            <span class="text-base font-extrabold text-[#101D3B]">{{ $division->instruments_count }}</span> jenis
                        </div>
                        <div>
                            <span class="text-gray-400 block text-[10px] uppercase font-bold tracking-wider">Anggota Aktif</span>
                            <span class="text-base font-extrabold text-[#C6A044]">{{ $division->members_count }}</span> orang
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-between">
                    <a href="{{ route('divisions.show', $division) }}" class="text-xs font-bold text-[#101D3B] hover:text-[#C6A044] flex items-center gap-1 transition">
                        Detail Divisi &rarr;
                    </a>
                    <div class="flex items-center gap-1">
                        <a href="{{ route('divisions.edit', $division) }}" class="p-1.5 rounded-lg text-gray-400 hover:text-[#101D3B] hover:bg-gray-100 transition" title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        </a>
                        <form action="{{ route('divisions.destroy', $division) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus divisi {{ $division->name }}?');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-red-600 hover:bg-red-50 transition" title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white p-12 rounded-2xl border border-gray-100 text-center text-gray-400">
            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            <p class="font-medium text-gray-600">Belum ada divisi yang dikonfigurasi</p>
            <a href="{{ route('divisions.create') }}" class="mt-3 inline-block text-xs font-bold text-[#C6A044] hover:underline">Tambah Divisi Pertama</a>
        </div>
        @endforelse
    </div>

</div>
@endsection

