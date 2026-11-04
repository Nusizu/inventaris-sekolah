<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Laporan Peminjaman</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <x-breadcrumb :items="[
                'Dashboard' => route('admin.dashboard'),
                'Laporan Peminjaman' => null,
            ]" />

            <x-flash-message />

            <div class="space-y-6">
                {{-- Filter --}}
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <form method="GET" action="{{ route('admin.reports.loans') }}" class="flex flex-wrap items-start gap-3">
                        <div>
                            <label for="date_from" class="block text-xs text-gray-500">Dari tanggal pinjam</label>
                            <input type="text" id="date_from" name="date_from" value="{{ request('date_from') }}"
                                   placeholder="Pilih tanggal" autofocus class="js-date-filter">
                            <x-input-error :messages="$errors->get('date_from')" class="mt-1" />
                        </div>

                        <div>
                            <label for="date_to" class="block text-xs text-gray-500">Sampai tanggal pinjam</label>
                            <input type="text" id="date_to" name="date_to" value="{{ request('date_to') }}"
                                   placeholder="Pilih tanggal" class="js-date-filter">
                            <x-input-error :messages="$errors->get('date_to')" class="mt-1" />
                        </div>

                        <div>
                            <label for="status" class="block text-xs text-gray-500">Status</label>
                            <select id="status" name="status"
                                    class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Semua Status</option>
                                @foreach (\App\Models\Loan::STATUSES as $status)
                                    <option value="{{ $status }}" @selected(request('status') === $status)>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-1" />
                        </div>

                        <div class="flex items-center gap-2 self-end">
                            <x-secondary-button type="submit">Tampilkan</x-secondary-button>

                            @if (request()->hasAny(['date_from', 'date_to', 'status']))
                                <a href="{{ route('admin.reports.loans') }}"
                                   class="text-sm text-gray-600 hover:text-gray-900">Reset</a>
                            @endif
                        </div>
                    </form>
                </div>

                {{-- Ringkasan dan tombol unduh --}}
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-gray-600">
                        Laporan ini mengikuti filter di atas. Tanggal yang dipakai adalah tanggal pinjam.
                    </p>

                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('admin.reports.loans.pdf', request()->query()) }}"
                           class="inline-flex items-center justify-center rounded-md bg-red-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-red-500">
                            Unduh PDF
                        </a>
                        <a href="{{ route('admin.reports.loans.excel', request()->query()) }}"
                           class="inline-flex items-center justify-center rounded-md bg-green-700 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-green-600">
                            Unduh Excel
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <x-stat-card label="Total Peminjaman"
                                 :value="number_format($totalLoans, 0, ',', '.')" color="blue" />

                    <x-stat-card label="Total Unit Barang"
                                 :value="number_format($totalUnits, 0, ',', '.')" color="green" />

                    <div class="rounded-lg bg-white p-5 shadow-sm sm:col-span-2">
                        <p class="text-xs font-semibold uppercase tracking-wide text-gray-500">Per Status</p>
                        <div class="mt-3 flex flex-wrap gap-x-5 gap-y-2 text-sm">
                            @foreach (\App\Models\Loan::STATUSES as $status)
                                <span class="inline-flex items-center gap-1.5">
                                    <x-loan-status-badge :status="$status" />
                                    <span class="font-semibold text-gray-800">
                                        {{ number_format($summary->get($status)?->total ?? 0, 0, ',', '.') }}
                                    </span>
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Pratinjau tabel --}}
                <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                    <h3 class="mb-4 text-sm font-semibold text-indigo-700">Pratinjau Data</h3>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">No</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Peminjam</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Barang</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Kategori</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Jumlah</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Tanggal Pinjam</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Jatuh Tempo</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Tanggal Kembali</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-600">Kondisi Kembali</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($loans as $loan)
                                    <tr>
                                        <td class="px-4 py-3">{{ $loans->firstItem() + $loop->index }}</td>
                                        <td class="px-4 py-3">{{ $loan->user->name }}</td>
                                        <td class="px-4 py-3">
                                            {{ $loan->item->name }}
                                            <span class="block font-mono text-xs text-gray-400">{{ $loan->item->code }}</span>
                                        </td>
                                        <td class="px-4 py-3">{{ $loan->item->category->name }}</td>
                                        <td class="px-4 py-3">{{ number_format($loan->quantity, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3">{{ $loan->loan_date->translatedFormat('j M Y') }}</td>
                                        <td class="px-4 py-3">{{ $loan->due_date->translatedFormat('j M Y') }}</td>
                                        <td class="px-4 py-3">
                                            {{ $loan->returnLog ? $loan->returnLog->returned_at->translatedFormat('j M Y') : '-' }}
                                        </td>
                                        <td class="px-4 py-3"><x-loan-status-badge :status="$loan->status" /></td>
                                        <td class="px-4 py-3">
                                            {{ $loan->returnLog ? ucfirst($loan->returnLog->condition_on_return) : '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="px-4 py-6 text-center text-gray-500">
                                            Tidak ada data peminjaman untuk filter ini.
                                        </td>
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
    </div>
</x-app-layout>