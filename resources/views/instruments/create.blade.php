@extends('layouts.app')

@section('title', 'Tambah Instrumen')
@section('breadcrumb')
    <a href="{{ route('instruments.index') }}" class="hover:underline">Instrumen</a> &gt; Tambah Baru
@endsection

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:p-8">
        <div class="mb-6 pb-4 border-b border-gray-100">
            <h2 class="text-xl font-bold text-[#101D3B]">Tambah Instrumen Baru</h2>
            <p class="text-xs text-gray-400 mt-1">Daftarkan nama alat musik dan tetapkan ke divisi yang sesuai</p>
        </div>

        <form action="{{ route('instruments.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Divisi / Seksi <span class="text-red-500">*</span></label>
                <select name="division_id" required class="form-select w-full rounded-xl border-gray-200 text-sm focus:border-[#C6A044] focus:ring-[#C6A044]">
                    <option value="">Pilih Divisi</option>
                    @foreach($divisions as $div)
                    <option value="{{ $div->id }}" {{ (old('division_id', $selectedDivisionId ?? '') == $div->id) ? 'selected' : '' }}>
                        {{ $div->name }}
                    </option>
                    @endforeach
                </select>
                @error('division_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nama Instrumen <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Trumpet 1 / Snare Drum" class="form-input w-full rounded-xl border-gray-200 text-sm focus:border-[#C6A044] focus:ring-[#C6A044]">
                @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Deskripsi / Catatan</label>
                <textarea name="description" rows="3" placeholder="Keterangan spesifikasi alat atau persyaratan khusus..." class="form-textarea w-full rounded-xl border-gray-200 text-sm focus:border-[#C6A044] focus:ring-[#C6A044]">{{ old('description') }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('instruments.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 transition">
                    Batal
                </a>
                <button type="submit" class="btn-primary px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-[#101D3B] hover:bg-[#1a2d57] transition shadow-md">
                    Simpan Instrumen
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

