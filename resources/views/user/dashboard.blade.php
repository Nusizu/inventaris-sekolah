<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Dashboard</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <x-breadcrumb :items="['Dashboard' => null]" />

            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-gray-600">Selamat datang, {{ auth()->user()->name }}.</p>

                <a href="{{ route('user.loans.create') }}"
                   class="inline-flex items-center justify-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700">
                    + Ajukan Peminjaman
                </a>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <x-stat-card label="Sedang Dipinjam"
                             :value="number_format($stats['borrowed'], 0, ',', '.')"
                             color="yellow" :href="route('user.loans.index', ['status' => 'dipinjam'])" />

                <x-stat-card label="Terlambat"
                             :value="number_format($stats['overdue'], 0, ',', '.')"
                             color="red" :href="route('user.loans.index', ['status' => 'terlambat'])" />

                <x-stat-card label="Sudah Dikembalikan"
                             :value="number_format($stats['returned'], 0, ',', '.')"
                             color="green" :href="route('user.loans.index', ['status' => 'dikembalikan'])" />

                <x-stat-card label="Total Peminjaman"
                             :value="number_format($stats['total'], 0, ',', '.')"
                             color="blue" :href="route('user.loans.index')" />
            </div>

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <h3 class="text-sm font-semibold text-indigo-700">Peminjaman Terbaru</h3>
                    <a href="{{ route('user.loans.index') }}" class="text-xs text-gray-500 hover:text-indigo-600">Lihat semua &rarr;</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600">Barang</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600">Jumlah</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600">Tanggal Pinjam</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600">Jatuh Tempo</th>
                                <th class="px-6 py-3 text-left font-semibold text-gray-600">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($recentLoans as $loan)
                                <tr>
                                    <td class="px-6 py-3">{{ $loan->item->name }}</td>
                                    <td class="px-6 py-3">{{ number_format($loan->quantity, 0, ',', '.') }}</td>
                                    <td class="px-6 py-3">{{ $loan->loan_date->translatedFormat('j M Y') }}</td>
                                    <td class="px-6 py-3">{{ $loan->due_date->translatedFormat('j M Y') }}</td>
                                    <td class="px-6 py-3"><x-loan-status-badge :status="$loan->status" /></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-6 text-center text-gray-500">Belum ada riwayat peminjaman.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>