@php
    $hasFilter = $search !== '' || $categoryId !== '' || $condition !== '';
@endphp

<div class="card p-6">
    <div class="mb-5 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex flex-1 flex-wrap items-center gap-2">
            <div class="relative w-full sm:w-72">
                <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                <input type="text" wire:model.live.debounce.300ms="search" autofocus
                       placeholder="Cari nama atau kode..."
                       class="w-full rounded-md border-gray-300 bg-white pl-9 text-sm shadow-sm">
            </div>

            <select wire:model.live="categoryId"
                    class="rounded-md border-gray-300 bg-white text-sm shadow-sm">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
            </select>

            <select wire:model.live="condition"
                    class="rounded-md border-gray-300 bg-white text-sm shadow-sm">
                <option value="">Semua Kondisi</option>
                @foreach (\App\Models\Item::CONDITIONS as $conditionOption)
                    <option value="{{ $conditionOption }}">{{ ucfirst($conditionOption) }}</option>
                @endforeach
            </select>

            @if ($hasFilter)
                <button type="button" wire:click="resetFilters"
                        class="inline-flex items-center gap-1 rounded-md px-2 py-1.5 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900">
                    <x-icon name="close" class="h-4 w-4" /> Reset
                </button>
            @endif

            <span wire:loading class="text-xs text-gray-400">Memuat...</span>
        </div>

        <div class="flex flex-wrap gap-2 lg:shrink-0 lg:flex-nowrap">
            <a href="{{ route('admin.items.labels', array_filter(['q' => $search, 'category_id' => $categoryId])) }}"
               target="_blank" rel="noopener" class="btn btn-secondary">
                <x-icon name="qr-code" class="h-4 w-4" /> Cetak Label QR
            </a>
            <a href="{{ route('admin.items.create') }}" class="btn btn-primary">
                + Tambah Barang
            </a>
        </div>
    </div>

    <div class="-mx-6 overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left font-semibold">No</th>
                    <th class="px-4 py-3 text-left font-semibold">Kode</th>
                    <th class="px-4 py-3 text-left font-semibold">Nama Barang</th>
                    <th class="px-4 py-3 text-left font-semibold">Kategori</th>
                    <th class="px-4 py-3 text-left font-semibold">Stok</th>
                    <th class="px-4 py-3 text-left font-semibold">Kondisi</th>
                    <th class="px-6 py-3 text-left font-semibold">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($items as $item)
                    <tr wire:key="item-{{ $item->id }}">
                        <td class="px-6 py-3 text-gray-500">{{ $items->firstItem() + $loop->index }}</td>
                        <td class="px-4 py-3 font-mono text-xs text-gray-600">{{ $item->code }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $item->name }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $item->category->name }}</td>
                        <td class="px-4 py-3 {{ $item->stock > 0 ? 'text-gray-900' : 'font-medium text-red-600' }}">
                            {{ number_format($item->stock, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3"><x-condition-badge :condition="$item->condition" /></td>
                        <td class="px-6 py-3">
                            <div class="flex items-center gap-4 whitespace-nowrap font-medium">
                                <a href="{{ route('admin.items.labels', ['ids' => [$item->id]]) }}"
                                   target="_blank" rel="noopener"
                                   class="text-gray-500 hover:text-gray-900 hover:underline">Label QR</a>
                                <a href="{{ route('admin.items.edit', $item) }}" class="text-brand-600 hover:text-brand-700 hover:underline">Edit</a>
                                <form method="POST" action="{{ route('admin.items.destroy', $item) }}"
                                      onsubmit="return confirm({{ \Illuminate\Support\Js::from('Hapus barang ' . $item->name . '?') }})">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-700 hover:underline">Hapus</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-14 text-center">
                            <x-icon name="cube" class="mx-auto h-8 w-8 text-gray-300" />
                            @if ($hasFilter)
                                <p class="mt-3 text-sm font-medium text-gray-900">Tidak ada barang yang cocok</p>
                                <p class="mt-1 text-sm text-gray-500">Coba ubah kata kunci atau filter.</p>
                                <button type="button" wire:click="resetFilters"
                                        class="mt-3 text-sm font-medium text-brand-600 hover:underline">Reset filter</button>
                            @else
                                <p class="mt-3 text-sm font-medium text-gray-900">Belum ada barang</p>
                                <p class="mt-1 text-sm text-gray-500">Tambahkan barang pertama untuk mulai mencatat inventaris.</p>
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $items->links() }}
    </div>
</div>