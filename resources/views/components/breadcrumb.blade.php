@props(['items' => []])

<nav class="mb-5 text-sm" aria-label="Breadcrumb">
    <ol class="flex flex-wrap items-center gap-1.5 text-gray-500">
        @foreach ($items as $label => $url)
            <li class="flex items-center gap-1.5">
                @if ($url && ! $loop->last)
                    <a href="{{ $url }}" class="transition-colors hover:text-gray-900">{{ $label }}</a>
                    <x-icon name="chevron-right" class="h-3.5 w-3.5 text-gray-300" />
                @else
                    <span class="font-medium text-gray-900" @if ($loop->last) aria-current="page" @endif>{{ $label }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>