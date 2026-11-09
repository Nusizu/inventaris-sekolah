{{-- Badge kondisi barang. Pemakaian: <x-condition-badge :condition="$item->condition" /> --}}
@props(['condition'])

@php
    // [kelas badge, kelas titik]
    $styles = [
        'baik'         => ['bg-emerald-50 text-emerald-700 ring-emerald-600/20', 'bg-emerald-500'],
        'rusak ringan' => ['bg-amber-50 text-amber-700 ring-amber-600/20', 'bg-amber-500'],
        'rusak berat'  => ['bg-red-50 text-red-700 ring-red-600/20', 'bg-red-500'],
    ];

    [$classes, $dot] = $styles[$condition] ?? $styles['rusak berat'];
@endphp

<span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $classes }}">
    <span class="h-1.5 w-1.5 rounded-full {{ $dot }}"></span>
    {{ ucfirst($condition) }}
</span>