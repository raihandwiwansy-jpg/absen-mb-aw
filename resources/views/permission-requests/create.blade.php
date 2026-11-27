@extends('layouts.app')

@section('title', 'Ajukan Izin / Sakit')
@section('breadcrumb')
    <a href="{{ route('permission-requests.index') }}" class="hover:underline">Permohonan Izin</a> &gt; Ajukan Baru
@endsection

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:p-8">
        <div class="mb-6 pb-4 border-b border-gray-100">
            <h2 class="text-xl font-bold text-[#101D3B]">Formulir Pengajuan Izin / Sakit</h2>
            <p class="text-xs text-gray-400 mt-1">Kirimkan pemberitahuan ketidakhadiran sebelum sesi latihan dimulai</p>
        </div>

        <form action="{{ route('permission-requests.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Jadwal Latihan <span class="text-red-500">*</span></label>
                <select name="practice_schedule_id" required class="form-select w-full rounded-xl border-gray-200 text-sm focus:border-[#C6A044] focus:ring-[#C6A044]">
                    <option value="">-- Pilih Jadwal Latihan Terkait --</option>
                    @foreach($schedules as $sched)
                    <option value="{{ $sched->id }}" {{ old('practice_schedule_id', request('practice_schedule_id')) == $sched->id ? 'selected' : '' }}>
                        {{ $sched->date->format('d/m/Y') }} &mdash; {{ $sched->title }} ({{ substr($sched->start_time, 0, 5) }} WIB)
                    </option>
                    @endforeach
                </select>
                @error('practice_schedule_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Jenis Pengajuan <span class="text-red-500">*</span></label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                    <label class="flex items-center gap-3 p-3.5 rounded-xl border border-gray-200 cursor-pointer hover:border-[#C6A044] transition">
                        <input type="radio" name="type" value="permission" {{ old('type', 'permission') === 'permission' ? 'checked' : '' }} class="accent-[#C6A044]">
                        <div>
                            <span class="text-xs font-bold text-[#101D3B] block">Izin</span>
                            <span class="text-[11px] text-gray-400">Keperluan keluarga/sekolah</span>
                        </div>
                    </label>
                    <label class="flex items-center gap-3 p-3.5 rounded-xl border border-gray-200 cursor-pointer hover:border-[#C6A044] transition">
                        <input type="radio" name="type" value="sick" {{ old('type') === 'sick' ? 'checked' : '' }} class="accent-[#C6A044]">
                        <div>
                            <span class="text-xs font-bold text-[#101D3B] block">Sakit</span>
                            <span class="text-[11px] text-gray-400">Kondisi kesehatan kurang baik</span>
                        </div>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Alasan Tidak Dapat Hadir <span class="text-red-500">*</span></label>
                <textarea name="reason" rows="3" required placeholder="Tuliskan alasan lengkap ketidakhadiran..." class="form-textarea w-full rounded-xl border-gray-200 text-sm focus:border-[#C6A044] focus:ring-[#C6A044]">{{ old('reason') }}</textarea>
                @error('reason') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Lampiran Pendukung (Opsional)</label>
                <input type="file" name="attachment" accept="image/*,.pdf" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#101D3B]/10 file:text-[#101D3B] hover:file:bg-[#101D3B]/20">
                <p class="text-[11px] text-gray-400 mt-1">Surat dokter, surat orang tua, atau dokumen lain (JPG, PNG, WEBP, PDF maks. 10MB)</p>
                @error('attachment') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('permission-requests.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 transition">
                    Batal
                </a>
                <button type="submit" class="btn-primary px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-[#101D3B] hover:bg-[#1a2d57] transition shadow-md">
                    Kirim Permohonan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

