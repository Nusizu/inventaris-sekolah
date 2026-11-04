<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Pindai QR Code</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl space-y-6 sm:px-6 lg:px-8">
            <x-breadcrumb :items="[
                'Dashboard' => route('user.dashboard'),
                'Pindai QR Code' => null,
            ]" />

            @include('scan._scanner', ['scanUrl' => route('user.scan')])

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
                            <div>
                                <dt class="text-gray-500">Ketersediaan</dt>
                                <dd>
                                    @if ($canBorrow)
                                        <span class="text-green-700">Tersedia</span>
                                    @elseif ($item->condition === 'rusak berat')
                                        <span class="text-red-600">Tidak dapat dipinjam</span>
                                    @else
                                        <span class="text-gray-500">Stok habis</span>
                                    @endif
                                </dd>
                            </div>
                        </dl>

                        @if ($canBorrow)
                            <div class="mt-5">
                                <a href="{{ route('user.loans.create', ['item_id' => $item->id]) }}"
                                   class="inline-flex items-center justify-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700">
                                    Ajukan Peminjaman
                                </a>
                            </div>
                        @endif
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>