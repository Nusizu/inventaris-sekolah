<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Dashboard Admin</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <p class="text-sm text-gray-500">
                Selamat datang, <span class="font-medium text-gray-900">{{ auth()->user()->name }}</span>.
                Berikut ringkasan inventaris sekolah.
            </p>

            {{-- Baris 1: yang perlu perhatian (3 kartu). Baris 2: total inventaris (2 kartu). --}}
            <div class="grid grid-cols-2 gap-4 lg:grid-cols-6">
                <x-stat-card class="lg:col-span-2" label="Menunggu Persetujuan"
                             :value="number_format($stats['pending'], 0, ',', '.')"
                             color="purple" :href="route('admin.loans.index', ['status' => 'menunggu'])" />

                <x-stat-card class="lg:col-span-2" label="Sedang Dipinjam"
                             :value="number_format($stats['borrowed'], 0, ',', '.')"
                             color="yellow" :href="route('admin.loans.index', ['status' => 'dipinjam'])" />

                <x-stat-card class="col-span-2 lg:col-span-2" label="Terlambat"
                             :value="number_format($stats['overdue'], 0, ',', '.')"
                             color="red" :href="route('admin.loans.index', ['status' => 'terlambat'])" />

                <x-stat-card class="lg:col-span-3" label="Total Barang"
                             :value="number_format($stats['total_items'], 0, ',', '.')"
                             color="blue" :href="route('admin.items.index')" />

                <x-stat-card class="lg:col-span-3" label="Total Stok"
                             :value="number_format($stats['total_stock'], 0, ',', '.')"
                             color="green" :href="route('admin.items.index')" />
            </div>

            <div class="card p-6">
                <div class="mb-4 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">Tren Peminjaman per Bulan</h3>
                        <p class="mt-1 text-xs text-gray-500">
                            Berdasarkan tanggal pinjam. Pengajuan yang menunggu, ditolak, atau dibatalkan tidak dihitung.
                        </p>
                    </div>

                    {{-- Kelas bg-gray-800 / bg-white pada tombol ini diatur ulang oleh loan-trend-chart.js --}}
                    <div class="flex flex-wrap gap-2">
                        <div class="inline-flex overflow-hidden rounded-md border border-gray-300 text-xs font-medium">
                            <button type="button" data-chart-range="6"
                                    class="bg-white px-3 py-1.5 text-gray-700">6 bulan</button>
                            <button type="button" data-chart-range="12"
                                    class="border-l border-gray-300 bg-white px-3 py-1.5 text-gray-700">12 bulan</button>
                        </div>

                        <div class="inline-flex overflow-hidden rounded-md border border-gray-300 text-xs font-medium">
                            <button type="button" data-chart-metric="count"
                                    class="bg-white px-3 py-1.5 text-gray-700">Jumlah peminjaman</button>
                            <button type="button" data-chart-metric="units"
                                    class="border-l border-gray-300 bg-white px-3 py-1.5 text-gray-700">Jumlah unit</button>
                        </div>
                    </div>
                </div>

                @if ($hasTrendData)
                    <div class="relative h-72">
                        <canvas id="loanTrendChart" data-trend='@json($trend)'></canvas>
                    </div>
                @else
                    <p class="py-10 text-center text-sm text-gray-500">Belum ada data peminjaman untuk ditampilkan.</p>
                @endif
            </div>

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="card">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h3 class="text-base font-semibold text-gray-900">Barang Paling Sering Dipinjam</h3>
                    </div>

                    @php $maxItem = max(1, (int) $topItems->max('loans_count')); @endphp

                    <ul class="divide-y divide-gray-100">
                        @forelse ($topItems as $item)
                            <li class="px-6 py-3">
                                <div class="flex items-center justify-between gap-3 text-sm">
                                    <span class="flex min-w-0 items-center gap-3">
                                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-gray-100 text-xs font-medium text-gray-600">{{ $loop->iteration }}</span>
                                        <span class="truncate text-gray-900">{{ $item->name }}</span>
                                    </span>
                                    <span class="shrink-0 font-medium tabular-nums text-gray-900">{{ number_format($item->loans_count, 0, ',', '.') }}<span class="font-normal text-gray-400">x</span></span>
                                </div>
                                <div class="mt-2 h-1 overflow-hidden rounded-full bg-gray-100">
                                    <div class="h-full rounded-full bg-brand-500" style="width: {{ round($item->loans_count / $maxItem * 100) }}%"></div>
                                </div>
                            </li>
                        @empty
                            <li class="px-6 py-8 text-center text-sm text-gray-500">Belum ada data peminjaman.</li>
                        @endforelse
                    </ul>
                </div>

                <div class="card">
                    <div class="border-b border-gray-100 px-6 py-4">
                        <h3 class="text-base font-semibold text-gray-900">User Paling Aktif</h3>
                    </div>

                    @php $maxUser = max(1, (int) $activeUsers->max('loans_count')); @endphp

                    <ul class="divide-y divide-gray-100">
                        @forelse ($activeUsers as $user)
                            <li class="px-6 py-3">
                                <div class="flex items-center justify-between gap-3 text-sm">
                                    <span class="flex min-w-0 items-center gap-3">
                                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-gray-100 text-xs font-medium text-gray-600">{{ $loop->iteration }}</span>
                                        <span class="truncate text-gray-900">{{ $user->name }}</span>
                                    </span>
                                    <span class="shrink-0 font-medium tabular-nums text-gray-900">{{ number_format($user->loans_count, 0, ',', '.') }}<span class="font-normal text-gray-400">x</span></span>
                                </div>
                                <div class="mt-2 h-1 overflow-hidden rounded-full bg-gray-100">
                                    <div class="h-full rounded-full bg-brand-500" style="width: {{ round($user->loans_count / $maxUser * 100) }}%"></div>
                                </div>
                            </li>
                        @empty
                            <li class="px-6 py-8 text-center text-sm text-gray-500">Belum ada data peminjaman.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>