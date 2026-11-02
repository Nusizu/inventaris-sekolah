import './bootstrap';

import Alpine from 'alpinejs';
import flatpickr from 'flatpickr';
import { Indonesian } from 'flatpickr/dist/l10n/id.js';
import 'flatpickr/dist/flatpickr.min.css';

window.Alpine = Alpine;

Alpine.start();

const baseConfig = {
    locale: Indonesian,
    altInput: true,
    altFormat: 'j M Y',
    dateFormat: 'Y-m-d',
    onReady: (selectedDates, dateStr, instance) => {
        // Fokus otomatis ke field tanggal bila ia field pertama di form
        if (instance.input.hasAttribute('autofocus')) {
            instance.altInput.focus();
            instance.close();
        }
    },
};

document.addEventListener('DOMContentLoaded', () => {
    // Field tanggal di dalam form
    flatpickr('.js-date', {
        ...baseConfig,
        allowInput: false,
        altInputClass:
            'mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
    });

    // Field tanggal pada filter (boleh dikosongkan)
    flatpickr('.js-date-filter', {
        ...baseConfig,
        allowInput: true,
        altInputClass:
            'rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500',
    });
});

// Cegah klik ganda pada tombol submit (supaya data tidak tersimpan dua kali)
document.addEventListener('submit', (event) => {
    const form = event.target;

    // Dibatalkan oleh handler lain, misalnya dialog konfirmasi Hapus ditolak
    if (event.defaultPrevented) {
        return;
    }

    // Form sudah dikirim, abaikan pengiriman kedua
    if (form.dataset.submitted === 'true') {
        event.preventDefault();
        return;
    }

    form.dataset.submitted = 'true';
    form.querySelectorAll('button[type="submit"]').forEach((button) => {
        button.classList.add('opacity-50', 'cursor-not-allowed');
    });
});

// Saat pengguna menekan tombol Back, form dikembalikan ke keadaan normal
window.addEventListener('pageshow', () => {
    document.querySelectorAll('form[data-submitted]').forEach((form) => {
        delete form.dataset.submitted;
        form.querySelectorAll('button[type="submit"]').forEach((button) => {
            button.classList.remove('opacity-50', 'cursor-not-allowed');
        });
    });
});