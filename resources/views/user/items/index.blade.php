<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Daftar Barang</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <x-breadcrumb :items="[
                'Dashboard' => route('user.dashboard'),
                'Daftar Barang' => null,
            ]" />

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form method="GET" action="{{ route('user.items.index') }}" class="mb-4 flex flex-wrap gap-2">
                    <input type="text" name="q" value="{{ request('q') }}" autofocus
                           placeholder="Cari nama atau kode barang..."
                           class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                    <select name="category_id"
                            class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>

                    <select name="availability"
                            class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Semua Barang</option>
                        <option value="tersedia" @selected(request('availability') === 'tersedia')>Hanya yang tersedia</option>
                    </select>

                    <x-secondary-button type="submit">Filter</x-secondary-button>

                    @if (request()->hasAny(['q', 'category_id', 'availability']))
                        <a href="{{ route('user.items.index') }}" class="self-center text-sm text-gray-600 hover:text-gray-900">Reset</a>
                    @endif
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">No</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Kode</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Nama Barang</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Kategori</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Stok</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Kondisi</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Ketersediaan</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($items as $item)
                                @php
                                    $canBorrow = $item->stock > 0 && $item->condition !== 'rusak berat';
                                    $badge = match ($item->condition) {
                                        'baik' => 'bg-green-100 text-green-800',
                                        'rusak ringan' => 'bg-yellow-100 text-yellow-800',
                                        default => 'bg-red-100 text-red-800',
                                    };
                                @endphp
                                <tr>
                                    <td class="px-4 py-3">{{ $items->firstItem() + $loop->index }}</td>
                                    <td class="px-4 py-3 font-mono text-xs">{{ $item->code }}</td>
                                    <td class="px-4 py-3">{{ $item->name }}</td>
                                    <td class="px-4 py-3">{{ $item->category->name }}</td>
                                    <td class="px-4 py-3">{{ number_format($item->stock, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-full px-2 py-1 text-xs font-medium {{ $badge }}">
                                            {{ ucfirst($item->condition) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($canBorrow)
                                            <span class="text-green-700">Tersedia</span>
                                        @elseif ($item->condition === 'rusak berat')
                                            <span class="text-red-600">Tidak dapat dipinjam</span>
                                        @else
                                            <span class="text-gray-500">Stok habis</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($canBorrow)
                                            <a href="{{ route('user.loans.create', ['item_id' => $item->id]) }}"
                                               class="text-indigo-600 hover:underline">Ajukan</a>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-4 py-6 text-center text-gray-500">Belum ada barang.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $items->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>