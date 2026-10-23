@props(['items' => []])

<nav class="mb-4 text-sm text-gray-500" aria-label="Breadcrumb">
    <ol class="flex flex-wrap items-center gap-2">
        @foreach ($items as $label => $url)
            <li class="flex items-center gap-2">
                @if ($url && ! $loop->last)
                    <a href="{{ $url }}" class="hover:text-indigo-600">{{ $label }}</a>
                    <span>/</span>
                @else
                    <span class="font-medium text-gray-800">{{ $label }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>