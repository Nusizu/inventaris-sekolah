@props(['status'])

@php
    $classes = match ($status) {
        'menunggu' => 'bg-yellow-100 text-yellow-800',
        'dipinjam' => 'bg-blue-100 text-blue-800',
        'dikembalikan' => 'bg-green-100 text-green-800',
        'terlambat' => 'bg-red-100 text-red-800',
        'ditolak' => 'bg-rose-100 text-rose-800',
        'dibatalkan' => 'bg-gray-100 text-gray-700',
        default => 'bg-gray-100 text-gray-800',
    };
@endphp

<span class="rounded-full px-2 py-1 text-xs font-medium {{ $classes }}">
    {{ ucfirst($status) }}
</span>