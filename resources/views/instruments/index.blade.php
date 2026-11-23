@extends('layouts.app')

@section('title', 'Manajemen Instrumen')
@section('breadcrumb', 'Daftar Alat Musik')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[#101D3B]">Katalog Instrumen</h2>
            <p class="text-sm text-gray-500 mt-1">Daftar instrumen musik marching band dan alokasi seksi</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('divisions.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 transition shadow-sm">
                Lihat Divisi
            </a>
            <a href="{{ route('instruments.create') }}" class="btn-primary inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-[#101D3B] hover:bg-[#1a2d57] transition shadow-md">
                <svg class="w-4 h-4 text-[#C6A044]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambah Instrumen
            </a>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-wrap items-center justify-between gap-4">
        <form action="{{ route('instruments.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <select name="division_id" onchange="this.form.submit()" class="form-select rounded-xl border-gray-200 text-xs py-2 px-3 focus:border-[#C6A044] focus:ring-[#C6A044]">
                <option value="">Semua Divisi</option>
                @foreach($divisions as $div)
                <option value="{{ $div->id }}" {{ request('division_id') == $div->id ? 'selected' : '' }}>
                    {{ $div->name }}
                </option>
                @endforeach
            </select>
            @if(request('division_id'))
            <a href="{{ route('instruments.index') }}" class="text-xs text-gray-500 hover:text-red-600 underline">Reset</a>
            @endif
        </form>

        <span class="text-xs font-semibold text-gray-500">
            Total {{ $instruments->total() }} Instrumen
        </span>
    </div>

    {{-- Instruments Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                        <th class="p-4">Nama Instrumen</th>
                        <th class="p-4">Divisi / Seksi</th>
                        <th class="p-4">Deskripsi</th>
                        <th class="p-4">Jumlah Pemain</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($instruments as $inst)
                    <tr class="hover:bg-gray-50/50">
                        <td class="p-4 font-bold text-[#101D3B]">
                            {{ $inst->name }}
                        </td>
                        <td class="p-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-800">
                                <span class="w-2 h-2 rounded-full" style="background-color: {{ $inst->division->color ?? '#666' }}"></span>
                                {{ $inst->division->name ?? '-' }}
                            </span>
                        </td>
                        <td class="p-4 text-gray-500 max-w-xs truncate">
                            {{ $inst->description ?? '-' }}
                        </td>
                        <td class="p-4">
                            <span class="font-bold text-[#101D3B]">{{ $inst->members_count }}</span> pemain aktif
                        </td>
                        <td class="p-4 text-right">
                            <div class="inline-flex items-center gap-1">
                                <a href="{{ route('instruments.edit', $inst) }}" class="p-1.5 text-gray-400 hover:text-[#101D3B] hover:bg-gray-100 rounded-lg transition" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                <form action="{{ route('instruments.destroy', $inst) }}" method="POST" onsubmit="return confirm('Hapus instrumen {{ $inst->name }}?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-400">Belum ada instrumen yang terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($instruments->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $instruments->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

