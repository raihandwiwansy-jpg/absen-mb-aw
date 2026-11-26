@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('breadcrumb', 'Akses & Hak Pengguna')

@section('content')
<div class="space-y-6">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-[#101D3B]">Manajemen Pengguna Sistem</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola akun, hak akses role (Super Admin, Administrator, Pelatih, Petugas, Member)</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('users.create') }}" class="btn-primary inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-semibold text-white bg-[#101D3B] hover:bg-[#1a2d57] transition shadow-md">
                <svg class="w-4 h-4 text-[#C6A044]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                Tambah Pengguna
            </a>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-wrap items-center justify-between gap-4">
        <form action="{{ route('users.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / email..." class="form-input rounded-xl border-gray-200 text-xs py-2 px-3 focus:border-[#C6A044] focus:ring-[#C6A044]">

            <select name="role" onchange="this.form.submit()" class="form-select rounded-xl border-gray-200 text-xs py-2 px-3 focus:border-[#C6A044] focus:ring-[#C6A044]">
                <option value="">Semua Peran (Role)</option>
                @foreach($roles as $r)
                <option value="{{ $r->name }}" {{ request('role') === $r->name ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $r->name)) }}</option>
                @endforeach
            </select>

            @if(request()->hasAny(['search', 'role']))
            <a href="{{ route('users.index') }}" class="text-xs text-gray-400 hover:text-red-500 underline">Reset</a>
            @endif
        </form>

        <span class="text-xs font-semibold text-gray-500">
            Total {{ $users->total() }} Akun
        </span>
    </div>

    {{-- Users Table --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                        <th class="p-4">Pengguna</th>
                        <th class="p-4">Email</th>
                        <th class="p-4">Kata Sandi</th>
                        <th class="p-4">Role Akses</th>
                        <th class="p-4">Tautan Anggota</th>
                        <th class="p-4">Dibuat Pada</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-gray-700">
                    @forelse($users as $usr)
                    <tr class="hover:bg-gray-50/50">
                        <td class="p-4 font-bold text-[#101D3B] flex items-center gap-3">
                            @if($usr->photo_url)
                                <img src="{{ $usr->photo_url }}" alt="{{ $usr->name }}" class="w-8 h-8 rounded-full object-cover ring-2 ring-[#C6A044]/40 flex-shrink-0">
                            @else
                                <div class="w-8 h-8 rounded-full bg-[#101D3B] text-[#C6A044] flex items-center justify-center font-bold text-xs flex-shrink-0">
                                    {{ strtoupper(substr($usr->name, 0, 1)) }}
                                </div>
                            @endif
                            <span>{{ $usr->name }}</span>
                        </td>
                        <td class="p-4 font-mono text-gray-600">
                            {{ $usr->email }}
                        </td>
                        <td class="p-4 font-mono">
                            <span class="font-bold text-rose-700 bg-rose-50 border border-rose-200 px-2 py-0.5 rounded text-[11px]">
                                {{ $usr->plain_password ?? 'password' }}
                            </span>
                        </td>
                        <td class="p-4">
                            @foreach($usr->roles as $role)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase
                                {{ $role->name === 'super_admin' ? 'bg-purple-100 text-purple-800' :
                                   ($role->name === 'administrator' ? 'bg-blue-100 text-blue-800' :
                                   ($role->name === 'coach' ? 'bg-amber-100 text-amber-800' :
                                   ($role->name === 'attendance_officer' ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-800'))) }}">
                                {{ str_replace('_', ' ', $role->name) }}
                            </span>
                            @endforeach
                        </td>
                        <td class="p-4">
                            @if($usr->member)
                                <a href="{{ route('members.show', $usr->member) }}" class="font-bold text-[#C6A044] hover:underline">
                                    {{ $usr->member->full_name }} ({{ $usr->member->member_code }})
                                </a>
                            @else
                                <span class="text-gray-400">Tidak tertaut</span>
                            @endif
                        </td>
                        <td class="p-4 font-mono text-gray-400">
                            {{ $usr->created_at->format('d/m/Y') }}
                        </td>
                        <td class="p-4 text-right">
                            <div class="inline-flex items-center gap-1">
                                <a href="{{ route('users.edit', $usr) }}" class="p-1.5 text-gray-400 hover:text-[#101D3B] hover:bg-gray-100 rounded-lg transition" title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                                @if($usr->id !== auth()->id())
                                <form action="{{ route('users.destroy', $usr) }}" method="POST" onsubmit="return confirm('Hapus akun pengguna {{ $usr->name }}?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-400">Tidak ada data pengguna yang sesuai pencarian.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $users->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

