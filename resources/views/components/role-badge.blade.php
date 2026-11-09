{{-- Badge nama peran. Pemakaian: <x-role-badge :role="$userRole->name" /> --}}
@props(['role'])

@php
    $classes = match ($role) {
        'admin' => 'bg-violet-50 text-violet-700 ring-violet-600/20',
        'petugas' => 'bg-brand-50 text-brand-700 ring-brand-600/20',
        'user' => 'bg-sky-50 text-sky-700 ring-sky-600/20',
        default => 'bg-gray-100 text-gray-600 ring-gray-500/20',
    };
@endphp

<span class="inline-flex items-center whitespace-nowrap rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $classes }}">
    {{ ucfirst($role) }}
</span>