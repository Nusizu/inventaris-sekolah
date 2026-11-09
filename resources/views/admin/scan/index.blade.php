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
                <div class="card p-6">
                    <h3 class="mb-4 text-base font-semibold text-gray-900">Hasil Pindai</h3>

                    @if (! $item)
                        <div role="alert" class="flex items-start gap-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                            <x-icon name="alert-circle" class="mt-0.5 h-5 w-5 shrink-0 text-red-600" />
                            <p>Barang dengan kode <span class="font-mono font-semibold">{{ $code }}</span> tidak ditemukan.</p>
                        </div>
                    @else
                        @php
                            $canBorrow = $item->stock > 0 && $item->condition !== 'rusak berat';
                        @endphp

                        @include('scan._item', ['item' => $item])

                        <div class="mt-5 flex flex-wrap items-center gap-2">
                            @if ($canBorrow)
                                <a href="{{ route('admin.loans.create', ['item_id' => $item->id]) }}" class="btn btn-primary">
                                    + Tambah Peminjaman
                                </a>
                            @else
                                <span class="text-sm text-gray-500">
                                    {{ $item->condition === 'rusak berat' ? 'Barang rusak berat tidak dapat dipinjam.' : 'Stok habis.' }}
                                </span>
                            @endif

                            <a href="{{ route('admin.items.edit', $item) }}" class="btn btn-secondary">
                                Edit Barang
                            </a>

                            @if ($pendingCount > 0)
                                <a href="{{ route('admin.loans.index', ['status' => 'menunggu', 'q' => $item->name]) }}"
                                   class="btn border border-violet-200 bg-violet-50 text-violet-700 hover:bg-violet-100">
                                    {{ number_format($pendingCount, 0, ',', '.') }} pengajuan menunggu
                                </a>
                            @endif
                        </div>

                        <h4 class="mb-3 mt-8 text-sm font-semibold text-gray-900">Sedang Dipinjam</h4>

                        @if ($activeLoans->isEmpty())
                            <p class="rounded-lg bg-gray-50 px-4 py-3 text-sm text-gray-500">Tidak ada peminjaman yang sedang berjalan untuk barang ini.</p>
                        @else
                            <div class="-mx-6 overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 text-sm">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left font-semibold">Peminjam</th>
                                            <th class="px-4 py-3 text-left font-semibold">Jumlah</th>
                                            <th class="px-4 py-3 text-left font-semibold">Jatuh Tempo</th>
                                            <th class="px-4 py-3 text-left font-semibold">Status</th>
                                            <th class="px-6 py-3 text-left font-semibold">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach ($activeLoans as $loan)
                                            <tr>
                                                <td class="whitespace-nowrap px-6 py-3 font-medium text-gray-900">{{ $loan->user->name }}</td>
                                                <td class="px-4 py-3">{{ number_format($loan->quantity, 0, ',', '.') }}</td>
                                                <td class="whitespace-nowrap px-4 py-3 {{ $loan->status === 'terlambat' ? 'font-medium text-red-600' : 'text-gray-600' }}">{{ $loan->due_date->translatedFormat('j M Y') }}</td>
                                                <td class="px-4 py-3"><x-loan-status-badge :status="$loan->status" /></td>
                                                <td class="px-6 py-3">
                                                    <a href="{{ route('admin.loans.return.create', $loan) }}"
                                                       class="rounded-md border border-brand-600/30 bg-brand-50 px-2.5 py-1 text-xs font-medium text-brand-700 transition-colors hover:bg-brand-100">Kembalikan</a>
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