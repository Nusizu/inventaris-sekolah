@props(['label', 'value', 'color' => 'blue', 'href' => null])

@php
    $dot = match ($color) {
        'green' => 'bg-emerald-500',
        'yellow' => 'bg-amber-500',
        'red' => 'bg-red-500',
        'purple' => 'bg-violet-500',
        default => 'bg-sky-500',
    };
@endphp

<div class="card group relative p-5 transition-shadow {{ $href ? 'hover:shadow-md' : '' }}">
    <div class="flex items-center justify-between gap-2">
        <div class="flex items-center gap-2">
            <span class="h-2 w-2 rounded-full {{ $dot }}"></span>
            <p class="text-sm font-medium text-gray-500">{{ $label }}</p>
        </div>

        @if ($href)
            <x-icon name="arrow-right" class="h-4 w-4 -translate-x-1 text-gray-300 opacity-0 transition group-hover:translate-x-0 group-hover:opacity-100" />
        @endif
    </div>

    <p class="mt-3 text-3xl font-semibold tracking-tight text-gray-900 tabular-nums">{{ $value }}</p>

    @if ($href)
        {{-- Seluruh kartu bisa diklik --}}
        <a href="{{ $href }}" class="absolute inset-0 rounded-lg focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-brand-500">
            <span class="sr-only">Lihat detail {{ $label }}</span>
        </a>
    @endif
</div>