{{-- Sidebar. Di layar besar selalu tampil; di HP menjadi panel geser (state: sidebarOpen dari layouts/app) --}}

<!-- Latar gelap (HP) -->
<div x-show="sidebarOpen" x-cloak
     x-transition:enter="transition-opacity ease-out duration-200"
     x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition-opacity ease-in duration-150"
     x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
     @click="sidebarOpen = false"
     class="fixed inset-0 z-40 bg-gray-900/40 backdrop-blur-sm lg:hidden" aria-hidden="true"></div>

<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
       class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col border-r border-gray-200 bg-white transition-transform duration-200 ease-out lg:translate-x-0"
       aria-label="Menu utama">

    <!-- Merek -->
    <div class="flex h-16 shrink-0 items-center justify-between px-5">
        <a href="{{ route(Auth::user()->isAdmin() ? 'admin.dashboard' : 'user.dashboard') }}" class="flex items-center gap-3">
            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-gray-900 text-white">
                <x-application-logo class="h-5 w-5" />
            </span>
            <span class="text-sm font-semibold leading-tight tracking-tight text-gray-900">
                {{ config('app.name', 'Inventaris Sekolah') }}
            </span>
        </a>

        <button type="button" @click="sidebarOpen = false"
                class="-mr-2 rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-700 lg:hidden"
                aria-label="Tutup menu">
            <x-icon name="close" class="h-5 w-5" />
        </button>
    </div>

    <!-- Tautan menu -->
    <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
        <x-nav-link :href="route(Auth::user()->isAdmin() ? 'admin.dashboard' : 'user.dashboard')"
                    :active="request()->routeIs('admin.dashboard', 'user.dashboard')" icon="home">
            {{ __('Dashboard') }}
        </x-nav-link>

        @can('admin.access')
            @can('categories.manage')
                <x-nav-link :href="route('admin.categories.index')" :active="request()->routeIs('admin.categories.*')" icon="tag">
                    {{ __('Kategori') }}
                </x-nav-link>
            @endcan

            @can('items.manage')
                <x-nav-link :href="route('admin.items.index')" :active="request()->routeIs('admin.items.*')" icon="cube">
                    {{ __('Barang') }}
                </x-nav-link>
            @endcan

            @can('loans.manage')
                <x-nav-link :href="route('admin.loans.index')" :active="request()->routeIs('admin.loans.*')" icon="swap">
                    {{ __('Peminjaman') }}
                </x-nav-link>
            @endcan

            @can('returns.manage')
                <x-nav-link :href="route('admin.return-logs.index')" :active="request()->routeIs('admin.return-logs.*')" icon="return">
                    {{ __('Pengembalian') }}
                </x-nav-link>
            @endcan

            @can('users.manage')
                <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*', 'admin.roles.*')" icon="users">
                    {{ __('Manajemen User') }}
                </x-nav-link>
            @elsecan('roles.manage')
                <x-nav-link :href="route('admin.roles.index')" :active="request()->routeIs('admin.roles.*')" icon="shield">
                    {{ __('Peran') }}
                </x-nav-link>
            @endcan

            @can('activity-logs.view')
                <x-nav-link :href="route('admin.activity-logs.index')" :active="request()->routeIs('admin.activity-logs.*')" icon="clock">
                    {{ __('Activity Log') }}
                </x-nav-link>
            @endcan

            @can('reports.view')
                <x-nav-link :href="route('admin.reports.loans')" :active="request()->routeIs('admin.reports.*')" icon="chart">
                    {{ __('Laporan') }}
                </x-nav-link>
            @endcan
        @elsecan('user.access')
            @can('items.browse')
                <x-nav-link :href="route('user.items.index')" :active="request()->routeIs('user.items.*')" icon="search">
                    {{ __('Daftar Barang') }}
                </x-nav-link>
            @endcan

            @can('loans.request')
                <x-nav-link :href="route('user.loans.index')" :active="request()->routeIs('user.loans.*')" icon="swap">
                    {{ __('Peminjaman Saya') }}
                </x-nav-link>
            @endcan
        @endcan
    </nav>

    <!-- Akun: profil + keluar -->
    <div class="shrink-0 border-t border-gray-200 p-3">
        <div class="flex items-center gap-1">
            <a href="{{ route('profile.edit') }}"
               class="group flex min-w-0 flex-1 items-center gap-3 rounded-md p-2 transition-colors hover:bg-gray-50 {{ request()->routeIs('profile.*') ? 'bg-gray-100' : '' }}"
               title="Profil saya">
                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-100 text-sm font-semibold text-brand-700">
                    {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(Auth::user()->name, 0, 1)) }}
                </span>
                <span class="min-w-0">
                    <span class="block truncate text-sm font-medium text-gray-900">{{ Auth::user()->name }}</span>
                    <span class="block truncate text-xs text-gray-500">{{ Auth::user()->email }}</span>
                </span>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="rounded-md p-2 text-gray-400 transition-colors hover:bg-red-50 hover:text-red-600"
                        aria-label="Keluar" title="Keluar">
                    <x-icon name="logout" class="h-5 w-5" />
                </button>
            </form>
        </div>
    </div>
</aside>