@extends('layouts.app')

@section('title', 'Profil Akun Saya')
@section('breadcrumb', 'Pengaturan Akun')

@section('content')
@php
    $initials = strtoupper(substr($user->name ?? 'U', 0, 2));
    $hasExistingPhoto = (bool) $user->effective_photo;
    $existingPhotoUrl = $user->photo_url;
    $roleLabel = str_replace('_', ' ', $user->getRoleNames()->first() ?? 'member');
@endphp

<div class="max-w-3xl mx-auto space-y-5 sm:space-y-6" x-data="profileForm(@js($existingPhotoUrl), @js($hasExistingPhoto))">

    @if(session('success'))
    <div class="flex items-center gap-3 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-500/30 text-emerald-800 dark:text-emerald-300 rounded-2xl px-4 sm:px-5 py-3.5 sm:py-4 text-xs sm:text-sm font-semibold shadow-sm">
        <svg class="w-5 h-5 text-emerald-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        <span>{{ session('success') }}</span>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-red-50 dark:bg-red-950/30 border border-red-200 dark:border-red-500/30 text-red-700 dark:text-red-300 rounded-2xl px-4 sm:px-5 py-3.5 sm:py-4 text-xs space-y-1 shadow-sm">
        <p class="font-bold text-xs sm:text-sm">Mohon periksa kembali data yang dimasukkan:</p>
        <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-5 sm:space-y-6" @submit="isSubmitting = true">
        @csrf
        @method('PUT')
        <input type="hidden" name="remove_photo" :value="removePhoto ? '1' : '0'">

        {{-- Foto Profil --}}
        <div class="bg-white dark:bg-[#0D1629] rounded-2xl border border-gray-100 dark:border-white/10 shadow-sm p-5 sm:p-6 lg:p-7">
            <div class="flex items-center justify-between pb-3 border-b border-gray-100 dark:border-white/10 mb-5">
                <h2 class="text-sm sm:text-base font-bold text-[#101D3B] dark:text-white">Foto Profil</h2>
                <span class="text-[11px] font-medium text-emerald-600 dark:text-emerald-400" x-show="fileInfo" x-text="fileInfo" style="display: none;"></span>
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-5 sm:gap-7">
                {{-- Preview Foto --}}
                <div class="relative flex-shrink-0">
                    <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-full border-4 border-[#C6A044] p-1 bg-gray-50 dark:bg-white/5 shadow-md overflow-hidden">
                        <template x-if="previewUrl">
                            <img :src="previewUrl"
                                 alt="Foto Profil"
                                 class="w-full h-full rounded-full object-cover"
                                 id="photo-preview-img">
                        </template>
                        <template x-if="!previewUrl">
                            <div class="w-full h-full rounded-full bg-[#101D3B] flex items-center justify-center text-[#C6A044] text-2xl sm:text-3xl font-black">
                                {{ $initials }}
                            </div>
                        </template>
                    </div>

                    <label for="photo-upload"
                           title="Pilih Foto Baru"
                           class="absolute bottom-0 right-0 w-9 h-9 rounded-full bg-[#101D3B] border-2 border-white dark:border-[#0D1629] flex items-center justify-center cursor-pointer hover:bg-[#C6A044] hover:text-[#101D3B] text-white transition shadow-lg">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </label>
                    <input type="file" id="photo-upload" name="photo" accept="image/*"
                           class="hidden" @change="onFileChange($event)">
                </div>

                <div class="flex-1 w-full text-center sm:text-left min-w-0">
                    <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                        <p class="text-base sm:text-lg font-bold text-[#101D3B] dark:text-white truncate max-w-full">{{ $user->name }}</p>
                        <span class="px-2.5 py-0.5 rounded-full bg-[#C6A044]/15 text-[#92670a] dark:text-[#C6A044] text-[10px] font-bold uppercase tracking-wider">
                            {{ ucwords($roleLabel) }}
                        </span>
                        @if($member)
                            <span class="px-2 py-0.5 rounded-md bg-gray-100 dark:bg-white/10 text-gray-600 dark:text-gray-300 font-mono text-[10px] font-bold">
                                {{ $member->member_code }}
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-gray-400 mt-1 truncate">{{ $user->email }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2.5 leading-relaxed">
                        Bebas unggah foto apa saja (JPG, PNG, WEBP, HEIC, dll) langsung dari kamera atau galeri HP — ukuran besar akan disesuaikan otomatis.
                    </p>

                    <div class="mt-4 flex flex-wrap items-center justify-center sm:justify-start gap-2.5">
                        <label for="photo-upload"
                               class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold bg-[#101D3B]/5 dark:bg-white/10 text-[#101D3B] dark:text-white hover:bg-[#101D3B]/10 dark:hover:bg-white/20 cursor-pointer transition">
                            <svg class="w-4 h-4 text-[#C6A044]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            <span x-text="isCompressing ? 'Memproses foto...' : (previewUrl ? 'Ganti Foto' : 'Upload Foto')"></span>
                        </label>

                        <button type="button"
                                x-show="previewUrl"
                                @click="clearPhoto()"
                                class="inline-flex items-center justify-center gap-1 px-3.5 py-2 rounded-xl text-xs font-semibold text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-500/10 hover:bg-red-100 dark:hover:bg-red-500/20 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Hapus Foto
                        </button>
                    </div>
                    @error('photo') <p class="text-xs text-red-500 mt-2 font-medium">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Data Akun & Kontak --}}
        <div class="bg-white dark:bg-[#0D1629] rounded-2xl border border-gray-100 dark:border-white/10 shadow-sm p-5 sm:p-6 lg:p-7 space-y-4 sm:space-y-5">
            <h2 class="text-sm sm:text-base font-bold text-[#101D3B] dark:text-white pb-3 border-b border-gray-100 dark:border-white/10">Data Akun & Kontak</h2>

            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5 sm:mb-2">Nama Akun <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                       class="w-full rounded-xl border border-gray-200 dark:border-white/15 bg-white dark:bg-white/5 px-3.5 sm:px-4 py-2.5 text-sm text-gray-800 dark:text-white focus:border-[#C6A044] focus:ring-1 focus:ring-[#C6A044] outline-none transition">
                @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5 sm:mb-2">Alamat Email <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full rounded-xl border border-gray-200 dark:border-white/15 bg-white dark:bg-white/5 px-3.5 sm:px-4 py-2.5 text-sm text-gray-800 dark:text-white focus:border-[#C6A044] focus:ring-1 focus:ring-[#C6A044] outline-none transition">
                    @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5 sm:mb-2">Nomor Telepon / WhatsApp</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->effective_phone) }}" placeholder="Contoh: 08123456789"
                           class="w-full rounded-xl border border-gray-200 dark:border-white/15 bg-white dark:bg-white/5 px-3.5 sm:px-4 py-2.5 text-sm text-gray-800 dark:text-white focus:border-[#C6A044] focus:ring-1 focus:ring-[#C6A044] outline-none transition">
                    @error('phone') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        {{-- Ganti Password --}}
        <div class="bg-white dark:bg-[#0D1629] rounded-2xl border border-gray-100 dark:border-white/10 shadow-sm p-5 sm:p-6 lg:p-7 space-y-4">
            <div class="flex flex-wrap items-center justify-between gap-2 pb-3 border-b border-gray-100 dark:border-white/10">
                <h2 class="text-sm sm:text-base font-bold text-[#101D3B] dark:text-white">Ganti Kata Sandi <span class="text-xs font-normal text-gray-400">(Opsional)</span></h2>
                <button type="button" @click="showPasswords = !showPasswords" class="text-xs font-semibold text-[#C6A044] hover:underline">
                    <span x-text="showPasswords ? 'Sembunyikan Sandi' : 'Lihat Sandi'"></span>
                </button>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5 sm:mb-2">Kata Sandi Saat Ini</label>
                <input :type="showPasswords ? 'text' : 'password'" name="current_password" placeholder="Masukkan sandi lama jika ingin mengganti sandi"
                       class="w-full rounded-xl border border-gray-200 dark:border-white/15 bg-white dark:bg-white/5 px-3.5 sm:px-4 py-2.5 text-sm text-gray-800 dark:text-white focus:border-[#C6A044] focus:ring-1 focus:ring-[#C6A044] outline-none transition">
                @error('current_password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5 sm:mb-2">Kata Sandi Baru</label>
                    <input :type="showPasswords ? 'text' : 'password'" name="password" placeholder="Minimal 8 karakter"
                           class="w-full rounded-xl border border-gray-200 dark:border-white/15 bg-white dark:bg-white/5 px-3.5 sm:px-4 py-2.5 text-sm text-gray-800 dark:text-white focus:border-[#C6A044] focus:ring-1 focus:ring-[#C6A044] outline-none transition">
                    @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-1.5 sm:mb-2">Konfirmasi Sandi Baru</label>
                    <input :type="showPasswords ? 'text' : 'password'" name="password_confirmation" placeholder="Ulangi sandi baru"
                           class="w-full rounded-xl border border-gray-200 dark:border-white/15 bg-white dark:bg-white/5 px-3.5 sm:px-4 py-2.5 text-sm text-gray-800 dark:text-white focus:border-[#C6A044] focus:ring-1 focus:ring-[#C6A044] outline-none transition">
                </div>
            </div>
        </div>

        <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3 pt-1">
            <a href="{{ $member ? route('portal.profile') : route('dashboard') }}" class="px-5 py-2.5 rounded-xl text-sm font-semibold text-center text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-white/15 bg-white dark:bg-white/5 hover:bg-gray-50 dark:hover:bg-white/10 transition">
                Kembali
            </a>
            <button type="submit"
                    :disabled="isSubmitting || isCompressing"
                    class="px-6 py-2.5 rounded-xl text-sm font-bold text-white bg-[#101D3B] dark:bg-[#C6A044] dark:text-[#101D3B] hover:bg-[#1a2d57] dark:hover:bg-[#d4b055] transition shadow-md disabled:opacity-60">
                <span x-text="isSubmitting ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
            </button>
        </div>

    </form>

</div>
@endsection

@push('scripts')
<script>
function compressImageForUpload(file, maxDimension = 1200, quality = 0.85) {
    return new Promise((resolve) => {
        // Only compress files larger than 500KB and readable by browser
        if (!file || file.size <= 500 * 1024 || file.type === 'image/gif') {
            resolve(file);
            return;
        }

        const reader = new FileReader();
        reader.onerror = () => resolve(file);
        reader.onload = (e) => {
            const img = new Image();
            img.onerror = () => resolve(file);
            img.onload = () => {
                let width = img.naturalWidth || img.width;
                let height = img.naturalHeight || img.height;

                if (width > maxDimension || height > maxDimension) {
                    if (width >= height) {
                        height = Math.round((height * maxDimension) / width);
                        width = maxDimension;
                    } else {
                        width = Math.round((width * maxDimension) / height);
                        height = maxDimension;
                    }
                }

                const canvas = document.createElement('canvas');
                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(img, 0, 0, width, height);

                canvas.toBlob((blob) => {
                    if (!blob || blob.size >= file.size) {
                        resolve(file);
                        return;
                    }
                    const baseName = file.name.replace(/\.[^/.]+$/, '') || 'profile';
                    const compressed = new File([blob], baseName + '.jpg', {
                        type: 'image/jpeg',
                        lastModified: Date.now(),
                    });
                    resolve(compressed);
                }, 'image/jpeg', quality);
            };
            img.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });
}

function profileForm(initialUrl, hasExistingPhoto) {
    return {
        previewUrl: initialUrl || null,
        hasExistingPhoto: Boolean(hasExistingPhoto),
        removePhoto: false,
        showPasswords: false,
        isCompressing: false,
        isSubmitting: false,
        fileInfo: '',

        async onFileChange(event) {
            const input = event.target;
            const file = input.files[0];
            if (!file) return;

            this.removePhoto = false;
            this.isCompressing = true;

            // Show immediate local preview
            const quickReader = new FileReader();
            quickReader.onload = (e) => {
                this.previewUrl = e.target.result;
            };
            quickReader.readAsDataURL(file);

            try {
                const optimized = await compressImageForUpload(file);
                if (optimized !== file && typeof DataTransfer !== 'undefined') {
                    const dt = new DataTransfer();
                    dt.items.add(optimized);
                    input.files = dt.files;
                }
                const kb = Math.max(1, Math.round((input.files[0]?.size || file.size) / 1024));
                this.fileInfo = kb >= 1024
                    ? 'Siap diunggah (' + (kb / 1024).toFixed(1) + ' MB)'
                    : 'Siap diunggah (' + kb + ' KB)';
            } catch (err) {
                // Keep original file if compression fails
                this.fileInfo = 'Foto dipilih';
            } finally {
                this.isCompressing = false;
            }
        },

        clearPhoto() {
            const input = document.getElementById('photo-upload');
            if (input) input.value = '';
            this.previewUrl = null;
            this.fileInfo = '';
            this.removePhoto = this.hasExistingPhoto;
        }
    };
}
</script>
@endpush
