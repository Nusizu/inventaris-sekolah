@props(['label', 'value', 'color' => 'blue', 'href' => null])

@php
    $border = match ($color) {
        'green' => 'border-green-500',
        'yellow' => 'border-yellow-500',
        'red' => 'border-red-500',
        default => 'border-blue-500',
    };

    $text = match ($color) {
        'green' => 'text-green-600',
        'yellow' => 'text-yellow-600',
        'red' => 'text-red-600',
        default => 'text-blue-600',
    };
@endphp

<div class="rounded-lg border-l-4 {{ $border }} bg-white p-5 shadow-sm">
    <p class="text-xs font-semibold uppercase tracking-wide {{ $text }}">{{ $label }}</p>
    <p class="mt-2 text-2xl font-bold text-gray-800">{{ $value }}</p>

    @if ($href)
        <a href="{{ $href }}" class="mt-2 inline-block text-xs text-gray-500 hover:text-indigo-600">
            Lihat detail &rarr;
        </a>
    @endif
</div>