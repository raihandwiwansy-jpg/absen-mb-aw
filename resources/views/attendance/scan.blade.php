@extends('layouts.app')

@section('title', 'Scan QR Code Presensi')
@section('breadcrumb', 'Pemindai Presensi Mandiri')

@section('content')
<div class="max-w-xl mx-auto space-y-6"
     data-initial-token="{{ $token ?? '' }}"
     data-process-url="{{ route('attendance.process') }}"
     x-data="qrScanner($el.dataset.initialToken || '', $el.dataset.processUrl)"
     @qr-token-scanned.window="submitToken($event.detail.token)"
     @qr-camera-status.window="cameraStatus = $event.detail.status; if ($event.detail.msg) cameraErrorMsg = $event.detail.msg;"
     x-init="
        if (manualToken) {
            submitToken(manualToken);
        }
     ">

    {{-- Scanner Card --}}
    <div class="bg-white rounded-3xl border border-gray-100 shadow-xl overflow-hidden p-6 lg:p-8 text-center relative transition-all duration-300">

        {{-- Top Badge & Sound Toggle --}}
        <div class="flex items-center justify-between mb-4">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-[#101D3B]/5 border border-[#C6A044]/30 text-[#101D3B] text-xs font-bold uppercase tracking-wider">
                <span class="w-2 h-2 rounded-full bg-[#C6A044] animate-ping"></span>
                <span>Pemindai Presensi Mandiri</span>
            </div>

            {{-- Sound Feedback Toggle --}}
            <button type="button"
                    @click="soundEnabled = !soundEnabled"
                    :class="soundEnabled ? 'text-[#C6A044] bg-[#C6A044]/10 border border-[#C6A044]/30' : 'text-gray-400 bg-gray-100'"
                    class="p-2 rounded-xl transition"
                    :title="soundEnabled ? 'Suara Aktif' : 'Suara Senyap'">
                <template x-if="soundEnabled">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/></svg>
                </template>
                <template x-if="!soundEnabled">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2"/></svg>
                </template>
            </button>
        </div>

        <h2 class="text-2xl font-black text-[#101D3B]">Scan QR Code Presensi</h2>
        <p class="text-xs text-gray-400 mt-1 max-w-sm mx-auto">
            Arahkan kamera ke QR Code di layar proyektor atau gunakan tombol foto galeri di bawah.
        </p>

        {{-- Notice if accessed via insecure HTTP on mobile --}}
        <template x-if="isInsecure">
            <div class="mt-3 p-3 rounded-2xl bg-amber-500/10 border border-amber-500/30 text-amber-800 text-xs text-left flex items-start gap-2">
                <svg class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                <div class="text-[11px] leading-relaxed">
                    <strong>Catatan Mobile/HP:</strong> Browser HP (iOS/Android) membatasi akses streaming kamera langsung pada jaringan HTTP. Jika kamera gelap, gunakan tombol <strong>"Ambil Foto QR / Pilih dari Galeri"</strong> di bawah.
                </div>
            </div>
        </template>

        {{-- Futuristic Viewfinder Container with HUD Brackets & Laser Sweep --}}
        <div class="my-5 relative max-w-sm mx-auto">
            <div class="relative rounded-3xl overflow-hidden border-2 border-[#C6A044]/60 bg-[#101D3B] shadow-2xl p-2">

                {{-- Golden HUD Corner Brackets --}}
                <div class="scanner-corner scanner-corner-tl"></div>
                <div class="scanner-corner scanner-corner-tr"></div>
                <div class="scanner-corner scanner-corner-bl"></div>
                <div class="scanner-corner scanner-corner-br"></div>

                {{-- Sweeping Laser Line --}}
                <div class="scanner-laser-line"></div>

                {{-- Camera Feed Element --}}
                <div id="reader" class="rounded-2xl overflow-hidden w-full bg-black min-h-[250px] relative"></div>

                {{-- Fallback message when camera cannot be activated --}}
                <div x-show="cameraStatus === 'error'"
                     class="absolute inset-2 rounded-2xl bg-[#101D3B]/90 flex flex-col items-center justify-center p-6 text-center text-white"
                     style="display: none;">
                    <div class="w-12 h-12 rounded-full bg-amber-500/20 text-[#C6A044] flex items-center justify-center mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <p class="text-xs font-bold text-white mb-1">Kamera Langsung Tidak Terdeteksi</p>
                    <p class="text-[11px] text-gray-300 max-w-xs leading-relaxed" x-text="cameraErrorMsg || 'Browser memblokir streaming video atau izin kamera belum diberikan.'"></p>
                    <p class="text-[10px] text-[#C6A044] mt-2 font-semibold">Silakan gunakan tombol foto di bawah ini ↓</p>
                </div>
            </div>

            {{-- Camera Status Indicator --}}
            <div class="mt-2.5 flex items-center justify-between text-xs px-1 text-gray-500">
                <span class="flex items-center gap-1.5 font-medium">
                    <template x-if="cameraStatus === 'active'">
                        <span class="inline-flex items-center gap-1.5 text-emerald-600">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Kamera Belakang Aktif
                        </span>
                    </template>
                    <template x-if="cameraStatus === 'starting'">
                        <span class="inline-flex items-center gap-1.5 text-amber-600">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                            Menghubungkan kamera...
                        </span>
                    </template>
                    <template x-if="cameraStatus === 'error'">
                        <span class="inline-flex items-center gap-1.5 text-red-500">
                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                            Mode Foto Siap Digunakan
                        </span>
                    </template>
                </span>

                <button type="button"
                        onclick="window.restartCameraLive()"
                        class="text-[11px] text-[#C6A044] hover:underline font-bold">
                    Muat Ulang Kamera
                </button>
            </div>
        </div>

        {{-- BUTTON 1: NATIVE PHONE CAMERA / GALLERY SCAN (100% Reliable on iPhone & Android) --}}
        <div class="my-4">
            <input type="file"
                   id="qr-file-input"
                   accept="image/*"
                   capture="environment"
                   class="hidden"
                   @change="scanFromFile($event)">
            <button type="button"
                    @click="document.getElementById('qr-file-input').click()"
                    class="w-full inline-flex items-center justify-center gap-2.5 py-3 px-4 rounded-2xl bg-gradient-to-r from-[#101D3B] to-[#1e315e] text-white hover:from-[#1a2d57] hover:to-[#2b447e] font-bold text-xs sm:text-sm transition shadow-lg border border-[#C6A044]/40 hover:scale-[1.01] active:scale-95">
                <svg class="w-5 h-5 text-[#C6A044]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>Ambil Foto QR / Unggah dari Galeri HP</span>
            </button>
            <p class="text-[10px] text-gray-400 mt-1">Kompatibel dengan semua smartphone (iPhone, iPad, Android)</p>
        </div>

        {{-- Processing State --}}
        <div x-show="isProcessing"
             x-transition
             style="display: none;"
             class="my-4 p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs font-bold flex items-center justify-center gap-3 shadow-sm">
            <svg class="animate-spin h-5 w-5 text-[#C6A044]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <span>Memverifikasi token presensi dan mencatat kehadiran...</span>
        </div>

        {{-- Success Celebration Card --}}
        <template x-if="resultStatus === 'success'">
            <div class="my-5 p-6 rounded-3xl bg-gradient-to-br from-emerald-500 to-emerald-700 text-white shadow-xl animate-scale-in text-center relative overflow-hidden">
                <div class="w-14 h-14 mx-auto rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-white text-2xl font-black mb-3 border border-white/30 shadow-inner">
                    ✓
                </div>
                <h3 class="text-lg font-black tracking-tight">Presensi Berhasil Dicatat!</h3>
                <p class="text-xs text-emerald-100 mt-1" x-text="resultMessage"></p>

                <div class="mt-4 pt-3 border-t border-white/20 flex items-center justify-center gap-4 text-xs font-medium">
                    <span>Waktu: <strong>{{ now()->format('H:i:s') }} WIB</strong></span>
                    <span>•</span>
                    <button type="button" @click="resetScanner()" class="underline font-bold text-white hover:text-emerald-100">
                        Scan Sesi Lainnya &rarr;
                    </button>
                </div>
            </div>
        </template>

        {{-- Error Card --}}
        <template x-if="resultStatus === 'error'">
            <div class="my-5 p-5 rounded-2xl bg-red-50 border border-red-200 text-red-900 text-xs font-semibold flex items-center justify-between gap-3 animate-fade-in text-left">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center font-bold flex-shrink-0">
                        ✕
                    </div>
                    <div>
                        <p class="font-bold">Presensi Gagal</p>
                        <p class="text-red-700 font-normal mt-0.5" x-text="resultMessage"></p>
                    </div>
                </div>
                <button type="button" @click="resetScanner()" class="text-xs font-bold text-red-800 underline flex-shrink-0">
                    Coba Lagi
                </button>
            </div>
        </template>

        {{-- Manual Token Fallback --}}
        <div class="mt-8 pt-6 border-t border-gray-100 text-left">
            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Atau Masukkan Token Sesi Manual</label>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2">
                <input type="text"
                       x-model="manualToken"
                       placeholder="Ketik kode token sesi (misal: A1B2C3D4)..."
                       class="form-input flex-1 rounded-xl border-gray-200 text-xs font-mono uppercase focus:border-[#C6A044] focus:ring-[#C6A044]">
                <button type="button"
                        @click="submitToken(manualToken)"
                        class="btn-primary px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-[#101D3B] hover:bg-[#1a2d57] transition shadow-xs flex-shrink-0 justify-center">
                    Kirim Token
                </button>
            </div>
            <p class="text-[11px] text-gray-400 mt-1.5">Gunakan opsi ini jika kamera tidak dapat membaca atau sedang terjadi kendala sinyal.</p>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
function qrScanner(initialToken, processUrl) {
    return {
        manualToken: initialToken || '',
        isProcessing: false,
        resultMessage: '',
        resultStatus: '',
        attendeeData: null,
        soundEnabled: true,
        cameraStatus: 'starting',
        cameraErrorMsg: '',
        isInsecure: location.protocol !== 'https:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1',

        submitToken(tokenValue) {
            if (!tokenValue) {
                window.showToast('Token kosong atau tidak valid.', 'warning');
                return;
            }

            let cleanToken = tokenValue.trim();
            try {
                if (cleanToken.includes('http://') || cleanToken.includes('https://')) {
                    const parsedUrl = new URL(cleanToken);
                    if (parsedUrl.searchParams.has('token')) {
                        cleanToken = parsedUrl.searchParams.get('token');
                    }
                }
            } catch (e) {}

            this.isProcessing = true;
            this.resultMessage = '';
            this.attendeeData = null;

            fetch(processUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ token: cleanToken })
            })
            .then(res => res.json().then(data => ({ status: res.status, body: data })))
            .then(res => {
                this.isProcessing = false;
                if (res.status === 200 && res.body.success) {
                    this.resultStatus = 'success';
                    this.resultMessage = res.body.message;
                    this.attendeeData = res.body.attendance || {};
                    if (this.soundEnabled) {
                        window.playAudioFeedback('success');
                    }
                    window.triggerHaptic('success');
                    window.showToast(res.body.message, 'success');
                } else {
                    this.resultStatus = 'error';
                    this.resultMessage = res.body.message || 'Gagal mencatat presensi.';
                    if (this.soundEnabled) {
                        window.playAudioFeedback('error');
                    }
                    window.triggerHaptic('error');
                    window.showToast(this.resultMessage, 'error');
                }
            })
            .catch(() => {
                this.isProcessing = false;
                this.resultStatus = 'error';
                this.resultMessage = 'Terjadi kesalahan koneksi ke server.';
                window.showToast(this.resultMessage, 'error');
            });
        },

        scanFromFile(e) {
            const file = e.target.files[0];
            if (!file) return;

            this.isProcessing = true;
            if (window.html5QrCodeScannerInstance) {
                window.html5QrCodeScannerInstance.scanFile(file, true)
                    .then(decodedText => {
                        this.isProcessing = false;
                        this.submitToken(decodedText);
                    })
                    .catch(() => {
                        this.isProcessing = false;
                        window.showToast('Gagal mendeteksi QR Code dari foto. Pastikan gambar cukup terang dan fokus.', 'error');
                    });
            } else {
                this.isProcessing = false;
                window.showToast('Pemindai belum siap. Silakan muat ulang halaman.', 'error');
            }
        },

        resetScanner() {
            this.resultMessage = '';
            this.resultStatus = '';
            this.attendeeData = null;
        }
    };
}

document.addEventListener('DOMContentLoaded', function () {
    const readerElement = document.getElementById("reader");
    if (!readerElement) return;

    window.html5QrCodeScannerInstance = new Html5Qrcode("reader");
    let hasScanned = false;

    const qrCodeSuccessCallback = (decodedText, decodedResult) => {
        if (hasScanned) return;
        hasScanned = true;

        let token = decodedText;
        try {
            if (decodedText.includes('http://') || decodedText.includes('https://')) {
                const url = new URL(decodedText);
                if (url.searchParams.has('token')) {
                    token = url.searchParams.get('token');
                }
            }
        } catch (e) {}

        // Dispatch clean global custom event for Alpine component
        window.dispatchEvent(new CustomEvent('qr-token-scanned', {
            detail: { token: token }
        }));

        setTimeout(() => {
            hasScanned = false;
        }, 3500);
    };

    const config = {
        fps: 15,
        qrbox: { width: 250, height: 250 },
        aspectRatio: 1.0,
        experimentalFeatures: {
            useBarCodeDetectorIfSupported: true
        }
    };

    function setCameraStatus(status, msg = '') {
        window.dispatchEvent(new CustomEvent('qr-camera-status', {
            detail: { status: status, msg: msg }
        }));
    }

    function startCamera() {
        setCameraStatus('starting');

        // Check if browser environment supports getUserMedia
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            console.warn("navigator.mediaDevices.getUserMedia is not available in this context.");
            setCameraStatus('error', 'Browser membatasi kamera langsung pada koneksi non-HTTPS. Silakan gunakan tombol "Ambil Foto QR" di bawah.');
            return;
        }

        // Try environment (rear) camera first
        window.html5QrCodeScannerInstance.start(
            { facingMode: "environment" },
            config,
            qrCodeSuccessCallback
        ).then(() => {
            setCameraStatus('active');
        }).catch(err => {
            console.warn("Rear camera start failed, attempting fallback:", err);
            // Fallback to any available camera
            window.html5QrCodeScannerInstance.start(
                { facingMode: "user" },
                config,
                qrCodeSuccessCallback
            ).then(() => {
                setCameraStatus('active');
            }).catch(fallbackErr => {
                console.warn("All camera attempts failed:", fallbackErr);
                setCameraStatus('error', 'Izin kamera ditolak atau perangkat tidak mendukung. Gunakan tombol "Ambil Foto QR" di bawah.');
            });
        });
    }

    window.restartCameraLive = function () {
        if (window.html5QrCodeScannerInstance && window.html5QrCodeScannerInstance.isScanning) {
            window.html5QrCodeScannerInstance.stop().then(() => {
                startCamera();
            }).catch(() => {
                startCamera();
            });
        } else {
            startCamera();
        }
    };

    // Delay start slightly to let DOM render completely
    setTimeout(startCamera, 300);
});
</script>
@endpush
