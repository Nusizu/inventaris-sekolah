@php
    $hasFilter = $search !== '' || $categoryId !== '' || $availability !== '';
@endphp

<div class="card p-6">
    <div class="mb-5 flex flex-wrap items-center gap-2">
        <div class="relative w-full sm:w-72">
            <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
            <input type="text" wire:model.live.debounce.300ms="search" autofocus
                   placeholder="Cari nama atau kode barang..."
                   class="w-full rounded-md border-gray-300 bg-white pl-9 text-sm shadow-sm">
        </div>

        <select wire:model.live="categoryId"
                class="rounded-md border-gray-300 bg-white text-sm shadow-sm">
            <option value="">Semua Kategori</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>

        <select wire:model.live="availability"
                class="rounded-md border-gray-300 bg-white text-sm shadow-sm">
            <option value="">Semua Barang</option>
            <option value="tersedia">Hanya yang tersedia</option>
        </select>

        @if ($hasFilter)
            <button type="button" wire:click="resetFilters"
                    class="inline-flex items-center gap-1 rounded-md px-2 py-1.5 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900">
                <x-icon name="close" class="h-4 w-4" /> Reset
            </button>
        @endif

        <span wire:loading class="text-xs text-gray-400">Memuat...</span>
    </div>

    @if ($items->isEmpty())
        <div class="py-14 text-center">
            <x-icon name="search" class="mx-auto h-8 w-8 text-gray-300" />
            <p class="mt-3 text-sm font-medium text-gray-900">Tidak ada barang yang cocok</p>
            <p class="mt-1 text-sm text-gray-500">Coba ubah kata kunci atau filter.</p>
            @if ($hasFilter)
                <button type="button" wire:click="resetFilters"
                        class="mt-3 text-sm font-medium text-brand-600 hover:underline">Reset filter</button>
            @endif
        </div>
    @else
        {{-- HP: daftar kartu (tabel terlalu lebar untuk layar kecil) --}}
        <ul class="divide-y divide-gray-100 md:hidden">
            @foreach ($items as $item)
                @php
                    $canBorrow = $item->stock > 0 && $item->condition !== 'rusak berat';
                @endphp
                <li wire:key="item-card-{{ $item->id }}" class="py-4 first:pt-0 last:pb-0">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="truncate font-medium text-gray-900">{{ $item->name }}</p>
                            <p class="mt-0.5 text-xs text-gray-500">
                                <span class="font-mono">{{ $item->code }}</span> &middot; {{ $item->category->name }}
                            </p>
                        </div>
                        <x-condition-badge :condition="$item->condition" />
                    </div>

                    <div class="mt-3 flex items-center justify-between gap-3">
                        <p class="text-sm">
                            <span class="text-gray-500">Stok</span>
                            <span class="font-medium tabular-nums {{ $item->stock > 0 ? 'text-gray-900' : 'text-red-600' }}">{{ number_format($item->stock, 0, ',', '.') }}</span>
                            <span class="mx-1 text-gray-300">&middot;</span>
                            @if ($canBorrow)
                                <span class="font-medium text-emerald-700">Tersedia</span>
                            @elseif ($item->condition === 'rusak berat')
                                <span class="font-medium text-red-600">Tidak dapat dipinjam</span>
                            @else
                                <span class="text-gray-500">Stok habis</span>
                            @endif
                        </p>

                        @if ($canBorrow)
                            <a href="{{ route('user.loans.create', ['item_id' => $item->id]) }}" class="btn btn-primary px-3 py-1.5">Ajukan</a>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>

        {{-- Layar sedang ke atas: tabel --}}
        <div class="-mx-6 hidden overflow-x-auto md:block">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left font-semibold">No</th>
                        <th class="px-4 py-3 text-left font-semibold">Kode</th>
                        <th class="px-4 py-3 text-left font-semibold">Nama Barang</th>
                        <th class="px-4 py-3 text-left font-semibold">Kategori</th>
                        <th class="px-4 py-3 text-left font-semibold">Stok</th>
                        <th class="px-4 py-3 text-left font-semibold">Kondisi</th>
                        <th class="px-4 py-3 text-left font-semibold">Ketersediaan</th>
                        <th class="px-6 py-3 text-left font-semibold">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($items as $item)
                        @php
                            $canBorrow = $item->stock > 0 && $item->condition !== 'rusak berat';
                        @endphp
                        <tr wire:key="item-{{ $item->id }}">
                            <td class="px-6 py-3 text-gray-500">{{ $items->firstItem() + $loop->index }}</td>
                            <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $item->code }}</td>
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $item->name }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $item->category->name }}</td>
                            <td class="px-4 py-3 {{ $item->stock > 0 ? 'text-gray-900' : 'font-medium text-red-600' }}">
                                {{ number_format($item->stock, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3"><x-condition-badge :condition="$item->condition" /></td>
                            <td class="px-4 py-3">
                                @if ($canBorrow)
                                    <span class="font-medium text-emerald-700">Tersedia</span>
                                @elseif ($item->condition === 'rusak berat')
                                    <span class="font-medium text-red-600">Tidak dapat dipinjam</span>
                                @else
                                    <span class="text-gray-500">Stok habis</span>
                                @endif
                            </td>
                            <td class="px-6 py-3">
                                @if ($canBorrow)
                                    <a href="{{ route('user.loans.create', ['item_id' => $item->id]) }}"
                                       class="font-medium text-brand-600 hover:text-brand-700 hover:underline">Ajukan</a>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <div class="mt-4">
        {{ $items->links() }}
    </div>
</div>