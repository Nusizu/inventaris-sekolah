{{--
    Ringkasan barang hasil pindai (dipakai halaman Pindai admin & user).
    Pemakaian: @include('scan._item', ['item' => $item])
               @include('scan._item', ['item' => $item, 'showAvailability' => true])
--}}
@php
    $canBorrow = $item->stock > 0 && $item->condition !== 'rusak berat';
@endphp

<div class="flex items-start justify-between gap-4">
    <div class="min-w-0">
        <p class="text-lg font-semibold text-gray-900">{{ $item->name }}</p>
        <p class="mt-0.5 font-mono text-xs text-gray-500">{{ $item->code }}</p>
    </div>
    <x-condition-badge :condition="$item->condition" />
</div>

<dl class="mt-5 grid grid-cols-2 gap-4 border-t border-gray-100 pt-5 text-sm sm:grid-cols-3">
    <div>
        <dt class="text-gray-500">Kategori</dt>
        <dd class="mt-0.5 font-medium text-gray-900">{{ $item->category->name }}</dd>
    </div>
    <div>
        <dt class="text-gray-500">Stok</dt>
        <dd class="mt-0.5 font-medium {{ $item->stock > 0 ? 'text-gray-900' : 'text-red-600' }}">{{ number_format($item->stock, 0, ',', '.') }}</dd>
    </div>

    @if ($showAvailability ?? false)
        <div>
            <dt class="text-gray-500">Ketersediaan</dt>
            <dd class="mt-0.5 font-medium">
                @if ($canBorrow)
                    <span class="text-emerald-700">Tersedia</span>
                @elseif ($item->condition === 'rusak berat')
                    <span class="text-red-600">Tidak dapat dipinjam</span>
                @else
                    <span class="text-gray-500">Stok habis</span>
                @endif
            </dd>
        </div>
    @endif
</dl>