@props(['status'])

@php
    $classes = match ($status) {
        'dipinjam' => 'bg-blue-100 text-blue-800',
        'dikembalikan' => 'bg-green-100 text-green-800',
        'terlambat' => 'bg-red-100 text-red-800',
        default => 'bg-gray-100 text-gray-800',
    };
@endphp

<span class="rounded-full px-2 py-1 text-xs font-medium {{ $classes }}">
    {{ ucfirst($status) }}
</span>