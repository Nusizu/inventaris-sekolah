<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Laporan Peminjaman</h2>
    </x-slot>

    @php
        $hasFilter = request()->anyFilled(['date_from', 'date_to', 'status']);
    @endphp

    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <x-breadcrumb :items="[
                'Dashboard' => route('admin.dashboard'),
                'Laporan Peminjaman' => null,
            ]" />

            <x-flash-message />

            <div class="space-y-6">
                {{-- Filter. [&_input]:w-full membuat kolom tanggal (flatpickr) selebar kolomnya --}}
                <div class="card p-6">
                    <form method="GET" action="{{ route('admin.reports.loans') }}"
                          class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 lg:items-start [&_input]:w-full">
                        <div>
                            <label for="date_from" class="mb-1 block text-xs font-medium text-gray-500">Dari tanggal pinjam</label>
                            <input type="text" id="date_from" name="date_from" value="{{ request('date_from') }}"
                                   placeholder="Pilih tanggal" autofocus class="js-date-filter">
                            <x-input-error :messages="$errors->get('date_from')" class="mt-1" />
                        </div>

                        <div>
                            <label for="date_to" class="mb-1 block text-xs font-medium text-gray-500">Sampai tanggal pinjam</label>
                            <input type="text" id="date_to" name="date_to" value="{{ request('date_to') }}"
                                   placeholder="Pilih tanggal" class="js-date-filter">
                            <x-input-error :messages="$errors->get('date_to')" class="mt-1" />
                        </div>

                        <div>
                            <label for="status" class="mb-1 block text-xs font-medium text-gray-500">Status</label>
                            <select id="status" name="status"
                                    class="w-full rounded-md border-gray-300 bg-white text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
                                <option value="">Semua Status</option>
                                @foreach (\App\Models\Loan::STATUSES as $status)
                                    <option value="{{ $status }}" @selected(request('status') === $status)>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('status')" class="mt-1" />
                        </div>

                        <div class="flex items-center gap-2 lg:pt-5">
                            <x-primary-button>Tampilkan</x-primary-button>

                            @if ($hasFilter)
                                <a href="{{ route('admin.reports.loans') }}"
                                   class="inline-flex items-center gap-1 rounded-md px-2 py-1.5 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900">
                                    <x-icon name="close" class="h-4 w-4" /> Reset
                                </a>
                            @endif
                        </div>
                    </form>
                </div>

                {{-- Ringkasan dan tombol unduh --}}
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-gray-500">
                        Laporan ini mengikuti filter di atas. Tanggal yang dipakai adalah tanggal pinjam.
                    </p>

                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('admin.reports.loans.pdf', request()->query()) }}"
                           class="btn border border-red-200 bg-red-50 text-red-700 hover:bg-red-100">
                            Unduh PDF
                        </a>
                        <a href="{{ route('admin.reports.loans.excel', request()->query()) }}"
                           class="btn border border-emerald-200 bg-emerald-50 text-emerald-700 hover:bg-emerald-100">
                            Unduh Excel
                        </a>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
                    <x-stat-card label="Total Peminjaman"
                                 :value="number_format($totalLoans, 0, ',', '.')" color="blue" />

                    <x-stat-card label="Total Unit Barang"
                                 :value="number_format($totalUnits, 0, ',', '.')" color="green" />

                    <div class="card col-span-2 p-5">
                        <p class="text-sm font-medium text-gray-500">Per Status</p>
                        <div class="mt-3 grid grid-cols-1 gap-x-6 gap-y-2 text-sm sm:grid-cols-2">
                            @foreach (\App\Models\Loan::STATUSES as $status)
                                <div class="flex items-center justify-between gap-3">
                                    <x-loan-status-badge :status="$status" />
                                    <span class="font-semibold tabular-nums text-gray-900">
                                        {{ number_format($summary->get($status)?->total ?? 0, 0, ',', '.') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Pratinjau tabel --}}
                <div class="card p-6">
                    <h3 class="mb-4 text-base font-semibold text-gray-900">Pratinjau Data</h3>

                    <div class="-mx-6 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left font-semibold">No</th>
                                    <th class="px-4 py-3 text-left font-semibold">Peminjam</th>
                                    <th class="px-4 py-3 text-left font-semibold">Barang</th>
                                    <th class="px-4 py-3 text-left font-semibold">Kategori</th>
                                    <th class="px-4 py-3 text-left font-semibold">Jumlah</th>
                                    <th class="px-4 py-3 text-left font-semibold">Tanggal Pinjam</th>
                                    <th class="px-4 py-3 text-left font-semibold">Jatuh Tempo</th>
                                    <th class="px-4 py-3 text-left font-semibold">Tanggal Kembali</th>
                                    <th class="px-4 py-3 text-left font-semibold">Status</th>
                                    <th class="px-6 py-3 text-left font-semibold">Kondisi Kembali</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($loans as $loan)
                                    <tr>
                                        <td class="px-6 py-3 text-gray-500">{{ $loans->firstItem() + $loop->index }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 font-medium text-gray-900">{{ $loan->user->name }}</td>
                                        <td class="px-4 py-3">
                                            <span class="text-gray-900">{{ $loan->item->name }}</span>
                                            <span class="block font-mono text-xs text-gray-400">{{ $loan->item->code }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-gray-600">{{ $loan->item->category->name }}</td>
                                        <td class="px-4 py-3">{{ number_format($loan->quantity, 0, ',', '.') }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-gray-600">{{ $loan->loan_date->translatedFormat('j M Y') }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 {{ $loan->status === 'terlambat' ? 'font-medium text-red-600' : 'text-gray-600' }}">{{ $loan->due_date->translatedFormat('j M Y') }}</td>
                                        <td class="whitespace-nowrap px-4 py-3 text-gray-600">
                                            {{ $loan->returnLog ? $loan->returnLog->returned_at->translatedFormat('j M Y') : '-' }}
                                        </td>
                                        <td class="px-4 py-3"><x-loan-status-badge :status="$loan->status" /></td>
                                        <td class="px-6 py-3 text-gray-600">
                                            {{ $loan->returnLog ? ucfirst($loan->returnLog->condition_on_return) : '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="px-6 py-14 text-center">
                                            <x-icon name="chart" class="mx-auto h-8 w-8 text-gray-300" />
                                            <p class="mt-3 text-sm font-medium text-gray-900">Tidak ada data peminjaman untuk filter ini</p>
                                            @if ($hasFilter)
                                                <a href="{{ route('admin.reports.loans') }}" class="mt-3 inline-block text-sm font-medium text-brand-600 hover:underline">Reset filter</a>
                                            @endif
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