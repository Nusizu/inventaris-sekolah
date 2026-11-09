<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="theme-color" content="#ffffff">

        <title>{{ config('app.name', 'Inventaris Sekolah') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-full font-sans antialiased">
        {{-- sidebarOpen dipakai bersama oleh sidebar (navigation) dan tombol menu di topbar --}}
        <div x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false">
            @include('layouts.navigation')

            <div class="lg:pl-64">
                <!-- Topbar: judul halaman + aksi cepat -->
                <div class="sticky top-0 z-30 flex h-16 items-center gap-3 border-b border-gray-200 bg-white/80 px-4 backdrop-blur sm:px-6 lg:px-8">
                    <button type="button" @click="sidebarOpen = true"
                            class="-ml-2 rounded-md p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-900 lg:hidden"
                            aria-label="Buka menu">
                        <x-icon name="menu" class="h-6 w-6" />
                    </button>

                    {{-- Judul halaman (slot "header") --}}
                    <div class="min-w-0 flex-1 [&>h2]:truncate [&>h2]:text-lg [&>h2]:font-semibold [&>h2]:tracking-tight [&>h2]:text-gray-900">
                        @isset($header)
                            {{ $header }}
                        @endisset
                    </div>

                    <div class="flex items-center gap-1">
                        @can('scan.use')
                            <a href="{{ route(Auth::user()->isAdmin() ? 'admin.scan' : 'user.scan') }}"
                               class="rounded-md p-2 transition-colors hover:bg-gray-100 {{ request()->routeIs('admin.scan', 'user.scan') ? 'bg-gray-100 text-gray-900' : 'text-gray-500 hover:text-gray-900' }}"
                               aria-label="Pindai QR Code" title="Pindai QR Code">
                                <x-icon name="qr-code" class="h-6 w-6" />
                            </a>
                        @endcan

                        <x-notification-bell />
                    </div>
                </div>

                <!-- Page Content -->
                <main class="px-4 sm:px-0">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @livewireScriptConfig
    </body>
</html>