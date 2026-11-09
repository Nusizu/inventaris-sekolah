<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Kategori Barang</h2>
    </x-slot>

    @php
        $hasFilter = request()->anyFilled(['q', 'usage']);
    @endphp

    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <x-breadcrumb :items="[
                'Dashboard' => route('admin.dashboard'),
                'Kategori' => null,
            ]" />

            <x-flash-message />

            <div class="card p-6">
                <div class="mb-5 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <form method="GET" action="{{ route('admin.categories.index') }}" class="flex flex-1 flex-wrap items-center gap-2">
                        <div class="relative w-full sm:w-72">
                            <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                            <input type="text" name="q" value="{{ request('q') }}" autofocus
                                   placeholder="Cari nama kategori..."
                                   class="w-full rounded-md border-gray-300 bg-white pl-9 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        </div>

                        <select name="usage"
                                class="rounded-md border-gray-300 bg-white text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
                            <option value="">Semua Kategori</option>
                            <option value="terpakai" @selected(request('usage') === 'terpakai')>Memiliki barang</option>
                            <option value="kosong" @selected(request('usage') === 'kosong')>Belum ada barang</option>
                        </select>

                        <x-secondary-button type="submit">Filter</x-secondary-button>

                        @if ($hasFilter)
                            <a href="{{ route('admin.categories.index') }}"
                               class="inline-flex items-center gap-1 rounded-md px-2 py-1.5 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900">
                                <x-icon name="close" class="h-4 w-4" /> Reset
                            </a>
                        @endif
                    </form>

                    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary lg:shrink-0">
                        + Tambah Kategori
                    </a>
                </div>

                <div class="-mx-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold">No</th>
                                <th class="px-4 py-3 text-left font-semibold">Nama Kategori</th>
                                <th class="px-4 py-3 text-left font-semibold">Jumlah Barang</th>
                                <th class="px-4 py-3 text-left font-semibold">Dibuat</th>
                                <th class="px-6 py-3 text-left font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($categories as $category)
                                <tr>
                                    <td class="px-6 py-3 text-gray-500">{{ $categories->firstItem() + $loop->index }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $category->name }}</td>
                                    <td class="px-4 py-3 {{ $category->items_count > 0 ? 'text-gray-900' : 'text-gray-400' }}">{{ number_format($category->items_count, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $category->created_at->translatedFormat('j M Y') }}</td>
                                    <td class="px-6 py-3">
                                        <div class="flex items-center gap-4 whitespace-nowrap font-medium">
                                            <a href="{{ route('admin.categories.edit', $category) }}" class="text-brand-600 hover:text-brand-700 hover:underline">Edit</a>
                                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                                  onsubmit="return confirm({{ \Illuminate\Support\Js::from('Hapus kategori ' . $category->name . '?') }})">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-700 hover:underline">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-14 text-center">
                                        <x-icon name="tag" class="mx-auto h-8 w-8 text-gray-300" />
                                        @if ($hasFilter)
                                            <p class="mt-3 text-sm font-medium text-gray-900">Tidak ada kategori yang cocok</p>
                                            <p class="mt-1 text-sm text-gray-500">Coba ubah kata kunci atau filter.</p>
                                            <a href="{{ route('admin.categories.index') }}" class="mt-3 inline-block text-sm font-medium text-brand-600 hover:underline">Reset filter</a>
                                        @else
                                            <p class="mt-3 text-sm font-medium text-gray-900">Belum ada kategori</p>
                                            <p class="mt-1 text-sm text-gray-500">Kategori dipakai untuk mengelompokkan barang, mis. Alat Lab atau Elektronik.</p>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $categories->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>