@extends('layouts.app')

@section('title', 'Manajemen Anggota')
@section('breadcrumb', 'Data & Keanggotaan Symphony')

@section('content')
@php
    $isAdmin = auth()->check() && auth()->user()->hasAnyRole(['super_admin', 'administrator']);
@endphp

<div x-data="{
    showImportModal: false,
    showDeleteModal: false,
    showPreviewModal: false,
    showReferenceGuide: false,
    importFileName: '',
    importFileSize: '',
    isUploading: false,
    deleteUrl: '',
    memberName: '',
    clientSearch: '',
    clientDivision: '',
    clientStatus: '',
    previewMember: {},

    openDelete(url, name) {
        this.deleteUrl = url;
        this.memberName = name;
        this.showDeleteModal = true;
    },

    openPreview(data) {
        this.previewMember = data;
        this.showPreviewModal = true;
    },

    matchesFilter(name, code, div, st) {
        const q = this.clientSearch.toLowerCase();
        const matchesQ = !q || name.includes(q) || code.includes(q);
        const matchesDiv = !this.clientDivision || div === this.clientDivision;
        const matchesSt = !this.clientStatus || st === this.clientStatus;
        return matchesQ && matchesDiv && matchesSt;
    }
}">

    {{-- Page Header --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-black text-[#101D3B]">
                Manajemen Anggota Symphony
            </h1>
            <p class="mt-1 text-xs sm:text-sm text-gray-500">Kelola informasi anggota, instrumen, dan penugasan divisi marching band</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            {{-- Export Buttons --}}
            @php
    $isAdmin = auth()->check() && auth()->user()->hasAnyRole(['super_admin', 'administrator']);
@endphp

<div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                    class="inline-flex items-center gap-2 rounded-xl border border-[#C6A044] px-4 py-2.5 text-xs font-bold text-[#C6A044] hover:bg-[#C6A044] hover:text-[#101D3B] transition shadow-xs">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Export Data
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                <div x-show="open" @click.outside="open = false" x-transition
                    class="absolute right-0 z-20 mt-2 w-44 rounded-2xl border border-gray-100 bg-white p-1 shadow-2xl">
                    <a href="{{ route('members.export.excel') }}?{{ http_build_query(request()->all()) }}"
                        class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-emerald-50 hover:text-emerald-800 rounded-xl transition">
                        <svg class="h-4 w-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Excel Spreadsheet
                    </a>
                    <a href="{{ route('members.export.pdf') }}?{{ http_build_query(request()->all()) }}"
                        class="flex items-center gap-2.5 px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-red-50 hover:text-red-800 rounded-xl transition">
                        <svg class="h-4 w-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Dokumen PDF
                    </a>
                </div>
            </div>

            @if(auth()->user()->hasAnyRole(['super_admin', 'administrator', 'attendance_officer']) || auth()->user()->can('manage-members'))
                <button type="button" @click="showImportModal = true"
                    class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 text-xs font-bold transition shadow-md active:scale-95">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    <span>Import Excel / CSV</span>
                </button>
                <a href="{{ route('members.create') }}"
                    class="btn-primary inline-flex items-center gap-2 rounded-xl bg-[#101D3B] dark:bg-[#C6A044] px-4 sm:px-5 py-2.5 text-xs font-bold text-white dark:text-[#101D3B] hover:bg-[#1a2d57] dark:hover:bg-[#d4b055] transition shadow-md">
                    <svg class="h-4 w-4 text-[#C6A044] dark:text-[#101D3B]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Anggota</span>
                </a>
            @endif
        </div>
    </div>

    {{-- Stats Row with Animated Numbers & Lift Effect --}}
    <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="rounded-3xl border border-gray-100 bg-white p-5 shadow-sm hover:shadow-lg hover:-translate-y-1 transition duration-300">
            <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Total Anggota</p>
            <p class="mt-2 text-3xl font-black text-[#101D3B]" data-counter="{{ $totalMembers }}">0</p>
        </div>
        <div class="rounded-3xl border border-emerald-100 bg-emerald-50/50 p-5 shadow-sm hover:shadow-lg hover:-translate-y-1 transition duration-300">
            <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-800">Aktif Latihan</p>
            <p class="mt-2 text-3xl font-black text-emerald-700" data-counter="{{ $activeMembers }}">0</p>
        </div>
        <div class="rounded-3xl border border-gray-100 bg-white p-5 shadow-sm hover:shadow-lg hover:-translate-y-1 transition duration-300">
            <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Tidak Aktif</p>
            <p class="mt-2 text-3xl font-black text-gray-500" data-counter="{{ $inactiveMembers }}">0</p>
        </div>
        <div class="rounded-3xl border border-blue-100 bg-blue-50/50 p-5 shadow-sm hover:shadow-lg hover:-translate-y-1 transition duration-300">
            <p class="text-[10px] font-bold uppercase tracking-wider text-blue-800">Alumni</p>
            <p class="mt-2 text-3xl font-black text-blue-700" data-counter="{{ $alumniMembers }}">0</p>
        </div>
    </div>

    {{-- Filter Toolbar: Server-side fallback + Client-side Realtime Filter --}}
    <div class="mb-5 rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-4">
            {{-- Instant Live Search Input --}}
            <div class="relative flex-1 min-w-[220px]">
                <svg class="absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0" />
                </svg>
                <input type="text"
                    x-model="clientSearch"
                    placeholder="Ketik nama atau kode anggota (filter instan)..."
                    class="w-full rounded-xl border border-gray-200 py-2.5 pl-10 pr-3 text-xs focus:border-[#C6A044] focus:outline-none focus:ring-1 focus:ring-[#C6A044]">
            </div>

            {{-- Division Instant Filter --}}
            <div class="min-w-[160px]">
                <select x-model="clientDivision"
                    class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-xs focus:border-[#C6A044] focus:outline-none focus:ring-1 focus:ring-[#C6A044]">
                    <option value="">Semua Divisi</option>
                    @foreach ($divisions as $division)
                        <option value="{{ $division->name }}">{{ $division->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Status Instant Filter --}}
            <div class="min-w-[140px]">
                <select x-model="clientStatus"
                    class="w-full rounded-xl border border-gray-200 px-3 py-2.5 text-xs focus:border-[#C6A044] focus:outline-none focus:ring-1 focus:ring-[#C6A044]">
                    <option value="">Semua Status</option>
                    <option value="active">Aktif</option>
                    <option value="inactive">Tidak Aktif</option>
                    <option value="alumni">Alumni</option>
                </select>
            </div>

            <button type="button"
                    @click="clientSearch = ''; clientDivision = ''; clientStatus = '';"
                    class="rounded-xl border border-gray-200 px-4 py-2.5 text-xs font-semibold text-gray-600 hover:bg-gray-50 transition">
                Reset
            </button>
        </div>
    </div>

    {{-- Members Table --}}
    <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
        @if ($members->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <svg class="mb-4 h-16 w-16 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <h3 class="text-base font-bold text-gray-700">Belum ada anggota terdaftar</h3>
                <p class="mt-1 text-xs text-gray-400">Mulai dengan menambahkan anggota pertama Anda.</p>
                @can('create', App\Models\Member::class)
                    <a href="{{ route('members.create') }}"
                        class="mt-4 inline-flex items-center gap-2 rounded-xl bg-[#101D3B] px-4 py-2 text-xs font-bold text-white hover:bg-[#202B40]">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Anggota
                    </a>
                @endcan
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-xs min-w-[800px]">
                    <thead>
                        <tr class="border-b border-gray-100 bg-gray-50 text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                            <th class="px-4 py-3.5 text-left" style="width: 45px;">No</th>
                            <th class="px-4 py-3.5 text-left">Foto</th>
                            <th class="px-4 py-3.5 text-left">Kode Anggota</th>
                            <th class="px-4 py-3.5 text-left">Nama Lengkap</th>
                            <th class="px-4 py-3.5 text-left">Kelas</th>
                            <th class="px-4 py-3.5 text-left">Divisi</th>
                            <th class="px-4 py-3.5 text-left">Instrumen</th>
                            <th class="px-4 py-3.5 text-left">Status</th>
                            <th class="px-4 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($members as $index => $member)
                            @php
                                $effectivePhoto = $member->effective_photo;
                                $photoUrl = $effectivePhoto ? asset('storage/' . $effectivePhoto) : null;
                                $memberJson = [
                                    'id' => $member->id,
                                    'name' => $member->full_name,
                                    'code' => $member->member_code,
                                    'class' => $member->class ?? '-',
                                    'division' => $member->division->name ?? 'Belum Ditentukan',
                                    'division_color' => $member->division->color ?? '#101D3B',
                                    'instrument' => $member->instrument?->name ?? 'Belum Ditentukan',
                                    'position' => $member->position?->name ?? 'Anggota',
                                    'phone' => $member->effective_phone ?? '-',
                                    'email' => $member->user?->email ?? '-',
                                    'password' => $isAdmin ? ($member->user?->plain_password ?? ($member->user ? 'password' : '-')) : null,
                                    'gender' => $member->gender === 'L' || $member->gender === 'male' ? 'Laki-laki' : 'Perempuan',
                                    'status' => $member->status,
                                    'enrollment_date' => $member->enrollment_date ? $member->enrollment_date->format('d F Y') : '-',
                                    'photo_url' => $photoUrl,
                                    'show_url' => route('members.show', $member),
                                    'edit_url' => route('members.edit', $member),
                                ];
                            @endphp
                            <tr class="hover:bg-amber-50/20 transition-colors"
                                x-show="matchesFilter('{{ strtolower($member->full_name) }}', '{{ strtolower($member->member_code) }}', '{{ $member->division->name ?? '' }}', '{{ $member->status }}')">
                                <td class="px-4 py-3 text-gray-400 font-mono">
                                    {{ $members->firstItem() + $index }}
                                </td>
                                <td class="px-4 py-3">
                                    @if ($photoUrl)
                                        <img src="{{ $photoUrl }}" alt="{{ $member->full_name }}"
                                            class="h-9 w-9 rounded-full object-cover ring-2 ring-[#C6A044]/30 cursor-pointer hover:scale-105 transition"
                                            @click='openPreview(@json($memberJson))'>
                                    @else
                                        <div @click='openPreview(@json($memberJson))'
                                            class="flex h-9 w-9 items-center justify-center rounded-full bg-[#101D3B] text-xs font-black text-white cursor-pointer hover:scale-105 transition">
                                            {{ strtoupper(substr($member->full_name, 0, 2)) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <span class="rounded-lg bg-[#101D3B]/5 px-2.5 py-1 font-mono text-[11px] font-bold text-[#101D3B]">
                                        {{ $member->member_code }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-bold text-gray-800">
                                    <button type="button"
                                            @click='openPreview(@json($memberJson))'
                                            class="hover:text-[#C6A044] text-left transition font-bold block">
                                        {{ $member->full_name }}
                                    </button>
                                    @if($isAdmin)
                                        <div class="mt-1 flex flex-wrap items-center gap-1.5 text-[10px] font-normal text-gray-500">
                                            @if($member->user?->email)
                                                <span class="font-mono text-gray-600 bg-gray-100 px-1.5 py-0.5 rounded">✉️ {{ $member->user->email }}</span>
                                            @endif
                                            @if($member->effective_phone)
                                                <span class="font-mono text-gray-600 bg-gray-100 px-1.5 py-0.5 rounded">📞 {{ $member->effective_phone }}</span>
                                            @endif
                                            <span class="font-mono font-bold text-rose-700 bg-rose-50 border border-rose-200 px-1.5 py-0.5 rounded">
                                                🔑 PW: {{ $member->user?->plain_password ?? ($member->user ? 'password' : '-') }}
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-600 font-medium">{{ $member->class ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    @if ($member->division)
                                        <span class="inline-flex items-center gap-1.5 font-semibold text-gray-700">
                                            <span class="h-2 w-2 rounded-full"
                                                style="background-color: {{ $member->division->color ?? '#101D3B' }}"></span>
                                            {{ $member->division->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-600">{{ $member->instrument?->name ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    @if ($member->status === 'active')
                                        <span class="rounded-full bg-emerald-100 px-2.5 py-0.5 text-[10px] font-bold text-emerald-800 uppercase">Aktif</span>
                                    @elseif ($member->status === 'inactive')
                                        <span class="rounded-full bg-gray-100 px-2.5 py-0.5 text-[10px] font-bold text-gray-600 uppercase">Nonaktif</span>
                                    @else
                                        <span class="rounded-full bg-blue-100 px-2.5 py-0.5 text-[10px] font-bold text-blue-800 uppercase">Alumni</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="inline-flex items-center gap-1">
                                        {{-- Quick Preview Modal Trigger --}}
                                        <button type="button"
                                            @click='openPreview(@json($memberJson))'
                                            class="rounded-xl p-1.5 text-gray-400 hover:bg-gold/10 hover:text-gold transition"
                                            title="Quick View Preview">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </button>

                                        {{-- Full Profile Page --}}
                                        <a href="{{ route('members.show', $member) }}"
                                            class="rounded-xl p-1.5 text-gray-400 hover:bg-gray-100 hover:text-[#101D3B] transition"
                                            title="Buka Halaman Detail">
                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                            </svg>
                                        </a>

                                        @can('update', $member)
                                            <a href="{{ route('members.edit', $member) }}"
                                                class="rounded-xl p-1.5 text-gray-400 hover:bg-blue-50 hover:text-blue-600 transition"
                                                title="Edit">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </a>
                                        @endcan

                                        @can('delete', $member)
                                            <button
                                                @click="openDelete('{{ route('members.destroy', $member) }}', '{{ addslashes($member->full_name) }}')"
                                                class="rounded-xl p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-600 transition"
                                                title="Hapus">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if ($members->hasPages())
                <div class="border-t border-gray-100 px-5 py-4">
                    {{ $members->links() }}
                </div>
            @endif
        @endif
    </div>

    {{-- ============================================================
         MODAL 1: QUICK PREVIEW DRAWER / MODAL (2026 UI)
         ============================================================ --}}
    <div x-show="showPreviewModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         style="display: none;"
         @keydown.window.escape="showPreviewModal = false">

        <div @click.outside="showPreviewModal = false"
             class="w-full max-w-lg rounded-3xl bg-white dark:bg-[#0D1629] shadow-2xl border border-gray-100 dark:border-white/10 overflow-hidden relative animate-scale-in">

            {{-- Modal Header Banner --}}
            <div class="h-28 bg-gradient-to-r from-[#101D3B] to-[#202B40] dark:from-[#080E1C] dark:to-[#16233B] p-6 relative flex items-start justify-between">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 text-[#C6A044] text-[10px] font-bold uppercase tracking-wider border border-white/15">
                    Symphony Marching Band
                </span>
                <button type="button" @click="showPreviewModal = false" class="p-1.5 text-white/70 hover:text-white rounded-full bg-white/10 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            {{-- Avatar & Identity Overlay --}}
            <div class="px-6 pb-6 pt-0 relative">
                <div class="-mt-12 mb-4 flex items-end justify-between">
                    <template x-if="previewMember.photo_url">
                        <img :src="previewMember.photo_url" :alt="previewMember.name" class="w-20 h-20 rounded-2xl object-cover border-4 border-white dark:border-[#0D1629] shadow-xl bg-gray-50 dark:bg-white/5">
                    </template>
                    <template x-if="!previewMember.photo_url">
                        <div class="w-20 h-20 rounded-2xl bg-[#101D3B] dark:bg-[#16233B] text-[#C6A044] border-4 border-white dark:border-[#0D1629] shadow-xl flex items-center justify-center text-2xl font-black">
                            <span x-text="previewMember.name ? previewMember.name.substring(0, 2).toUpperCase() : 'MB'"></span>
                        </div>
                    </template>

                    <div class="text-right">
                        <span class="font-mono text-xs font-bold text-[#101D3B] dark:text-[#C6A044] bg-gray-100 dark:bg-white/10 px-3 py-1 rounded-xl block" x-text="previewMember.code"></span>
                    </div>
                </div>

                <h3 class="text-xl font-black text-[#101D3B] dark:text-white" x-text="previewMember.name"></h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5" x-text="previewMember.position + ' • ' + previewMember.class"></p>

                {{-- Detail Grid --}}
                <div class="my-5 grid grid-cols-2 gap-3 p-4 rounded-2xl bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/10 text-xs">
                    <div>
                        <span class="text-gray-400 dark:text-gray-400 block text-[10px] uppercase font-bold">Divisi</span>
                        <span class="font-bold text-[#101D3B] dark:text-white mt-0.5 block" x-text="previewMember.division"></span>
                    </div>
                    <div>
                        <span class="text-gray-400 dark:text-gray-400 block text-[10px] uppercase font-bold">Instrumen</span>
                        <span class="font-bold text-[#101D3B] dark:text-white mt-0.5 block" x-text="previewMember.instrument"></span>
                    </div>
                    <div>
                        <span class="text-gray-400 dark:text-gray-400 block text-[10px] uppercase font-bold">Jenis Kelamin</span>
                        <span class="font-bold text-[#101D3B] dark:text-white mt-0.5 block" x-text="previewMember.gender"></span>
                    </div>
                    <div>
                        <span class="text-gray-400 dark:text-gray-400 block text-[10px] uppercase font-bold">No. Telepon / WA</span>
                        <span class="font-bold text-[#101D3B] dark:text-white mt-0.5 block font-mono" x-text="previewMember.phone"></span>
                    </div>
                    @if($isAdmin)
                    <div class="col-span-2 pt-2.5 mt-1 border-t border-gray-200 dark:border-white/10 bg-amber-50/70 dark:bg-amber-950/20 p-3 rounded-xl border border-amber-200/60">
                        <span class="text-[10px] font-bold text-amber-800 dark:text-amber-300 uppercase tracking-wider block mb-1.5">
                            🔑 Kredensial Akun (Khusus Admin)
                        </span>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <span class="text-gray-500 dark:text-gray-400 block text-[10px]">Email Akun</span>
                                <span class="font-mono font-bold text-[#101D3B] dark:text-white truncate block" x-text="previewMember.email"></span>
                            </div>
                            <div>
                                <span class="text-gray-500 dark:text-gray-400 block text-[10px]">Kata Sandi / Password</span>
                                <span class="font-mono font-bold text-rose-700 dark:text-rose-400 bg-white dark:bg-black/30 px-2 py-0.5 rounded border border-rose-200 inline-block" x-text="previewMember.password"></span>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Action Buttons in Quick View --}}
                <div class="flex items-center justify-between gap-3 pt-2">
                    <template x-if="previewMember.phone && previewMember.phone !== '-'">
                        <a :href="'https://wa.me/' + previewMember.phone.replace(/[^0-9]/g, '')" target="_blank"
                           class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 transition shadow-sm">
                            <span>Hubungi WA</span>
                        </a>
                    </template>

                    <div class="flex items-center gap-2 ml-auto">
                        <a :href="previewMember.edit_url"
                           class="px-4 py-2.5 rounded-xl text-xs font-semibold text-gray-700 dark:text-gray-200 bg-gray-100 dark:bg-white/10 hover:bg-gray-200 dark:hover:bg-white/20 transition">
                            Edit Data
                        </a>
                        <a :href="previewMember.show_url"
                           class="px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-[#101D3B] hover:bg-[#1a2d57] transition shadow-md">
                            Lihat Profil Lengkap &rarr;
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- ============================================================
         MODAL 2: DELETE CONFIRMATION MODAL
         ============================================================ --}}
    <div x-show="showDeleteModal"
         x-transition
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         style="display: none;">
        <div @click.stop class="w-full max-w-md rounded-3xl bg-white dark:bg-[#0D1629] p-6 shadow-2xl border border-gray-100 dark:border-white/10 text-center animate-scale-in">
            <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-red-100 dark:bg-red-500/20 text-red-600 dark:text-red-400">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h3 class="text-lg font-black text-gray-800 dark:text-white">Hapus Data Anggota</h3>
            <p class="mt-2 text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                Apakah Anda yakin ingin menghapus anggota <strong class="text-red-700 dark:text-red-400 font-bold" x-text="memberName"></strong>?
                Data yang dihapus tidak dapat dipulihkan kembali.
            </p>
            <div class="mt-6 flex justify-center gap-3">
                <button type="button" @click="showDeleteModal = false"
                    class="rounded-xl border border-gray-200 dark:border-white/15 px-5 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-white/5 transition">
                    Batal
                </button>
                <form :action="deleteUrl" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="rounded-xl bg-red-600 px-5 py-2.5 text-xs font-bold text-white hover:bg-red-700 transition shadow-sm">
                        Ya, Hapus Data
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ============================================================
         MODAL 3: IMPORT MODAL (STATE OF THE ART 2026 WORKFLOW)
         ============================================================ --}}
    <div x-show="showImportModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         style="display: none;"
         @keydown.window.escape="showImportModal = false">

        <div @click.stop class="w-full max-w-xl rounded-3xl bg-white dark:bg-[#0D1629] p-6 sm:p-7 shadow-2xl border border-gray-100 dark:border-white/10 animate-scale-in max-h-[92vh] overflow-y-auto">

            {{-- Modal Title & Close --}}
            <div class="flex items-start justify-between gap-4 mb-4">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-500/10 text-[#C6A044] text-[11px] font-black tracking-wide border border-[#C6A044]/20 mb-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/>
                        </svg>
                        Fitur Import Massal Anggota
                    </div>
                    <h3 class="text-xl font-black text-[#101D3B] dark:text-white">Import Data Anggota Symphony</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Tambahkan banyak data anggota sekaligus ke database menggunakan file spreadsheet Excel atau CSV tanpa perlu input manual satu per satu.
                    </p>
                </div>
                <button type="button" @click="showImportModal = false" class="p-2 text-gray-400 hover:text-gray-700 dark:hover:text-white rounded-xl hover:bg-gray-100 dark:hover:bg-white/10 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Step 1: Download Template Section --}}
            <div class="mb-5 rounded-2xl border border-emerald-500/20 bg-gradient-to-br from-emerald-50/80 via-white to-emerald-50/30 dark:from-emerald-950/20 dark:via-[#0D1629] dark:to-emerald-900/10 p-4 shadow-xs">
                <div class="flex items-center gap-2 mb-2">
                    <div class="flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-600 text-white font-black text-xs">
                        1
                    </div>
                    <h4 class="text-xs font-black uppercase tracking-wider text-emerald-900 dark:text-emerald-400">
                        Unduh Format Template Resmi
                    </h4>
                </div>
                <p class="text-xs text-gray-600 dark:text-gray-300 mb-3 leading-relaxed">
                    Unduh file template yang telah dilengkapi contoh data dan lembar referensi master data (divisi, instrumen, dan jabatan) agar penulisan kolom tidak terjadi kesalahan.
                </p>
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('members.import.template') }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-xs font-bold text-white hover:bg-emerald-700 active:scale-95 transition shadow-sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Download Template Excel (.xlsx)
                    </a>
                    <a href="{{ route('members.import.template', ['format' => 'csv']) }}"
                       class="inline-flex items-center gap-1.5 rounded-xl border border-gray-300 dark:border-white/20 bg-white dark:bg-white/5 px-3 py-2.5 text-xs font-bold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/10 transition">
                        <span>Format CSV</span>
                    </a>
                </div>
            </div>

            {{-- Step 2: Upload Spreadsheet File --}}
            <div class="mb-5">
                <div class="flex items-center gap-2 mb-2">
                    <div class="flex h-6 w-6 items-center justify-center rounded-lg bg-[#101D3B] dark:bg-[#C6A044] text-white dark:text-[#101D3B] font-black text-xs">
                        2
                    </div>
                    <h4 class="text-xs font-black uppercase tracking-wider text-[#101D3B] dark:text-white">
                        Isi & Unggah File Spreadsheet
                    </h4>
                </div>

                <form action="{{ route('members.import') }}" method="POST" enctype="multipart/form-data" @submit="isUploading = true">
                    @csrf

                    {{-- Custom Drag & Drop File Upload Area --}}
                    <div class="relative border-2 border-dashed border-gray-300 dark:border-white/20 hover:border-[#C6A044] dark:hover:border-[#C6A044] rounded-2xl p-5 text-center transition bg-gray-50/60 dark:bg-white/5 group"
                         :class="{ 'border-emerald-500 bg-emerald-50/30': importFileName }">
                        <input type="file" name="file" id="member_import_file" accept=".xlsx,.xls,.csv" required
                               class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                               @change="
                                    if ($event.target.files.length > 0) {
                                        importFileName = $event.target.files[0].name;
                                        importFileSize = ($event.target.files[0].size / 1024).toFixed(1) + ' KB';
                                    } else {
                                        importFileName = '';
                                        importFileSize = '';
                                    }
                               ">

                        <template x-if="!importFileName">
                            <div>
                                <div class="mx-auto mb-2 flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-500/10 text-[#C6A044] group-hover:scale-110 transition">
                                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                </div>
                                <p class="text-xs font-bold text-gray-700 dark:text-gray-200">
                                    Klik atau tarik file spreadsheet ke area ini
                                </p>
                                <p class="mt-1 text-[11px] text-gray-400 dark:text-gray-400">
                                    Mendukung format .xlsx, .xls, .csv (Maksimal 5 MB)
                                </p>
                            </div>
                        </template>

                        <template x-if="importFileName">
                            <div class="flex items-center justify-center gap-3">
                                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                </div>
                                <div class="text-left">
                                    <p class="text-xs font-bold text-emerald-800 dark:text-emerald-400 font-mono" x-text="importFileName"></p>
                                    <p class="text-[10px] text-gray-500 dark:text-gray-400" x-text="'Ukuran file: ' + importFileSize"></p>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Collapsible Reference Guide --}}
                    <div class="mt-3">
                        <button type="button" @click="showReferenceGuide = !showReferenceGuide"
                                class="inline-flex items-center gap-1.5 text-[11px] font-bold text-[#C6A044] hover:underline focus:outline-none">
                            <svg class="w-3.5 h-3.5 transform transition-transform" :class="{ 'rotate-90': showReferenceGuide }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                            <span>Bantuan: Lihat Referensi Nama Divisi & Instrumen Resmi</span>
                        </button>

                        <div x-show="showReferenceGuide" x-transition class="mt-2 rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 p-3 text-[11px] space-y-2 text-gray-600 dark:text-gray-300">
                            <div>
                                <span class="font-bold text-[#101D3B] dark:text-[#C6A044] block mb-1">Divisi Terdaftar:</span>
                                <div class="flex flex-wrap gap-1">
                                    @foreach($divisions as $div)
                                        <span class="inline-block px-2 py-0.5 rounded-md bg-white dark:bg-white/10 border border-gray-200 dark:border-white/10 text-[10px] font-medium text-gray-700 dark:text-gray-200">
                                            {{ $div->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            <div>
                                <span class="font-bold text-[#101D3B] dark:text-[#C6A044] block mb-1">Instrumen Populer:</span>
                                <p class="text-[10px] text-gray-500 dark:text-gray-400 leading-relaxed">
                                    Trumpet, Trombone, Tuba, French Horn, Baritone, Snare Drum, Bass Drum, Tenor Drum, Cymbals, Marimba, Clarinet, Flute, Saxophone Alto, Bendera, Senapan.
                                </p>
                            </div>
                            <div class="pt-1 text-[10px] text-gray-500 dark:text-gray-400">
                                <strong>Tips:</strong> Kolom <code class="font-mono text-emerald-700 dark:text-emerald-400">kode_anggota</code> boleh dikosongkan — sistem otomatis men-generate kode resmi format <code class="font-mono">SMB-2026-XXX</code>. Jika kolom <code class="font-mono text-emerald-700 dark:text-emerald-400">email</code> diisi, akun portal anggota dibuatkan otomatis (password: password).
                            </div>
                        </div>
                    </div>

                    {{-- Action Footer --}}
                    <div class="mt-6 flex items-center justify-end gap-3 pt-3 border-t border-gray-100 dark:border-white/10">
                        <button type="button" @click="showImportModal = false" :disabled="isUploading"
                                class="rounded-xl border border-gray-200 dark:border-white/15 px-4 py-2.5 text-xs font-semibold text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-white/5 transition">
                            Batal
                        </button>
                        <button type="submit" :disabled="isUploading"
                                class="inline-flex items-center gap-2 rounded-xl bg-[#101D3B] dark:bg-[#C6A044] px-5 py-2.5 text-xs font-bold text-white dark:text-[#101D3B] hover:bg-[#1a2d57] dark:hover:bg-[#d4b055] transition shadow-md disabled:opacity-50">
                            <template x-if="isUploading">
                                <svg class="animate-spin h-4 w-4 text-white dark:text-[#101D3B]" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </template>
                            <span x-text="isUploading ? 'Sedang Memproses Import...' : 'Upload & Mulai Import'"></span>
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

</div>
@endsection
