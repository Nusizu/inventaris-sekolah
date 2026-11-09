import Chart from 'chart.js/auto';

const COLORS = {
    dipinjam: '#0ea5e9', // sky-500
    terlambat: '#ef4444', // red-500
    dikembalikan: '#10b981', // emerald-500
};

// Gaya dasar grafik: font & warna teks mengikuti tema aplikasi
Chart.defaults.font.family = '"Inter Variable", Inter, ui-sans-serif, system-ui, sans-serif';
Chart.defaults.font.size = 12;
Chart.defaults.color = '#71717a'; // gray-500
Chart.defaults.borderColor = '#f4f4f5'; // gray-100

const LABELS = {
    dipinjam: 'Dipinjam',
    terlambat: 'Terlambat',
    dikembalikan: 'Dikembalikan',
};

const Y_TITLES = {
    count: 'Jumlah peminjaman',
    units: 'Jumlah unit barang',
};

export default function initLoanTrendChart(canvas) {
    const data = JSON.parse(canvas.dataset.trend);
    const buttons = document.querySelectorAll('[data-chart-range], [data-chart-metric]');

    let range = 6;
    let metric = 'count';
    let chart = null;

    const datasets = () =>
        Object.keys(LABELS).map((status) => ({
            label: LABELS[status],
            data: data[metric][status].slice(-range),
            backgroundColor: COLORS[status],
            borderRadius: 4,
            maxBarThickness: 36,
        }));

    const updateButtons = () => {
        buttons.forEach((button) => {
            const active = button.dataset.chartRange
                ? Number(button.dataset.chartRange) === range
                : button.dataset.chartMetric === metric;

            button.classList.toggle('bg-gray-800', active);
            button.classList.toggle('text-white', active);
            button.classList.toggle('bg-white', !active);
            button.classList.toggle('text-gray-700', !active);
        });
    };

    const render = () => {
        const labels = data.labels.slice(-range);

        if (chart) {
            chart.data.labels = labels;
            chart.data.datasets = datasets();
            chart.options.scales.y.title.text = Y_TITLES[metric];
            chart.update();
        } else {
            chart = new Chart(canvas, {
                type: 'bar',
                data: { labels, datasets: datasets() },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    scales: {
                        x: { stacked: true, grid: { display: false } },
                        y: {
                            stacked: true,
                            beginAtZero: true,
                            ticks: { precision: 0 },
                            border: { display: false },
                            title: { display: true, text: Y_TITLES[metric] },
                        },
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { usePointStyle: true, pointStyle: 'circle', boxWidth: 8, boxHeight: 8, padding: 16 },
                        },
                        tooltip: {
                            backgroundColor: '#18181b',
                            padding: 10,
                            cornerRadius: 8,
                            callbacks: {
                                footer: (items) =>
                                    'Total: ' +
                                    items.reduce((sum, item) => sum + item.parsed.y, 0).toLocaleString('id-ID'),
                            },
                        },
                    },
                },
            });
        }

        updateButtons();
    };

    buttons.forEach((button) => {
        button.addEventListener('click', () => {
            if (button.dataset.chartRange) {
                range = Number(button.dataset.chartRange);
            }

            if (button.dataset.chartMetric) {
                metric = button.dataset.chartMetric;
            }

            render();
        });
    });

    render();
}