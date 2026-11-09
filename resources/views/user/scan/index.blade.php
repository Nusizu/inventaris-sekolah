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

                        @include('scan._item', ['item' => $item, 'showAvailability' => true])

                        @if ($canBorrow)
                            <div class="mt-5">
                                <a href="{{ route('user.loans.create', ['item_id' => $item->id]) }}" class="btn btn-primary w-full sm:w-auto">
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