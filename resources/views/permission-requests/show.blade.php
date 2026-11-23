@extends('layouts.app')

@section('title', 'Detail Permohonan Izin #' . $permissionRequest->id)
@section('breadcrumb')
    <a href="{{ route('permission-requests.index') }}" class="hover:underline">Permohonan Izin</a> &gt; #{{ $permissionRequest->id }}
@endsection

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:p-8">

        {{-- Header Status --}}
        <div class="flex items-center justify-between pb-5 border-b border-gray-100 mb-6">
            <div>
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Tipe Pengajuan</span>
                <span class="text-xl font-black text-[#101D3B]">{{ $permissionRequest->type_label }}</span>
            </div>
            <div>
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase
                    {{ $permissionRequest->status === 'approved' ? 'bg-emerald-100 text-emerald-800' :
                       ($permissionRequest->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                    {{ $permissionRequest->status_label }}
                </span>
            </div>
        </div>

        {{-- Details List --}}
        <div class="space-y-4 text-xs">
            <div class="flex items-start justify-between py-2 border-b border-gray-50">
                <span class="text-gray-400 w-1/3">Nama Anggota</span>
                <span class="font-bold text-[#101D3B] w-2/3 text-right">
                    {{ $permissionRequest->member->full_name }} ({{ $permissionRequest->member->member_code }})
                </span>
            </div>

            <div class="flex items-start justify-between py-2 border-b border-gray-50">
                <span class="text-gray-400 w-1/3">Divisi & Instrumen</span>
                <span class="font-semibold text-gray-800 w-2/3 text-right">
                    {{ $permissionRequest->member->division->name ?? '-' }} • {{ $permissionRequest->member->instrument->name ?? '-' }}
                </span>
            </div>

            <div class="flex items-start justify-between py-2 border-b border-gray-50">
                <span class="text-gray-400 w-1/3">Jadwal Latihan Terkait</span>
                <span class="font-semibold text-gray-800 w-2/3 text-right">
                    {{ $permissionRequest->practiceSchedule->title ?? '-' }}
                    <span class="block text-gray-400 font-mono">{{ $permissionRequest->practiceSchedule->date->format('d F Y') }}</span>
                </span>
            </div>

            <div class="flex items-start justify-between py-2 border-b border-gray-50">
                <span class="text-gray-400 w-1/3">Waktu Pengajuan</span>
                <span class="font-mono text-gray-700 w-2/3 text-right">
                    {{ $permissionRequest->created_at->format('d/m/Y H:i:s') }} WIB
                </span>
            </div>

            <div class="py-3">
                <span class="text-gray-400 block mb-1">Alasan Ketidakhadiran:</span>
                <div class="p-3.5 rounded-xl bg-gray-50 text-gray-800 text-xs leading-relaxed font-medium">
                    {{ $permissionRequest->reason }}
                </div>
            </div>

            @if($permissionRequest->attachment)
            <div class="py-2">
                <span class="text-gray-400 block mb-2">Berkas Lampiran Pendukung:</span>
                <div class="flex items-center gap-3">
                    <a href="{{ asset('storage/' . $permissionRequest->attachment) }}" target="_blank" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-[#101D3B] bg-amber-50 border border-[#C6A044]/40 hover:bg-amber-100 transition">
                        <svg class="w-4 h-4 text-[#C6A044]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                        Buka / Unduh Berkas Lampiran
                    </a>
                </div>
            </div>
            @endif

            @if($permissionRequest->reviewed_by)
            <div class="p-3.5 rounded-xl bg-gray-50 text-[11px] text-gray-500">
                Ditinjau oleh <strong>{{ $permissionRequest->reviewedBy->name ?? 'Pengurus' }}</strong> pada {{ $permissionRequest->reviewed_at ? $permissionRequest->reviewed_at->format('d/m/Y H:i') : '-' }} WIB.
            </div>
            @endif
        </div>

        {{-- Action Buttons for Admin/Officer --}}
        @hasanyrole('super_admin|administrator|attendance_officer')
        @if($permissionRequest->status === 'pending')
        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100 mt-6">
            <form action="{{ route('permission-requests.reject', $permissionRequest) }}" method="POST" onsubmit="return confirm('Tolak permohonan izin ini?');" class="inline">
                @csrf
                <button type="submit" class="px-5 py-2.5 rounded-xl text-xs font-bold text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 transition">
                    Tolak Pengajuan
                </button>
            </form>

            <form action="{{ route('permission-requests.approve', $permissionRequest) }}" method="POST" onsubmit="return confirm('Setujui permohonan izin ini? Status kehadiran anggota akan otomatis diperbarui.');" class="inline">
                @csrf
                <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition shadow-md">
                    Setujui & Perbarui Absensi
                </button>
            </form>
        </div>
        @endif
        @endhasanyrole

        <div class="mt-4 text-center">
            <a href="{{ route('permission-requests.index') }}" class="text-xs text-gray-400 hover:underline">
                &larr; Kembali ke Daftar Pengajuan
            </a>
        </div>

    </div>

</div>
@endsection

