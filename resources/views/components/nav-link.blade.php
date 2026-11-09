{{-- Item menu sidebar. Pemakaian: <x-nav-link :href="..." :active="..." icon="home">Dashboard</x-nav-link> --}}
@props(['active' => false, 'icon' => null])

@php
    $classes = 'group flex items-center gap-3 rounded-md px-3 py-2 text-sm font-medium transition-colors '
        . ($active
            ? 'bg-gray-100 text-gray-900'
            : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900');

    $iconClasses = 'h-5 w-5 shrink-0 transition-colors '
        . ($active ? 'text-brand-600' : 'text-gray-400 group-hover:text-gray-600');
@endphp

<a {{ $attributes->merge(['class' => $classes]) }} @if ($active) aria-current="page" @endif>
    @if ($icon)
        <x-icon :name="$icon" :class="$iconClasses" />
    @endif

    <span class="truncate">{{ $slot }}</span>
</a>