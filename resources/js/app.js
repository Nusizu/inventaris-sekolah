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