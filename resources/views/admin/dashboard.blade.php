<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Dashboard Admin</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <x-breadcrumb :items="['Dashboard' => null]" />

            <p class="text-sm text-gray-600">Selamat datang, {{ auth()->user()->name }}.</p>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <x-stat-card label="Total Barang"
                             :value="number_format($stats['total_items'], 0, ',', '.')"
                             color="blue" :href="route('admin.items.index')" />

                <x-stat-card label="Total Stok"
                             :value="number_format($stats['total_stock'], 0, ',', '.')"
                             color="green" :href="route('admin.items.index')" />

                <x-stat-card label="Sedang Dipinjam"
                             :value="number_format($stats['borrowed'], 0, ',', '.')"
                             color="yellow" :href="route('admin.loans.index', ['status' => 'dipinjam'])" />

                <x-stat-card label="Terlambat"
                             :value="number_format($stats['overdue'], 0, ',', '.')"
                             color="red" :href="route('admin.loans.index', ['status' => 'terlambat'])" />
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h3 class="text-sm font-semibold text-indigo-700">Barang Paling Sering Dipinjam</h3>
                    </div>
                    <ul class="divide-y divide-gray-100">
                        @forelse ($topItems as $item)
                            <li class="flex items-center justify-between px-6 py-3 text-sm">
                                <span>{{ $item->name }}</span>
                                <span class="rounded-full bg-indigo-100 px-2 py-1 text-xs font-medium text-indigo-800">
                                    {{ number_format($item->loans_count, 0, ',', '.') }}x
                                </span>
                            </li>
                        @empty
                            <li class="px-6 py-4 text-sm text-gray-500">Belum ada data peminjaman.</li>
                        @endforelse
                    </ul>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h3 class="text-sm font-semibold text-indigo-700">User Paling Aktif</h3>
                    </div>
                    <ul class="divide-y divide-gray-100">
                        @forelse ($activeUsers as $user)
                            <li class="flex items-center justify-between px-6 py-3 text-sm">
                                <span>{{ $user->name }}</span>
                                <span class="rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800">
                                    {{ number_format($user->loans_count, 0, ',', '.') }}x
                                </span>
                            </li>
                        @empty
                            <li class="px-6 py-4 text-sm text-gray-500">Belum ada data peminjaman.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>