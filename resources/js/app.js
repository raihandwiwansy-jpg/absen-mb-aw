// Symphony Marching Band Management System (2026 Edition)
// Modern UI/UX Interactive Engine

// ============================================================
// 1. Web Audio API - Chime & Sound Synthesizer
// ============================================================
let audioCtx = null;

function getAudioContext() {
    if (!audioCtx) {
        const AudioContextClass = window.AudioContext || window.webkitAudioContext;
        if (AudioContextClass) {
            audioCtx = new AudioContextClass();
        }
    }
    if (audioCtx && audioCtx.state === 'suspended') {
        audioCtx.resume();
    }
    return audioCtx;
}

/**
 * Play synthesizer audio feedback for scans and actions
 * @param {'success'|'error'|'click'|'beep'} type
 */
window.playAudioFeedback = function (type = 'success') {
    try {
        const ctx = getAudioContext();
        if (!ctx) return;

        const now = ctx.currentTime;

        if (type === 'success') {
            // Elegant 2-tone melodic chime: E5 (659.25Hz) -> A5 (880Hz)
            const osc1 = ctx.createOscillator();
            const gain1 = ctx.createGain();
            osc1.type = 'sine';
            osc1.frequency.setValueAtTime(659.25, now);
            gain1.gain.setValueAtTime(0.18, now);
            gain1.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
            osc1.connect(gain1);
            gain1.connect(ctx.destination);
            osc1.start(now);
            osc1.stop(now + 0.35);

            const osc2 = ctx.createOscillator();
            const gain2 = ctx.createGain();
            osc2.type = 'sine';
            osc2.frequency.setValueAtTime(880.0, now + 0.12);
            gain2.gain.setValueAtTime(0.22, now + 0.12);
            gain2.gain.exponentialRampToValueAtTime(0.001, now + 0.55);
            osc2.connect(gain2);
            gain2.connect(ctx.destination);
            osc2.start(now + 0.12);
            osc2.stop(now + 0.55);
        } else if (type === 'error') {
            // Soft low warning tone: 220Hz -> 180Hz
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'triangle';
            osc.frequency.setValueAtTime(220, now);
            osc.frequency.linearRampToValueAtTime(180, now + 0.3);
            gain.gain.setValueAtTime(0.2, now);
            gain.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start(now);
            osc.stop(now + 0.35);
        } else if (type === 'click') {
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.type = 'sine';
            osc.frequency.setValueAtTime(1200, now);
            gain.gain.setValueAtTime(0.05, now);
            gain.gain.exponentialRampToValueAtTime(0.001, now + 0.05);
            osc.connect(gain);
            gain.connect(ctx.destination);
            osc.start(now);
            osc.stop(now + 0.05);
        }
    } catch (e) {
        // AudioContext might be blocked until user gesture, safely ignore
    }
};

/**
 * Mobile Haptic feedback trigger
 */
window.triggerHaptic = function (type = 'success') {
    if ('vibrate' in navigator) {
        if (type === 'success') {
            navigator.vibrate([60, 40, 80]);
        } else if (type === 'error') {
            navigator.vibrate([120, 80, 120]);
        } else {
            navigator.vibrate(40);
        }
    }
};

// ============================================================
// 2. Modern 2026 Toast Notification System
// ============================================================
let toastContainer = null;

function ensureToastContainer() {
    if (!toastContainer || !document.body.contains(toastContainer)) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'symphony-toast-container';
        toastContainer.className = 'fixed bottom-6 right-6 z-[9999] flex flex-col gap-2.5 pointer-events-none max-w-sm w-full px-4 sm:px-0';
        document.body.appendChild(toastContainer);
    }
    return toastContainer;
}

/**
 * Modern floating toast
 * @param {string} message
 * @param {'success'|'error'|'warning'|'info'} type
 * @param {number} duration
 */
window.showToast = function (message, type = 'success', duration = 3500) {
    const container = ensureToastContainer();

    const config = {
        success: {
            bg: 'bg-[#101D3B] text-white border border-[#C6A044]/40',
            bar: 'bg-[#C6A044]',
            icon: `<svg class="w-5 h-5 text-emerald-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>`,
            sound: 'success'
        },
        error: {
            bg: 'bg-red-950 text-white border border-red-500/40',
            bar: 'bg-red-500',
            icon: `<svg class="w-5 h-5 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>`,
            sound: 'error'
        },
        warning: {
            bg: 'bg-[#202B40] text-white border border-amber-500/40',
            bar: 'bg-amber-400',
            icon: `<svg class="w-5 h-5 text-amber-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01M5.07 19H19a2 2 0 001.75-2.75L13.75 4a2 2 0 00-3.5 0L2.25 16.25A2 2 0 004 19h1.07z"/></svg>`,
            sound: 'error'
        },
        info: {
            bg: 'bg-[#101D3B] text-white border border-blue-400/40',
            bar: 'bg-blue-400',
            icon: `<svg class="w-5 h-5 text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`,
            sound: 'click'
        }
    };

    const cfg = config[type] || config.info;
    window.playAudioFeedback(cfg.sound);
    window.triggerHaptic(type);

    const toast = document.createElement('div');
    toast.className = `pointer-events-auto relative overflow-hidden rounded-2xl p-4 shadow-2xl backdrop-blur-xl transition-all duration-300 transform translate-y-3 opacity-0 ${cfg.bg}`;
    toast.style.boxShadow = '0 12px 36px rgba(16, 29, 59, 0.45)';

    toast.innerHTML = `
        <div class="flex items-start gap-3">
            ${cfg.icon}
            <div class="flex-1 text-xs font-semibold leading-relaxed">${message}</div>
            <button class="text-white/60 hover:text-white transition p-0.5 rounded-lg ml-1 text-sm font-bold leading-none">&times;</button>
        </div>
        <div class="toast-progress absolute bottom-0 left-0 h-1 ${cfg.bar}" style="width: 100%; transition: width ${duration}ms linear;"></div>
    `;

    container.appendChild(toast);

    // Animate enter
    requestAnimationFrame(() => {
        toast.classList.remove('translate-y-3', 'opacity-0');
        const bar = toast.querySelector('.toast-progress');
        if (bar) {
            requestAnimationFrame(() => {
                bar.style.width = '0%';
            });
        }
    });

    // Close button
    toast.querySelector('button').addEventListener('click', () => {
        dismissToast(toast);
    });

    const timer = setTimeout(() => {
        dismissToast(toast);
    }, duration);

    function dismissToast(el) {
        clearTimeout(timer);
        el.classList.add('opacity-0', 'scale-95');
        setTimeout(() => el.remove(), 250);
    }
};

// Aliases for compatibility
window.flashToast = window.showToast;

// ============================================================
// 3. Smooth Counter Number Animation
// ============================================================
/**
 * Smoothly count up a number in an HTML element
 * @param {HTMLElement} el
 * @param {number} targetValue
 * @param {number} durationMs
 * @param {string} suffix
 */
window.animateNumber = function (el, targetValue, durationMs = 1200, suffix = '') {
    if (!el) return;
    const startValue = parseInt(el.textContent.replace(/[^0-9]/g, ''), 10) || 0;
    const startTime = performance.now();

    function updateCounter(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / durationMs, 1);
        // EaseOutExpo easing
        const easeProgress = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
        const currentVal = Math.floor(startValue + (targetValue - startValue) * easeProgress);

        el.textContent = currentVal.toLocaleString('id-ID') + suffix;

        if (progress < 1) {
            requestAnimationFrame(updateCounter);
        } else {
            el.textContent = targetValue.toLocaleString('id-ID') + suffix;
        }
    }

    requestAnimationFrame(updateCounter);
};

window.animateAllCounters = function () {
    const counterElements = document.querySelectorAll('[data-counter]');
    counterElements.forEach(el => {
        const target = parseFloat(el.getAttribute('data-counter')) || 0;
        const suffix = el.getAttribute('data-counter-suffix') || '';
        window.animateNumber(el, target, 1000, suffix);
    });
};

// ============================================================
// 4. Digital Clock in Indonesian Format
// ============================================================
window.initDigitalClock = function (elementId) {
    const clockEl = document.getElementById(elementId);
    if (!clockEl) return;

    function update() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        clockEl.textContent = `${hours}:${minutes}:${seconds} WIB`;
    }

    update();
    setInterval(update, 1000);
};

// ============================================================
// 5. Copy to Clipboard Utility
// ============================================================
window.copyToClipboard = function (text) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(() => {
            window.showToast('Kode berhasil disalin ke clipboard!', 'success');
        }).catch(() => fallbackCopy(text));
    } else {
        fallbackCopy(text);
    }

    function fallbackCopy(val) {
        const temp = document.createElement('textarea');
        temp.value = val;
        document.body.appendChild(temp);
        temp.select();
        document.execCommand('copy');
        document.body.removeChild(temp);
        window.showToast('Kode berhasil disalin!', 'success');
    }
};

// ============================================================
// 6. Photo Previewer
// ============================================================
window.previewPhoto = function (input, previewId) {
    const preview = document.getElementById(previewId);
    if (!preview) return;
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = (e) => {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
};

// ============================================================
// 7. Auto-dismiss alerts & Init on DOMContentLoaded
// ============================================================
document.addEventListener('DOMContentLoaded', () => {
    // Auto-dismiss standard alerts
    const alerts = document.querySelectorAll('[data-auto-dismiss]');
    alerts.forEach((alert) => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });

    // Auto-run counters if any
    window.animateAllCounters();
});
