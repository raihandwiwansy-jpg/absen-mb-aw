@extends('layouts.app')

@section('title', 'Buat Jadwal Latihan')
@section('breadcrumb')
    <a href="{{ route('schedules.index') }}" class="hover:underline">Jadwal</a> &gt; Tambah Baru
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:p-8">
        <div class="mb-6 pb-4 border-b border-gray-100">
            <h2 class="text-xl font-bold text-[#101D3B]">Buat Jadwal Latihan Baru</h2>
            <p class="text-xs text-gray-400 mt-1">Tentukan tanggal, jam, lokasi, dan penanggung jawab latihan</p>
        </div>

        <form action="{{ route('schedules.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Judul / Agenda Latihan <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title') }}" required placeholder="Contoh: Latihan Rutin Formasi Lapangan" class="form-input w-full rounded-xl border-gray-200 text-sm focus:border-[#C6A044] focus:ring-[#C6A044]">
                @error('title') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Tipe Latihan <span class="text-red-500">*</span></label>
                    <select name="type" required class="form-select w-full rounded-xl border-gray-200 text-sm focus:border-[#C6A044] focus:ring-[#C6A044]">
                        <option value="regular" {{ old('type') === 'regular' ? 'selected' : '' }}>Latihan Rutin</option>
                        <option value="additional" {{ old('type') === 'additional' ? 'selected' : '' }}>Latihan Tambahan</option>
                        <option value="competition" {{ old('type') === 'competition' ? 'selected' : '' }}>Persiapan Kompetisi</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Tanggal Latihan <span class="text-red-500">*</span></label>
                    <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required class="form-input w-full rounded-xl border-gray-200 text-sm focus:border-[#C6A044] focus:ring-[#C6A044]">
                    @error('date') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Jam Mulai <span class="text-red-500">*</span></label>
                    <input type="time" name="start_time" value="{{ old('start_time', '08:00') }}" required class="form-input w-full rounded-xl border-gray-200 text-sm focus:border-[#C6A044] focus:ring-[#C6A044]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Jam Selesai <span class="text-red-500">*</span></label>
                    <input type="time" name="end_time" value="{{ old('end_time', '11:00') }}" required class="form-input w-full rounded-xl border-gray-200 text-sm focus:border-[#C6A044] focus:ring-[#C6A044]">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Lokasi Latihan <span class="text-red-500">*</span></label>
                    <input type="text" name="location" value="{{ old('location', 'Lapangan SMK AL Wasliyah Perdagangan') }}" required class="form-input w-full rounded-xl border-gray-200 text-sm focus:border-[#C6A044] focus:ring-[#C6A044]">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Dresscode / Seragam Latihan</label>
                    <input type="text" name="dress_code" value="{{ old('dress_code') }}" placeholder="Contoh: Kaos Latihan Hitam / PDL MB" class="form-input w-full rounded-xl border-gray-200 text-sm focus:border-[#C6A044] focus:ring-[#C6A044]">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Petugas / Penanggung Jawab</label>
                    <select name="officer_id" class="form-select w-full rounded-xl border-gray-200 text-sm focus:border-[#C6A044] focus:ring-[#C6A044]">
                        <option value="">Pilih Petugas</option>
                        @foreach($officers as $off)
                        <option value="{{ $off->id }}" {{ old('officer_id') == $off->id ? 'selected' : '' }}>
                            {{ $off->name }} ({{ $off->getRoleNames()->first() ?? '' }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Periode Organisasi</label>
                    <select name="organizational_period_id" class="form-select w-full rounded-xl border-gray-200 text-sm focus:border-[#C6A044] focus:ring-[#C6A044]">
                        <option value="">Pilih Periode</option>
                        @foreach($periods as $prd)
                        <option value="{{ $prd->id }}" {{ old('organizational_period_id') == $prd->id ? 'selected' : '' }}>
                            {{ $prd->name }} {{ $prd->is_active ? '(Aktif)' : '' }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Keterangan / Catatan Latihan</label>
                <textarea name="description" rows="3" placeholder="Instruksi seragam, perlengkapan yang harus dibawa, dsb..." class="form-textarea w-full rounded-xl border-gray-200 text-sm focus:border-[#C6A044] focus:ring-[#C6A044]">{{ old('description') }}</textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('schedules.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 transition">
                    Batal
                </a>
                <button type="submit" class="btn-primary px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-[#101D3B] hover:bg-[#1a2d57] transition shadow-md">
                    Simpan Jadwal
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

