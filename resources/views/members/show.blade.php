@extends('layouts.app')

@section('title', 'Detail Anggota')
@section('breadcrumb')
    <a href="{{ route('members.index') }}" class="hover:underline">Anggota</a> &gt; {{ $member->full_name }}
@endsection

@section('content')
@php
    $memberEffectivePhoto = $member->effective_photo;
    $memberPhotoUrl = $memberEffectivePhoto ? asset('storage/' . $memberEffectivePhoto) : null;
@endphp
<div class="space-y-6" x-data="{ showPhotoModal: false }">

    {{-- Header Actions --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[#101D3B]">{{ $member->full_name }}</h2>
            <p class="text-sm text-gray-500 font-mono mt-0.5">{{ $member->member_code }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('members.edit', $member) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white bg-[#101D3B] hover:bg-[#1a2d57] transition shadow-sm">
                <svg class="w-4 h-4 text-[#C6A044]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit Anggota
            </a>
            <a href="{{ route('members.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium text-gray-700 bg-white border border-gray-200 hover:bg-gray-50 transition">
                Kembali
            </a>
        </div>
    </div>

    {{-- Profile Overview & Tabs --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

        {{-- Left Profile Card --}}
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm text-center flex flex-col items-center">
            <div class="relative w-32 h-32 rounded-full border-4 border-[#C6A044] p-1 bg-white shadow-md mb-4">
                @if($memberPhotoUrl)
                    <img src="{{ $memberPhotoUrl }}"
                         alt="{{ $member->full_name }}"
                         @click="showPhotoModal = true"
                         title="Klik untuk memperbesar foto"
                         class="w-full h-full rounded-full object-cover cursor-pointer hover:scale-105 transition">
                @else
                    <div class="w-full h-full rounded-full bg-[#101D3B] flex items-center justify-center text-white text-3xl font-bold">
                        {{ strtoupper(substr($member->full_name, 0, 2)) }}
                    </div>
                @endif
                <span class="absolute bottom-1 right-1 w-5 h-5 rounded-full border-2 border-white {{ $member->status === 'active' ? 'bg-emerald-500' : 'bg-gray-400' }}" title="Status: {{ ucfirst($member->status) }}"></span>
            </div>

            <h3 class="text-lg font-bold text-[#101D3B]">{{ $member->full_name }}</h3>
            <span class="inline-block px-3 py-1 mt-1 text-xs font-semibold rounded-full bg-[#C6A044]/15 text-[#92670a]">
                {{ $member->position->name ?? 'Anggota' }}
            </span>

            <div class="w-full border-t border-gray-100 my-4"></div>

            <div class="w-full space-y-3 text-left text-xs">
                <div>
                    <span class="text-gray-400 block">ID Anggota</span>
                    <span class="font-mono font-bold text-[#101D3B]">{{ $member->member_code }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block">Divisi</span>
                    <span class="font-semibold text-gray-800">{{ $member->division->name ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block">Instrumen</span>
                    <span class="font-semibold text-gray-800">{{ $member->instrument->name ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block">Kelas</span>
                    <span class="font-semibold text-gray-800">{{ $member->class ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block">Kontak</span>
                    <span class="font-semibold text-gray-800">{{ $member->effective_phone ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-gray-400 block">Tingkat Kehadiran</span>
                    <div class="flex items-center gap-2 mt-1">
                        <div class="flex-1 bg-gray-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-[#C6A044] h-2 rounded-full" style="width: {{ $member->attendance_percentage }}%"></div>
                        </div>
                        <span class="font-bold text-[#101D3B]">{{ $member->attendance_percentage }}%</span>
                    </div>
                </div>
            </div>

            @if(auth()->user() && auth()->user()->hasAnyRole(['super_admin', 'administrator']))
                <div class="w-full border-t border-gray-100 my-4"></div>
                <div class="w-full text-left text-xs bg-amber-50/70 border border-amber-200/80 rounded-xl p-3.5 space-y-2.5">
                    <div class="flex items-center gap-1.5 text-[#101D3B] font-bold uppercase text-[10px] tracking-wider">
                        <svg class="w-3.5 h-3.5 text-[#C6A044]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        <span>Akses Akun (Khusus Admin)</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block text-[10px]">Email Akun</span>
                        <span class="font-mono font-bold text-[#101D3B]">{{ $member->user->email ?? 'Belum ada akun' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block text-[10px]">Nomor HP / WhatsApp</span>
                        <span class="font-mono font-bold text-[#101D3B]">{{ $member->phone ?? '-' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 block text-[10px]">Kata Sandi / Password</span>
                        <span class="font-mono font-black text-rose-700 bg-white px-2 py-0.5 rounded border border-rose-200 inline-block">
                            {{ $member->user->plain_password ?? ($member->user ? 'password' : 'Tidak ada akun') }}
                        </span>
                    </div>
                </div>
            @endif
        </div>

        {{-- Right Tabs: Riwayat Kehadiran & Pengajuan Izin --}}
        <div class="lg:col-span-3 space-y-6">

            {{-- Attendance History Card --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h4 class="text-base font-bold text-[#101D3B]">Riwayat Kehadiran</h4>
                        <p class="text-xs text-gray-400 mt-0.5">Catatan presensi pada sesi latihan resmi</p>
                    </div>
                    <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-gray-100 text-gray-600">
                        Total {{ $attendances->total() }} Sesi
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                <th class="p-3.5">Tanggal</th>
                                <th class="p-3.5">Latihan</th>
                                <th class="p-3.5">Status</th>
                                <th class="p-3.5">Waktu Scan</th>
                                <th class="p-3.5">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs text-gray-700">
                            @forelse($attendances as $att)
                            <tr class="hover:bg-gray-50/50">
                                <td class="p-3.5 font-medium whitespace-nowrap">
                                    {{ $att->attendanceSession->practiceSchedule->date->format('d/m/Y') ?? '-' }}
                                </td>
                                <td class="p-3.5">
                                    <div class="font-semibold text-gray-900">{{ $att->attendanceSession->practiceSchedule->title ?? 'Sesi Absensi' }}</div>
                                    <div class="text-[11px] text-gray-400">{{ $att->attendanceSession->practiceSchedule->location ?? '' }}</div>
                                </td>
                                <td class="p-3.5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase
                                        {{ $att->status === 'present' ? 'bg-emerald-100 text-emerald-800' :
                                           ($att->status === 'late' ? 'bg-amber-100 text-amber-800' :
                                           ($att->status === 'permission' ? 'bg-blue-100 text-blue-800' :
                                           ($att->status === 'sick' ? 'bg-purple-100 text-purple-800' : 'bg-red-100 text-red-800'))) }}">
                                        {{ $att->status_label }}
                                    </span>
                                </td>
                                <td class="p-3.5 text-gray-500 font-mono">
                                    {{ $att->check_in_time ? $att->check_in_time->format('H:i:s') : '-' }}
                                </td>
                                <td class="p-3.5 text-gray-500">
                                    {{ $att->notes ?? '-' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="p-8 text-center text-gray-400">Belum ada riwayat absensi untuk anggota ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($attendances->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $attendances->links() }}
                </div>
                @endif
            </div>

            {{-- Permission Requests Card --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-100">
                    <h4 class="text-base font-bold text-[#101D3B]">Riwayat Izin & Sakit</h4>
                    <p class="text-xs text-gray-400 mt-0.5">Surat permohonan dispensasi kehadiran</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 border-b border-gray-100 text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                <th class="p-3.5">Tipe</th>
                                <th class="p-3.5">Jadwal Latihan</th>
                                <th class="p-3.5">Alasan</th>
                                <th class="p-3.5">Status</th>
                                <th class="p-3.5">Lampiran</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-xs text-gray-700">
                            @forelse($permissionRequests as $req)
                            <tr class="hover:bg-gray-50/50">
                                <td class="p-3.5 font-bold uppercase text-gray-900">{{ $req->type_label }}</td>
                                <td class="p-3.5">
                                    {{ $req->practiceSchedule->title ?? '-' }}
                                    <div class="text-[11px] text-gray-400">{{ $req->practiceSchedule->date->format('d/m/Y') ?? '' }}</div>
                                </td>
                                <td class="p-3.5 text-gray-600 max-w-xs truncate">{{ $req->reason }}</td>
                                <td class="p-3.5">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase
                                        {{ $req->status === 'approved' ? 'bg-emerald-100 text-emerald-800' :
                                           ($req->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') }}">
                                        {{ $req->status_label }}
                                    </span>
                                </td>
                                <td class="p-3.5">
                                    @if($req->attachment)
                                        <a href="{{ asset('storage/' . $req->attachment) }}" target="_blank" class="text-xs text-[#C6A044] hover:underline font-semibold">Lihat Berkas</a>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="p-6 text-center text-gray-400">Belum ada pengajuan izin/sakit.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

    @if($memberPhotoUrl)
    {{-- Photo Zoom Modal --}}
    <div x-show="showPhotoModal"
         x-transition
         @keydown.window.escape="showPhotoModal = false"
         @click="showPhotoModal = false"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm"
         style="display: none;">
        <div @click.stop class="relative max-w-md w-full bg-white rounded-3xl overflow-hidden shadow-2xl border-2 border-[#C6A044]">
            <div class="p-4 bg-[#101D3B] text-white flex items-center justify-between">
                <div>
                    <h4 class="font-bold text-sm">{{ $member->full_name }}</h4>
                    <p class="text-[11px] font-mono text-[#C6A044]">{{ $member->member_code }}</p>
                </div>
                <button type="button" @click="showPhotoModal = false" class="p-1.5 rounded-full bg-white/10 hover:bg-white/20 text-white transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div class="p-4 bg-gray-50 flex items-center justify-center">
                <img src="{{ $memberPhotoUrl }}" alt="{{ $member->full_name }}" class="max-h-[70vh] w-auto rounded-2xl object-contain shadow">
            </div>
        </div>
    </div>
    @endif

</div>
@endsection

