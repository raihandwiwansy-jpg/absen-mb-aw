@extends('layouts.app')

@section('title', 'Edit Data Anggota')
@section('breadcrumb')
    <a href="{{ route('members.index') }}" class="hover:underline">Anggota</a> &gt; Edit {{ $member->full_name }}
@endsection

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 lg:p-8">
        <div class="mb-6 pb-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold text-[#101D3B]">Edit Informasi Anggota</h2>
                <p class="text-xs text-gray-400 mt-1">Perbarui data profil, divisi, dan instrumen anggota</p>
            </div>
            <span class="font-mono text-xs font-semibold px-2.5 py-1 rounded bg-[#101D3B]/5 text-[#101D3B]">
                {{ $member->member_code }}
            </span>
        </div>

        <form action="{{ route('members.update', $member) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Full Name --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="full_name" value="{{ old('full_name', $member->full_name) }}" required class="form-input w-full rounded-xl border-gray-200 text-sm focus:border-[#C6A044] focus:ring-[#C6A044]">
                    @error('full_name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Gender --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Jenis Kelamin <span class="text-red-500">*</span></label>
                    <select name="gender" required class="form-select w-full rounded-xl border-gray-200 text-sm focus:border-[#C6A044] focus:ring-[#C6A044]">
                        <option value="male" {{ old('gender', $member->gender) === 'male' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="female" {{ old('gender', $member->gender) === 'female' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>

                {{-- Class / Grade --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Kelas / Tingkat</label>
                    <input type="text" name="class" value="{{ old('class', $member->class) }}" placeholder="Contoh: XII RPL 1" class="form-input w-full rounded-xl border-gray-200 text-sm focus:border-[#C6A044] focus:ring-[#C6A044]">
                </div>

                {{-- Phone --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nomor Telepon / WA</label>
                    <input type="text" name="phone" value="{{ old('phone', $member->phone) }}" placeholder="08xxxxxxxxxx" class="form-input w-full rounded-xl border-gray-200 text-sm focus:border-[#C6A044] focus:ring-[#C6A044]">
                </div>

                {{-- Enrollment Date --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Tanggal Bergabung <span class="text-red-500">*</span></label>
                    <input type="date" name="enrollment_date" value="{{ old('enrollment_date', $member->enrollment_date ? $member->enrollment_date->format('Y-m-d') : '') }}" required class="form-input w-full rounded-xl border-gray-200 text-sm focus:border-[#C6A044] focus:ring-[#C6A044]">
                </div>

                {{-- Division --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Divisi</label>
                    <select name="division_id" id="division_select" class="form-select w-full rounded-xl border-gray-200 text-sm focus:border-[#C6A044] focus:ring-[#C6A044]">
                        <option value="">Pilih Divisi</option>
                        @foreach($divisions as $div)
                        <option value="{{ $div->id }}" {{ old('division_id', $member->division_id) == $div->id ? 'selected' : '' }}>
                            {{ $div->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Instrument --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Instrumen</label>
                    <select name="instrument_id" id="instrument_select" class="form-select w-full rounded-xl border-gray-200 text-sm focus:border-[#C6A044] focus:ring-[#C6A044]">
                        <option value="">Pilih Instrumen</option>
                        @foreach($instruments as $inst)
                        <option value="{{ $inst->id }}" data-division="{{ $inst->division_id }}" {{ old('instrument_id', $member->instrument_id) == $inst->id ? 'selected' : '' }}>
                            {{ $inst->name }} ({{ $inst->division->name ?? '' }})
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Position --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Jabatan Organisasi</label>
                    <select name="position_id" class="form-select w-full rounded-xl border-gray-200 text-sm focus:border-[#C6A044] focus:ring-[#C6A044]">
                        <option value="">Pilih Jabatan</option>
                        @foreach($positions as $pos)
                        <option value="{{ $pos->id }}" {{ old('position_id', $member->position_id) == $pos->id ? 'selected' : '' }}>
                            {{ $pos->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Organizational Period --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Periode Organisasi</label>
                    <select name="organizational_period_id" class="form-select w-full rounded-xl border-gray-200 text-sm focus:border-[#C6A044] focus:ring-[#C6A044]">
                        <option value="">Pilih Periode</option>
                        @foreach($periods as $prd)
                        <option value="{{ $prd->id }}" {{ old('organizational_period_id', $member->organizational_period_id) == $prd->id ? 'selected' : '' }}>
                            {{ $prd->name }} {{ $prd->is_active ? '(Aktif)' : '' }}
                        </option>
                        @endforeach
                    </select>
                </div>

                {{-- Status --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Status Keanggotaan <span class="text-red-500">*</span></label>
                    <select name="status" required class="form-select w-full rounded-xl border-gray-200 text-sm focus:border-[#C6A044] focus:ring-[#C6A044]">
                        <option value="active" {{ old('status', $member->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ old('status', $member->status) === 'inactive' ? 'selected' : '' }}>Tidak Aktif</option>
                        <option value="alumni" {{ old('status', $member->status) === 'alumni' ? 'selected' : '' }}>Alumni</option>
                    </select>
                </div>

                {{-- Photo --}}
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Foto Profil</label>
                    <input type="file" name="photo" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-[#101D3B]/10 file:text-[#101D3B] hover:file:bg-[#101D3B]/20">
                    <p class="text-[11px] text-gray-400 mt-1">Format: JPG, PNG maks. 2MB. Biarkan kosong jika tidak diubah.</p>
                </div>

                {{-- Address --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Alamat Domisili</label>
                    <textarea name="address" rows="3" class="form-textarea w-full rounded-xl border-gray-200 text-sm focus:border-[#C6A044] focus:ring-[#C6A044]">{{ old('address', $member->address) }}</textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('members.show', $member) }}" class="px-5 py-2.5 rounded-xl text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 transition">
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

