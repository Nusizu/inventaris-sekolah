<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Kategori Barang</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <x-breadcrumb :items="[
                'Dashboard' => route('admin.dashboard'),
                'Kategori' => null,
            ]" />

            <x-flash-message />

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <form method="GET" action="{{ route('admin.categories.index') }}" class="flex gap-2">
                        <input
                            type="text"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Cari nama kategori..."
                            autofocus
                            class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                        >
                        <x-secondary-button type="submit">Cari</x-secondary-button>
                        @if (request('q'))
                            <a href="{{ route('admin.categories.index') }}" class="self-center text-sm text-gray-600 hover:text-gray-900">Reset</a>
                        @endif
                    </form>

                    <a href="{{ route('admin.categories.create') }}"
                       class="inline-flex items-center justify-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700">
                        + Tambah Kategori
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">No</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Nama Kategori</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Jumlah Barang</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Dibuat</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($categories as $category)
                                <tr>
                                    <td class="px-4 py-3">{{ $categories->firstItem() + $loop->index }}</td>
                                    <td class="px-4 py-3">{{ $category->name }}</td>
                                    <td class="px-4 py-3">{{ number_format($category->items_count, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3">{{ $category->created_at->translatedFormat('j M Y') }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <a href="{{ route('admin.categories.edit', $category) }}" class="text-indigo-600 hover:underline">Edit</a>
                                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}"
                                                  onsubmit="return confirm('Hapus kategori {{ $category->name }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-gray-500">Belum ada kategori.</td>
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