<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Data Barang</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <x-breadcrumb :items="[
                'Dashboard' => route('admin.dashboard'),
                'Barang' => null,
            ]" />

            <x-flash-message />

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <div class="mb-4 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <form method="GET" action="{{ route('admin.items.index') }}" class="flex flex-wrap gap-2">
                        <input type="text" name="q" value="{{ request('q') }}" autofocus
                               placeholder="Cari nama atau kode..."
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

                        <select name="condition"
                                class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua Kondisi</option>
                            @foreach (\App\Models\Item::CONDITIONS as $condition)
                                <option value="{{ $condition }}" @selected(request('condition') === $condition)>
                                    {{ ucfirst($condition) }}
                                </option>
                            @endforeach
                        </select>

                        <x-secondary-button type="submit">Filter</x-secondary-button>

                        @if (request()->hasAny(['q', 'category_id', 'condition']))
                            <a href="{{ route('admin.items.index') }}" class="self-center text-sm text-gray-600 hover:text-gray-900">Reset</a>
                        @endif
                    </form>

                    <a href="{{ route('admin.items.create') }}"
                       class="inline-flex items-center justify-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700">
                        + Tambah Barang
                    </a>
                </div>

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
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($items as $item)
                                @php
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
                                        <div class="flex items-center gap-3">
                                            <a href="{{ route('admin.items.edit', $item) }}" class="text-indigo-600 hover:underline">Edit</a>
                                            <form method="POST" action="{{ route('admin.items.destroy', $item) }}"
                                                  onsubmit="return confirm({{ \Illuminate\Support\Js::from('Hapus barang ' . $item->name . '?') }})">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-6 text-center text-gray-500">Belum ada barang.</td>
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