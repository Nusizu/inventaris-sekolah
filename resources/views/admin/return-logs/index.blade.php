<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Data Pengembalian</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <x-breadcrumb :items="[
                'Dashboard' => route('admin.dashboard'),
                'Pengembalian' => null,
            ]" />

            <x-flash-message />

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <div class="mb-4">
                    <form method="GET" action="{{ route('admin.return-logs.index') }}" class="flex flex-wrap gap-2">
                        <input type="text" name="q" value="{{ request('q') }}" autofocus
                               placeholder="Cari peminjam atau barang..."
                               class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

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

                        @if (request()->hasAny(['q', 'condition']))
                            <a href="{{ route('admin.return-logs.index') }}" class="self-center text-sm text-gray-600 hover:text-gray-900">Reset</a>
                        @endif
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">No</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Peminjam</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Barang</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Jumlah</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Jatuh Tempo</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Tanggal Kembali</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Ketepatan</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Kondisi</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($returnLogs as $log)
                                @php
                                    $badge = match ($log->condition_on_return) {
                                        'baik' => 'bg-green-100 text-green-800',
                                        'rusak ringan' => 'bg-yellow-100 text-yellow-800',
                                        default => 'bg-red-100 text-red-800',
                                    };
                                    $isLate = $log->returned_at->gt($log->loan->due_date);
                                    $lateDays = (int) $log->loan->due_date->diffInDays($log->returned_at, true);
                                @endphp
                                <tr>
                                    <td class="px-4 py-3">{{ $returnLogs->firstItem() + $loop->index }}</td>
                                    <td class="px-4 py-3">{{ $log->loan->user->name }}</td>
                                    <td class="px-4 py-3">{{ $log->loan->item->name }}</td>
                                    <td class="px-4 py-3">{{ number_format($log->loan->quantity, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3">{{ $log->loan->due_date->translatedFormat('j M Y') }}</td>
                                    <td class="px-4 py-3">{{ $log->returned_at->translatedFormat('j M Y') }}</td>
                                    <td class="px-4 py-3">
                                        @if ($isLate)
                                            <span class="text-red-600">Terlambat {{ number_format($lateDays, 0, ',', '.') }} hari</span>
                                        @else
                                            <span class="text-green-700">Tepat waktu</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-full px-2 py-1 text-xs font-medium {{ $badge }}">
                                            {{ ucfirst($log->condition_on_return) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">{{ $log->notes ? \Illuminate\Support\Str::limit($log->notes, 40) : '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-6 text-center text-gray-500">Belum ada data pengembalian.</td>
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