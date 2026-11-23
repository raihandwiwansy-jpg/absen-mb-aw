@extends('layouts.app')

@section('title', 'Akun Belum Terhubung')

@section('content')
<div class="max-w-md mx-auto text-center py-12">
    <div class="w-20 h-20 rounded-full bg-amber-50 text-[#C6A044] border-2 border-[#C6A044] flex items-center justify-center mx-auto mb-4">
        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M5.07 19H19a2 2 0 001.75-2.75L13.75 4a2 2 0 00-3.5 0L2.25 16.25A2 2 0 004 19h1.07z"/></svg>
    </div>
    <h2 class="text-xl font-bold text-[#101D3B]">Data Anggota Belum Ditautkan</h2>
    <p class="text-xs text-gray-500 mt-2 leading-relaxed">
        Akun login Anda (<strong>{{ Auth::user()->email }}</strong>) belum dihubungkan dengan data identitas personil Symphony Marching Band oleh administrator.
    </p>
    <div class="mt-6 p-4 rounded-xl bg-gray-50 text-xs text-gray-600 border border-gray-100">
        Silakan hubungi staf/administrator untuk menautkan akun Anda ke data anggota aktif agar dapat mengakses portal dan memindai QR presensi.
    </div>
</div>
@endsection

