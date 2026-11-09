<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Dashboard</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-gray-500">
                    Selamat datang, <span class="font-medium text-gray-900">{{ auth()->user()->name }}</span>.
                </p>

                <a href="{{ route('user.loans.create') }}" class="btn btn-primary">
                    + Ajukan Peminjaman
                </a>
            </div>

            {{-- Peringatan hanya muncul bila ada peminjaman terlambat --}}
            @if ($stats['overdue'] > 0)
                <div role="alert" class="flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <x-icon name="alert-circle" class="mt-0.5 h-5 w-5 shrink-0 text-red-600" />
                    <p class="flex-1">
                        Anda memiliki <span class="font-semibold">{{ number_format($stats['overdue'], 0, ',', '.') }}</span>
                        peminjaman yang terlambat. Mohon segera kembalikan barangnya.
                    </p>
                    <a href="{{ route('user.loans.index', ['status' => 'terlambat']) }}"
                       class="shrink-0 font-medium underline hover:text-red-900">Lihat</a>
                </div>
            @endif

            <div class="grid grid-cols-2 gap-4 lg:grid-cols-6">
                <x-stat-card class="lg:col-span-2" label="Menunggu Persetujuan"
                             :value="number_format($stats['pending'], 0, ',', '.')"
                             color="purple" :href="route('user.loans.index', ['status' => 'menunggu'])" />

                <x-stat-card class="lg:col-span-2" label="Sedang Dipinjam"
                             :value="number_format($stats['borrowed'], 0, ',', '.')"
                             color="yellow" :href="route('user.loans.index', ['status' => 'dipinjam'])" />

                <x-stat-card class="col-span-2 lg:col-span-2" label="Terlambat"
                             :value="number_format($stats['overdue'], 0, ',', '.')"
                             color="red" :href="route('user.loans.index', ['status' => 'terlambat'])" />

                <x-stat-card class="lg:col-span-3" label="Sudah Dikembalikan"
                             :value="number_format($stats['returned'], 0, ',', '.')"
                             color="green" :href="route('user.loans.index', ['status' => 'dikembalikan'])" />

                <x-stat-card class="lg:col-span-3" label="Total Pengajuan"
                             :value="number_format($stats['total'], 0, ',', '.')"
                             color="blue" :href="route('user.loans.index')" />
            </div>

            <div class="card">
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <h3 class="text-base font-semibold text-gray-900">Peminjaman Terbaru</h3>
                    <a href="{{ route('user.loans.index') }}" class="text-sm font-medium text-brand-600 hover:text-brand-700">Lihat semua &rarr;</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold">Barang</th>
                                <th class="px-6 py-3 text-left font-semibold">Jumlah</th>
                                <th class="px-6 py-3 text-left font-semibold">Tanggal Pinjam</th>
                                <th class="px-6 py-3 text-left font-semibold">Jatuh Tempo</th>
                                <th class="px-6 py-3 text-left font-semibold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($recentLoans as $loan)
                                <tr>
                                    <td class="px-6 py-3 font-medium text-gray-900">{{ $loan->item->name }}</td>
                                    <td class="px-6 py-3">{{ number_format($loan->quantity, 0, ',', '.') }}</td>
                                    <td class="px-6 py-3">{{ $loan->loan_date->translatedFormat('j M Y') }}</td>
                                    <td class="px-6 py-3">{{ $loan->due_date->translatedFormat('j M Y') }}</td>
                                    <td class="px-6 py-3"><x-loan-status-badge :status="$loan->status" /></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500">
                                        Belum ada riwayat peminjaman.
                                        <a href="{{ route('user.loans.create') }}" class="font-medium text-brand-600 hover:underline">Ajukan peminjaman pertama</a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>