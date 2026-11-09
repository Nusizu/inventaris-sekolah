<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Data Pengembalian</h2>
    </x-slot>

    @php
        $hasFilter = request()->anyFilled(['q', 'condition']);
    @endphp

    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <x-breadcrumb :items="[
                'Dashboard' => route('admin.dashboard'),
                'Pengembalian' => null,
            ]" />

            <x-flash-message />

            <div class="card p-6">
                <form method="GET" action="{{ route('admin.return-logs.index') }}" class="mb-5 flex flex-wrap items-center gap-2">
                    <div class="relative w-full sm:w-72">
                        <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                        <input type="text" name="q" value="{{ request('q') }}" autofocus
                               placeholder="Cari peminjam atau barang..."
                               class="w-full rounded-md border-gray-300 bg-white pl-9 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>

                    <select name="condition"
                            class="rounded-md border-gray-300 bg-white text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
                        <option value="">Semua Kondisi</option>
                        @foreach (\App\Models\Item::CONDITIONS as $condition)
                            <option value="{{ $condition }}" @selected(request('condition') === $condition)>
                                {{ ucfirst($condition) }}
                            </option>
                        @endforeach
                    </select>

                    <x-secondary-button type="submit">Filter</x-secondary-button>

                    @if ($hasFilter)
                        <a href="{{ route('admin.return-logs.index') }}"
                           class="inline-flex items-center gap-1 rounded-md px-2 py-1.5 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900">
                            <x-icon name="close" class="h-4 w-4" /> Reset
                        </a>
                    @endif
                </form>

                <div class="-mx-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold">No</th>
                                <th class="px-4 py-3 text-left font-semibold">Peminjam</th>
                                <th class="px-4 py-3 text-left font-semibold">Barang</th>
                                <th class="px-4 py-3 text-left font-semibold">Jumlah</th>
                                <th class="px-4 py-3 text-left font-semibold">Jatuh Tempo</th>
                                <th class="px-4 py-3 text-left font-semibold">Tanggal Kembali</th>
                                <th class="px-4 py-3 text-left font-semibold">Ketepatan</th>
                                <th class="px-4 py-3 text-left font-semibold">Kondisi</th>
                                <th class="px-6 py-3 text-left font-semibold">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($returnLogs as $log)
                                @php
                                    $isLate = $log->returned_at->gt($log->loan->due_date);
                                    $lateDays = (int) $log->loan->due_date->diffInDays($log->returned_at, true);
                                @endphp
                                <tr>
                                    <td class="px-6 py-3 text-gray-500">{{ $returnLogs->firstItem() + $loop->index }}</td>
                                    <td class="whitespace-nowrap px-4 py-3 font-medium text-gray-900">{{ $log->loan->user->name }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $log->loan->item->name }}</td>
                                    <td class="px-4 py-3">{{ number_format($log->loan->quantity, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $log->loan->due_date->translatedFormat('j M Y') }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $log->returned_at->translatedFormat('j M Y') }}</td>
                                    <td class="whitespace-nowrap px-4 py-3">
                                        @if ($isLate)
                                            <span class="font-medium text-red-600">Terlambat {{ number_format($lateDays, 0, ',', '.') }} hari</span>
                                        @else
                                            <span class="font-medium text-emerald-700">Tepat waktu</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3"><x-condition-badge :condition="$log->condition_on_return" /></td>
                                    <td class="px-6 py-3 text-gray-600">{{ $log->notes ? \Illuminate\Support\Str::limit($log->notes, 40) : '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-14 text-center">
                                        <x-icon name="return" class="mx-auto h-8 w-8 text-gray-300" />
                                        @if ($hasFilter)
                                            <p class="mt-3 text-sm font-medium text-gray-900">Tidak ada data yang cocok</p>
                                            <p class="mt-1 text-sm text-gray-500">Coba ubah kata kunci atau kondisi.</p>
                                            <a href="{{ route('admin.return-logs.index') }}" class="mt-3 inline-block text-sm font-medium text-brand-600 hover:underline">Reset filter</a>
                                        @else
                                            <p class="mt-3 text-sm font-medium text-gray-900">Belum ada data pengembalian</p>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $returnLogs->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>