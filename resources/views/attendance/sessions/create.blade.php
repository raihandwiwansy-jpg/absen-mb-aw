@extends('layouts.app')

@section('title', 'Buka Sesi Absensi')
@section('breadcrumb')
    <a href="{{ route('attendance.sessions.index') }}" class="hover:underline">Sesi Absensi</a> &gt; Buka Baru
@endsection

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:p-8">
        <div class="mb-6 pb-4 border-b border-gray-100">
            <h2 class="text-xl font-bold text-[#101D3B]">Buka Sesi Presensi & QR Code</h2>
            <p class="text-xs text-gray-400 mt-1">Pilih jadwal latihan yang akan dibuka presensinya hari ini</p>
        </div>

        <form action="{{ route('attendance.sessions.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Pilih Jadwal Latihan <span class="text-red-500">*</span></label>
                <select name="practice_schedule_id" required class="form-select w-full rounded-xl border-gray-200 text-sm focus:border-[#C6A044] focus:ring-[#C6A044]">
                    <option value="">-- Pilih Jadwal Latihan --</option>
                    @foreach($schedules as $sched)
                    <option value="{{ $sched->id }}">
                        {{ $sched->date->format('d/m/Y') }} &mdash; {{ $sched->title }} ({{ substr($sched->start_time, 0, 5) }} WIB)
                    </option>
                    @endforeach
                </select>
                @error('practice_schedule_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="p-4 rounded-xl bg-amber-50/60 border border-amber-200/60 text-xs text-amber-900 leading-relaxed">
                <strong>Catatan Sistem:</strong>
                <ul class="list-disc list-inside mt-1 space-y-1 text-[11px] text-amber-800">
                    <li>Sistem akan otomatis menghasilkan <strong>QR Code unik yang aman & berbatas waktu</strong>.</li>
                    <li>Seluruh anggota aktif akan otomatis di-inisialisasi ke lembar absensi.</li>
                    <li>Sesi absensi dapat ditutup kapan saja setelah kegiatan latihan selesai.</li>
                </ul>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('attendance.sessions.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 transition">
                    Batal
                </a>
                <button type="submit" class="btn-gold px-6 py-2.5 rounded-xl text-sm font-bold text-[#101D3B] bg-[#C6A044] hover:bg-[#b59239] transition shadow-md">
                    Buka Sesi Presensi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

