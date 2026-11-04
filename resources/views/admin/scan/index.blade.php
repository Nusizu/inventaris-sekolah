<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Pindai QR Code</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl space-y-6 sm:px-6 lg:px-8">
            <x-breadcrumb :items="[
                'Dashboard' => route('admin.dashboard'),
                'Pindai QR Code' => null,
            ]" />

            @include('scan._scanner', ['scanUrl' => route('admin.scan')])

            @if ($code)
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="mb-4 text-sm font-semibold text-indigo-700">Hasil Pindai</h3>

                    @if (! $item)
                        <p class="text-sm text-red-600">
                            Barang dengan kode <span class="font-mono font-semibold">{{ $code }}</span> tidak ditemukan.
                        </p>
                    @else
                        @php
                            $canBorrow = $item->stock > 0 && $item->condition !== 'rusak berat';
                            $badge = match ($item->condition) {
                                'baik' => 'bg-green-100 text-green-800',
                                'rusak ringan' => 'bg-yellow-100 text-yellow-800',
                                default => 'bg-red-100 text-red-800',
                            };
                        @endphp

                        <dl class="grid grid-cols-2 gap-4 text-sm sm:grid-cols-3">
                            <div class="col-span-2 sm:col-span-3">
                                <dt class="text-gray-500">Nama Barang</dt>
                                <dd class="text-lg font-semibold text-gray-900">{{ $item->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Kode</dt>
                                <dd class="font-mono text-gray-900">{{ $item->code }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Kategori</dt>
                                <dd class="text-gray-900">{{ $item->category->name }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Stok</dt>
                                <dd class="text-gray-900">{{ number_format($item->stock, 0, ',', '.') }}</dd>
                            </div>
                            <div>
                                <dt class="text-gray-500">Kondisi</dt>
                                <dd>
                                    <span class="rounded-full px-2 py-1 text-xs font-medium {{ $badge }}">
                                        {{ ucfirst($item->condition) }}
                                    </span>
                                </dd>
                            </div>
                        </dl>

                        <div class="mt-5 flex flex-wrap gap-2">
                            @if ($canBorrow)
                                <a href="{{ route('admin.loans.create', ['item_id' => $item->id]) }}"
                                   class="inline-flex items-center justify-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700">
                                    + Tambah Peminjaman
                                </a>
                            @else
                                <span class="text-sm text-gray-500">
                                    {{ $item->condition === 'rusak berat' ? 'Barang rusak berat tidak dapat dipinjam.' : 'Stok habis.' }}
                                </span>
                            @endif

                            <a href="{{ route('admin.items.edit', $item) }}"
                               class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 hover:bg-gray-50">
                                Edit Barang
                            </a>

                            @if ($pendingCount > 0)
                                <a href="{{ route('admin.loans.index', ['status' => 'menunggu', 'q' => $item->name]) }}"
                                   class="inline-flex items-center justify-center rounded-md border border-purple-300 bg-purple-50 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-purple-700 hover:bg-purple-100">
                                    {{ number_format($pendingCount, 0, ',', '.') }} pengajuan menunggu
                                </a>
                            @endif
                        </div>

                        <h4 class="mb-2 mt-6 text-sm font-semibold text-gray-700">Sedang Dipinjam</h4>

                        @if ($activeLoans->isEmpty())
                            <p class="text-sm text-gray-500">Tidak ada peminjaman yang sedang berjalan untuk barang ini.</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Peminjam</th>
                                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Jumlah</th>
                                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Jatuh Tempo</th>
                                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                                            <th class="px-4 py-3 text-left font-semibold text-gray-600">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach ($activeLoans as $loan)
                                            <tr>
                                                <td class="px-4 py-3">{{ $loan->user->name }}</td>
                                                <td class="px-4 py-3">{{ number_format($loan->quantity, 0, ',', '.') }}</td>
                                                <td class="px-4 py-3">{{ $loan->due_date->translatedFormat('j M Y') }}</td>
                                                <td class="px-4 py-3"><x-loan-status-badge :status="$loan->status" /></td>
                                                <td class="px-4 py-3">
                                                    <a href="{{ route('admin.loans.return.create', $loan) }}"
                                                       class="text-indigo-600 hover:underline">Kembalikan</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>