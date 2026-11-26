@extends('layouts.app')

@section('title', 'Profil Anggota')
@section('breadcrumb')
    <a href="{{ route('portal.dashboard') }}" class="hover:underline">Portal</a> &gt; Profil Saya
@endsection

@section('content')
<div class="max-w-3xl mx-auto space-y-5 sm:space-y-6">

    {{-- Member ID Card Mockup (Digital ID) --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-[#101D3B] via-[#202B40] to-[#101D3B] text-white p-5 sm:p-6 lg:p-8 shadow-2xl border-2 border-[#C6A044]">
        {{-- Card Watermark Logo --}}
        <div class="absolute -right-12 -bottom-12 opacity-10 pointer-events-none">
            <img src="{{ asset('logo.jpg') }}" class="w-56 sm:w-64 h-56 sm:h-64 rounded-full">
        </div>

        {{-- ID Card Header --}}
        <div class="flex flex-wrap items-center justify-between gap-2.5 pb-4 border-b border-[#C6A044]/30">
            <div class="flex items-center gap-3 min-w-0">
                <img src="{{ asset('logo.jpg') }}" alt="Logo" class="w-10 h-10 sm:w-12 sm:h-12 rounded-full border-2 border-[#C6A044] flex-shrink-0">
                <div class="min-w-0">
                    <h3 class="text-xs sm:text-sm font-black text-[#C6A044] tracking-wider sm:tracking-widest uppercase truncate">SYMPHONY MARCHING BAND</h3>
                    <p class="text-[10px] text-white/60 tracking-wider truncate">SMK AL WASLIYAH PERDAGANGAN • 2026</p>
                </div>
            </div>
            <span class="px-2.5 py-1 rounded-full bg-[#C6A044]/20 border border-[#C6A044] text-[10px] font-bold text-[#C6A044] tracking-widest uppercase flex-shrink-0">
                KARTU ANGGOTA
            </span>
        </div>

        {{-- Card Body --}}
        <div class="my-6 flex flex-col sm:flex-row items-center sm:items-start gap-6">
            @php
                $memberPhoto = $member->effective_photo;
            @endphp
            <div class="relative w-28 h-28 rounded-2xl border-2 border-[#C6A044] p-1 bg-white/10 flex-shrink-0 shadow-lg">
                @if($memberPhoto)
                    <img src="{{ asset('storage/' . $memberPhoto) }}" alt="{{ $member->full_name }}" class="w-full h-full rounded-xl object-cover">
                @else
                    <div class="w-full h-full rounded-xl bg-[#101D3B] flex items-center justify-center text-white text-3xl font-black">
                        {{ strtoupper(substr($member->full_name, 0, 2)) }}
                    </div>
                @endif
                <a href="{{ route('profile.edit') }}"
                   title="Ubah Foto Profil"
                   class="absolute -bottom-2 -right-2 w-8 h-8 rounded-full bg-[#C6A044] text-[#101D3B] border-2 border-[#101D3B] flex items-center justify-center hover:bg-amber-300 transition shadow-md">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </a>
            </div>

            <div class="flex-1 text-center sm:text-left space-y-2">
                <div>
                    <span class="text-[10px] text-white/50 uppercase tracking-widest block">Nama Lengkap</span>
                    <h4 class="text-xl font-black text-white">{{ $member->full_name }}</h4>
                </div>
                <div class="grid grid-cols-2 gap-3 pt-1 text-xs">
                    <div>
                        <span class="text-[10px] text-white/50 uppercase tracking-widest block">ID Anggota</span>
                        <span class="font-mono font-bold text-[#C6A044]">{{ $member->member_code }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-white/50 uppercase tracking-widest block">Kelas</span>
                        <span class="font-semibold">{{ $member->class ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-white/50 uppercase tracking-widest block">Divisi</span>
                        <span class="font-semibold">{{ $member->division->name ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-[10px] text-white/50 uppercase tracking-widest block">Instrumen</span>
                        <span class="font-semibold">{{ $member->instrument->name ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ID Card Footer --}}
        <div class="pt-4 border-t border-white/10 flex items-center justify-between text-[10px] text-white/50">
            <span>Bergabung: {{ $member->enrollment_date ? $member->enrollment_date->format('d/m/Y') : '-' }}</span>
            <span class="font-bold text-emerald-400">STATUS: {{ strtoupper($member->status) }}</span>
        </div>
    </div>

    {{-- Details Section --}}
    <div class="bg-white dark:bg-[#0D1629] rounded-2xl border border-gray-100 dark:border-white/10 shadow-sm p-5 sm:p-6 lg:p-8 space-y-4">
        <h3 class="text-sm sm:text-base font-bold text-[#101D3B] dark:text-white pb-3 border-b border-gray-100 dark:border-white/10">Informasi Pribadi & Kontak</h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
            <div>
                <span class="text-gray-400 block mb-0.5">Alamat Email Login</span>
                <span class="font-bold text-gray-800 dark:text-white break-all">{{ Auth::user()->email }}</span>
            </div>
            <div>
                <span class="text-gray-400 block mb-0.5">Nomor Telepon / WhatsApp</span>
                <span class="font-bold text-gray-800 dark:text-white">{{ $member->effective_phone ?? 'Belum diisi' }}</span>
            </div>
            <div>
                <span class="text-gray-400 block mb-0.5">Jenis Kelamin</span>
                <span class="font-bold text-gray-800 dark:text-white">{{ $member->gender_label }}</span>
            </div>
            <div>
                <span class="text-gray-400 block mb-0.5">Jabatan Organisasi</span>
                <span class="font-bold text-gray-800 dark:text-white">{{ $member->position->name ?? 'Anggota' }}</span>
            </div>
            <div class="sm:col-span-2">
                <span class="text-gray-400 block mb-0.5">Alamat Tempat Tinggal</span>
                <span class="font-medium text-gray-800 dark:text-gray-200">{{ $member->address ?? '-' }}</span>
            </div>
        </div>

        <div class="pt-4 border-t border-gray-100 dark:border-white/10 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <span class="text-xs text-gray-400">Kamu bisa mengubah foto, email, nomor HP, dan kata sandi dari halaman pengaturan profil.</span>
            <a href="{{ route('profile.edit') }}" class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 sm:py-2 rounded-xl text-xs font-bold text-white bg-[#101D3B] dark:bg-[#C6A044] dark:text-[#101D3B] hover:bg-[#1a2d57] dark:hover:bg-[#d4b055] transition shadow-sm flex-shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit Profil & Sandi
            </a>
        </div>
    </div>

</div>
@endsection

