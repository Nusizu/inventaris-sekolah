import { Html5Qrcode } from 'html5-qrcode';

// Mendukung QR berisi kode biasa (contoh: LAB-001) maupun alamat web
const extractCode = (text) => {
    const value = text.trim();

    if (/^https?:\/\//i.test(value)) {
        try {
            const url = new URL(value);

            return url.searchParams.get('code') || url.pathname.split('/').filter(Boolean).pop() || value;
        } catch (error) {
            return value;
        }
    }

    return value;
};

export default function initQrScanner(element) {
    const statusEl = document.getElementById('qr-status');
    const startButton = document.getElementById('qr-start');
    const stopButton = document.getElementById('qr-stop');
    const scanUrl = element.dataset.scanUrl;

    const scanner = new Html5Qrcode(element.id);
    let running = false;

    const setStatus = (message, isError = false) => {
        statusEl.textContent = message;
        statusEl.classList.toggle('text-red-600', isError);
        statusEl.classList.toggle('text-gray-600', !isError);
    };

    const showButtons = () => {
        startButton.classList.toggle('hidden', running);
        stopButton.classList.toggle('hidden', !running);
        stopButton.classList.toggle('inline-flex', running);
    };

    const stop = async () => {
        if (!running) {
            return;
        }

        try {
            await scanner.stop();
        } catch (error) {
            // Kamera sudah berhenti, abaikan
        }

        running = false;
        showButtons();
    };

    const onDecoded = async (text) => {
        await stop();

        window.location.href = scanUrl + '?code=' + encodeURIComponent(extractCode(text));
    };

    const start = async () => {
        if (!window.isSecureContext) {
            setStatus('Kamera hanya dapat dipakai lewat localhost atau HTTPS. Ketik kode barang di kolom bawah.', true);

            return;
        }

        try {
            await scanner.start(
                { facingMode: 'environment' },
                { fps: 10, qrbox: { width: 240, height: 240 } },
                onDecoded,
                () => {}
            );

            running = true;
            showButtons();
            setStatus('Arahkan kamera ke QR Code pada label barang.');
        } catch (error) {
            setStatus('Kamera tidak dapat diakses. Izinkan akses kamera di browser, atau ketik kode barang di kolom bawah.', true);
        }
    };

    startButton.addEventListener('click', start);
    stopButton.addEventListener('click', stop);
    window.addEventListener('pagehide', stop);
}