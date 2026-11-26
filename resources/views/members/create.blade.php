@extends('layouts.app')

@section('title', 'Tambah Anggota')

@section('content')
    <div x-data="{
        photoPreview: null,
        selectedDivisionId: '{{ old('division_id') }}',
        instruments: @json($instruments->map(fn($i) => ['id' => $i->id, 'name' => $i->name, 'division_id' => $i->division_id])),

        get filteredInstruments() {
            if (!this.selectedDivisionId) return this.instruments;
            return this.instruments.filter(i => String(i.division_id) === String(this.selectedDivisionId));
        },

        previewPhoto(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => { this.photoPreview = e.target.result; };
                reader.readAsDataURL(file);
            }
        }
    }">
        {{-- Page Header --}}
        <div class="mb-6 flex items-center gap-4">
            <a href="{{ route('members.index') }}"
                class="rounded-lg p-2 text-gray-500 transition hover:bg-gray-100 hover:text-gray-700">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-[#101D3B]">Tambah Anggota</h1>
                <p class="mt-0.5 text-sm text-gray-500">Tambahkan anggota baru ke dalam sistem</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-4">
                <p class="mb-1 text-sm font-medium text-red-700">Terdapat kesalahan:</p>
                <ul class="list-inside list-disc space-y-0.5 text-sm text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('members.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">

                {{-- Left Column: Photo + Basic Info --}}
                <div class="space-y-6 lg:col-span-2">

                    {{-- Photo Upload --}}
                    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                        <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-[#101D3B]">Foto Anggota</h2>
                        <div class="flex items-start gap-6">
                            <div class="flex-shrink-0">
                                <div
                                    class="flex h-32 w-32 items-center justify-center overflow-hidden rounded-xl border-2 border-dashed border-gray-200 bg-gray-50">
                                    <template x-if="photoPreview">
                                        <img :src="photoPreview" class="h-full w-full object-cover">
                                    </template>
                                    <template x-if="!photoPreview">
                                        <div class="text-center">
                                            <svg class="mx-auto mb-1 h-8 w-8 text-gray-300" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            <p class="text-xs text-gray-400">Belum ada foto</p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                            <div class="flex-1">
                                <label
                                    class="inline-flex cursor-pointer items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Pilih Foto
                                    <input type="file" name="photo" class="hidden" accept="image/*"
                                        @change="previewPhoto($event)">
                                </label>
                                <p class="mt-2 text-xs text-gray-400">Bebas format gambar apa saja (JPG, PNG, WEBP, GIF, dll).</p>
                                @error('photo')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Personal Information --}}
                    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                        <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-[#101D3B]">Informasi Pribadi
                        </h2>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                    Nama Lengkap <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="full_name" value="{{ old('full_name') }}"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-[#101D3B] focus:outline-none focus:ring-1 focus:ring-[#101D3B] @error('full_name') border-red-300 @enderror"
                                    placeholder="Masukkan nama lengkap">
                                @error('full_name')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                    Jenis Kelamin <span class="text-red-500">*</span>
                                </label>
                                <select name="gender"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-[#101D3B] focus:outline-none focus:ring-1 focus:ring-[#101D3B] @error('gender') border-red-300 @enderror">
                                    <option value="">Pilih jenis kelamin</option>
                                    <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Laki-laki
                                    </option>
                                    <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Perempuan
                                    </option>
                                </select>
                                @error('gender')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Kelas</label>
                                <input type="text" name="class" value="{{ old('class') }}"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-[#101D3B] focus:outline-none focus:ring-1 focus:ring-[#101D3B]"
                                    placeholder="Contoh: X RPL 1">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Nomor Telepon</label>
                                <input type="text" name="phone" value="{{ old('phone') }}"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-[#101D3B] focus:outline-none focus:ring-1 focus:ring-[#101D3B]"
                                    placeholder="08xx-xxxx-xxxx">
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Tanggal Bergabung <span
                                        class="text-red-500">*</span></label>
                                <input type="date" name="enrollment_date" value="{{ old('enrollment_date', now()->format('Y-m-d')) }}"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-[#101D3B] focus:outline-none focus:ring-1 focus:ring-[#101D3B] @error('enrollment_date') border-red-300 @enderror">
                                @error('enrollment_date')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="sm:col-span-2">
                                <label class="mb-1 block text-sm font-medium text-gray-700">Alamat</label>
                                <textarea name="address" rows="3"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-[#101D3B] focus:outline-none focus:ring-1 focus:ring-[#101D3B]"
                                    placeholder="Alamat lengkap...">{{ old('address') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Organization Info --}}
                <div class="space-y-6">
                    <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                        <h2 class="mb-4 text-sm font-semibold uppercase tracking-wide text-[#101D3B]">Informasi Organisasi
                        </h2>
                        <div class="space-y-4">
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Divisi</label>
                                <select name="division_id" x-model="selectedDivisionId"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-[#101D3B] focus:outline-none focus:ring-1 focus:ring-[#101D3B]">
                                    <option value="">Pilih divisi</option>
                                    @foreach ($divisions as $division)
                                        <option value="{{ $division->id }}"
                                            {{ old('division_id') == $division->id ? 'selected' : '' }}>
                                            {{ $division->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Instrumen</label>
                                <select name="instrument_id"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-[#101D3B] focus:outline-none focus:ring-1 focus:ring-[#101D3B]">
                                    <option value="">Pilih instrumen</option>
                                    <template x-for="instrument in filteredInstruments" :key="instrument.id">
                                        <option :value="instrument.id"
                                            :selected="instrument.id == {{ old('instrument_id', 'null') }}"
                                            x-text="instrument.name"></option>
                                    </template>
                                </select>
                                <p class="mt-1 text-xs text-gray-400">Pilih divisi untuk memfilter instrumen.</p>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Jabatan</label>
                                <select name="position_id"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-[#101D3B] focus:outline-none focus:ring-1 focus:ring-[#101D3B]">
                                    <option value="">Pilih jabatan</option>
                                    @foreach ($positions as $position)
                                        <option value="{{ $position->id }}"
                                            {{ old('position_id') == $position->id ? 'selected' : '' }}>
                                            {{ $position->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">Periode Organisasi</label>
                                <select name="organizational_period_id"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-[#101D3B] focus:outline-none focus:ring-1 focus:ring-[#101D3B]">
                                    <option value="">Pilih periode</option>
                                    @foreach ($periods as $period)
                                        <option value="{{ $period->id }}"
                                            {{ old('organizational_period_id') == $period->id ? 'selected' : '' }}>
                                            {{ $period->name }}
                                            {{ $period->is_active ? '(Aktif)' : '' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="mb-1 block text-sm font-medium text-gray-700">
                                    Status <span class="text-red-500">*</span>
                                </label>
                                <select name="status"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-[#101D3B] focus:outline-none focus:ring-1 focus:ring-[#101D3B] @error('status') border-red-300 @enderror">
                                    <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>
                                        Aktif</option>
                                    <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Tidak
                                        Aktif</option>
                                    <option value="alumni" {{ old('status') === 'alumni' ? 'selected' : '' }}>Alumni
                                    </option>
                                </select>
                                @error('status')
                                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-col gap-3">
                        <button type="submit"
                            class="w-full rounded-lg bg-[#C6A044] px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-[#b08c35]">
                            Simpan Anggota
                        </button>
                        <a href="{{ route('members.index') }}"
                            class="w-full rounded-lg border border-gray-200 px-4 py-3 text-center text-sm font-medium text-gray-700 transition hover:bg-gray-50">
                            Batal
                        </a>
                    </div>
                </div>

            </div>
        </form>
    </div>
@endsection
