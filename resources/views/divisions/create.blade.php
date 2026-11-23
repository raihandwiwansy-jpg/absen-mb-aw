@extends('layouts.app')

@section('title', 'Tambah Divisi')
@section('breadcrumb')
    <a href="{{ route('divisions.index') }}" class="hover:underline">Divisi</a> &gt; Tambah Baru
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:p-8">
        <div class="mb-6 pb-4 border-b border-gray-100">
            <h2 class="text-xl font-bold text-[#101D3B]">Tambah Divisi Baru</h2>
            <p class="text-xs text-gray-400 mt-1">Daftarkan seksi marching band baru (misal: Brass, Percussion, dsb)</p>
        </div>

        <form action="{{ route('divisions.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nama Divisi <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Brass Section" class="form-input w-full rounded-xl border-gray-200 text-sm focus:border-[#C6A044] focus:ring-[#C6A044]">
                @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Warna Identitas (Hex) <span class="text-red-500">*</span></label>
                <div class="flex items-center gap-3">
                    <input type="color" id="color_picker" value="{{ old('color', '#101D3B') }}" onchange="document.getElementById('color_input').value = this.value" class="h-10 w-14 rounded-lg cursor-pointer border border-gray-200 p-1">
                    <input type="text" id="color_input" name="color" value="{{ old('color', '#101D3B') }}" required placeholder="#101D3B" class="form-input flex-1 rounded-xl border-gray-200 text-sm font-mono focus:border-[#C6A044] focus:ring-[#C6A044]" oninput="document.getElementById('color_picker').value = this.value">
                </div>
                @error('color') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Deskripsi / Peran</label>
                <textarea name="description" rows="3" placeholder="Jelaskan peran seksi ini dalam komposisi musik..." class="form-textarea w-full rounded-xl border-gray-200 text-sm focus:border-[#C6A044] focus:ring-[#C6A044]">{{ old('description') }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('divisions.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 transition">
                    Batal
                </a>
                <button type="submit" class="btn-primary px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-[#101D3B] hover:bg-[#1a2d57] transition shadow-md">
                    Simpan Divisi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

