@extends('layouts.app')

@section('title', 'Edit Pengguna')
@section('breadcrumb')
    <a href="{{ route('users.index') }}" class="hover:underline">Pengguna</a> &gt; Edit {{ $user->name }}
@endsection

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:p-8">
        <div class="mb-6 pb-4 border-b border-gray-100">
            <h2 class="text-xl font-bold text-[#101D3B]">Edit Data Pengguna</h2>
            <p class="text-xs text-gray-400 mt-1">Perbarui nama, email, hak akses, atau kata sandi</p>
        </div>

        <form action="{{ route('users.update', $user) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="form-input w-full rounded-xl border-gray-200 text-sm focus:border-[#C6A044] focus:ring-[#C6A044]">
                @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Alamat Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="form-input w-full rounded-xl border-gray-200 text-sm focus:border-[#C6A044] focus:ring-[#C6A044]">
                @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Role Hak Akses <span class="text-red-500">*</span></label>
                <select name="role" required class="form-select w-full rounded-xl border-gray-200 text-sm focus:border-[#C6A044] focus:ring-[#C6A044]">
                    @foreach($roles as $r)
                    <option value="{{ $r->name }}" {{ (old('role', $user->roles->first()?->name) === $r->name) ? 'selected' : '' }}>
                        {{ ucfirst(str_replace('_', ' ', $r->name)) }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Tautkan ke Data Anggota (Opsional)</label>
                <select name="member_id" class="form-select w-full rounded-xl border-gray-200 text-sm focus:border-[#C6A044] focus:ring-[#C6A044]">
                    <option value="">-- Tidak Ditautkan / Staf Non-Member --</option>
                    @foreach($unlinkedMembers as $m)
                    <option value="{{ $m->id }}" {{ (old('member_id', $user->member?->id) == $m->id) ? 'selected' : '' }}>
                        {{ $m->full_name }} ({{ $m->member_code }})
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2 border-t border-gray-100">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Ganti Kata Sandi (Opsional)</label>
                    <input type="password" name="password" placeholder="Biarkan kosong jika tidak diubah" class="form-input w-full rounded-xl border-gray-200 text-sm focus:border-[#C6A044] focus:ring-[#C6A044]">
                    @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Ulangi Kata Sandi Baru</label>
                    <input type="password" name="password_confirmation" placeholder="Ulangi kata sandi baru" class="form-input w-full rounded-xl border-gray-200 text-sm focus:border-[#C6A044] focus:ring-[#C6A044]">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('users.index') }}" class="px-5 py-2.5 rounded-xl text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 transition">
                    Batal
                </a>
                <button type="submit" class="btn-primary px-6 py-2.5 rounded-xl text-sm font-semibold text-white bg-[#101D3B] hover:bg-[#1a2d57] transition shadow-md">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

