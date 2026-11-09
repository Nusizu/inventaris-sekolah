<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#fafafa">

        <title>{{ config('app.name', 'Inventaris Sekolah') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-full font-sans text-gray-900 antialiased">
        <div class="flex min-h-screen flex-col items-center justify-center px-4 py-10">
            <a href="/" class="flex flex-col items-center gap-3">
                <span class="flex h-12 w-12 items-center justify-center rounded-lg bg-gray-900 text-white shadow-md">
                    <x-application-logo class="h-6 w-6" />
                </span>
                <span class="text-sm font-semibold tracking-tight text-gray-900">
                    {{ config('app.name', 'Inventaris Sekolah') }}
                </span>
            </a>

            <div class="mt-8 w-full max-w-sm rounded-lg bg-white p-6 shadow-sm sm:p-8">
                {{-- Judul kartu (opsional): <x-slot name="heading"> dan <x-slot name="subheading"> --}}
                @isset($heading)
                    <h1 class="text-xl font-semibold tracking-tight text-gray-900">{{ $heading }}</h1>
                @endisset

                @isset($subheading)
                    <p class="mt-1 text-sm text-gray-500">{{ $subheading }}</p>
                @endisset

                <div class="{{ isset($heading) ? 'mt-6' : '' }}">
                    {{ $slot }}
                </div>
            </div>
        </div>

        @livewireScriptConfig
    </body>
</html>