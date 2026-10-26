<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Peminjaman Saya</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <x-breadcrumb :items="[
                'Dashboard' => route('user.dashboard'),
                'Peminjaman Saya' => null,
            ]" />

            <x-flash-message />

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <div class="mb-4 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <form method="GET" action="{{ route('user.loans.index') }}" class="flex flex-wrap gap-2">
                        <input type="text" name="q" value="{{ request('q') }}" autofocus
                               placeholder="Cari nama barang..."
                               class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                        <select name="status"
                                class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua Status</option>
                            @foreach (['dipinjam', 'dikembalikan', 'terlambat'] as $status)
                                <option value="{{ $status }}" @selected(request('status') === $status)>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>

                        <x-secondary-button type="submit">Filter</x-secondary-button>

                        @if (request()->hasAny(['q', 'status']))
                            <a href="{{ route('user.loans.index') }}" class="self-center text-sm text-gray-600 hover:text-gray-900">Reset</a>
                        @endif
                    </form>

                    <a href="{{ route('user.loans.create') }}"
                       class="inline-flex items-center justify-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700">
                        + Ajukan Peminjaman
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">No</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Barang</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Jumlah</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Tanggal Pinjam</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Jatuh Tempo</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($loans as $loan)
                                <tr>
                                    <td class="px-4 py-3">{{ $loans->firstItem() + $loop->index }}</td>
                                    <td class="px-4 py-3">{{ $loan->item->name }}</td>
                                    <td class="px-4 py-3">{{ number_format($loan->quantity, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3">{{ $loan->loan_date->translatedFormat('j M Y') }}</td>
                                    <td class="px-4 py-3">{{ $loan->due_date->translatedFormat('j M Y') }}</td>
                                    <td class="px-4 py-3"><x-loan-status-badge :status="$loan->status" /></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">Belum ada riwayat peminjaman.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $loans->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>