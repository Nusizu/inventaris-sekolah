@props(['status'])

@php
    // [kelas badge, kelas titik]
    $styles = [
        'menunggu'     => ['bg-amber-50 text-amber-700 ring-amber-600/20', 'bg-amber-500'],
        'dipinjam'     => ['bg-sky-50 text-sky-700 ring-sky-600/20', 'bg-sky-500'],
        'dikembalikan' => ['bg-emerald-50 text-emerald-700 ring-emerald-600/20', 'bg-emerald-500'],
        'terlambat'    => ['bg-red-50 text-red-700 ring-red-600/20', 'bg-red-500'],
        'ditolak'      => ['bg-rose-50 text-rose-700 ring-rose-600/20', 'bg-rose-500'],
        'dibatalkan'   => ['bg-gray-100 text-gray-600 ring-gray-500/20', 'bg-gray-400'],
    ];

    [$classes, $dot] = $styles[$status] ?? $styles['dibatalkan'];
@endphp

<span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $classes }}">
    <span class="h-1.5 w-1.5 rounded-full {{ $dot }}"></span>
    {{ ucfirst($status) }}
</span>