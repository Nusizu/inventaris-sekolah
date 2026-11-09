<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Peminjaman Saya</h2>
    </x-slot>

    @php
        $hasFilter = request()->anyFilled(['q', 'status']);
        $pill = 'whitespace-nowrap rounded-full px-3 py-1 text-sm font-medium transition-colors';
    @endphp

    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <x-breadcrumb :items="[
                'Dashboard' => route('user.dashboard'),
                'Peminjaman Saya' => null,
            ]" />

            <x-flash-message />

            <div class="card p-6">
                <div class="mb-5 space-y-4">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                        <form method="GET" action="{{ route('user.loans.index') }}" class="flex flex-1 flex-wrap items-center gap-2">
                            @if (request('status'))
                                <input type="hidden" name="status" value="{{ request('status') }}">
                            @endif

                            <div class="relative min-w-0 flex-1 sm:w-72 sm:flex-none">
                                <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                                <input type="text" name="q" value="{{ request('q') }}" autofocus
                                       placeholder="Cari nama barang..."
                                       class="w-full rounded-md border-gray-300 bg-white pl-9 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
                            </div>

                            <x-secondary-button type="submit">Cari</x-secondary-button>

                            @if ($hasFilter)
                                <a href="{{ route('user.loans.index') }}"
                                   class="inline-flex items-center gap-1 rounded-md px-2 py-1.5 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900">
                                    <x-icon name="close" class="h-4 w-4" /> Reset
                                </a>
                            @endif
                        </form>

                        <a href="{{ route('user.loans.create') }}" class="btn btn-primary lg:shrink-0">
                            + Ajukan Peminjaman
                        </a>
                    </div>

                    <nav class="-mx-1 flex gap-1 overflow-x-auto px-1 pb-1" aria-label="Filter status peminjaman">
                        <a href="{{ route('user.loans.index', array_filter(['q' => request('q')])) }}"
                           class="{{ $pill }} {{ request('status') ? 'text-gray-600 hover:bg-gray-100' : 'bg-gray-800 text-white' }}">Semua</a>

                        @foreach (\App\Models\Loan::STATUSES as $status)
                            <a href="{{ route('user.loans.index', array_filter(['q' => request('q'), 'status' => $status])) }}"
                               class="{{ $pill }} {{ request('status') === $status ? 'bg-gray-800 text-white' : 'text-gray-600 hover:bg-gray-100' }}">{{ ucfirst($status) }}</a>
                        @endforeach
                    </nav>
                </div>

                @if ($loans->isEmpty())
                    <div class="py-14 text-center">
                        <x-icon name="swap" class="mx-auto h-8 w-8 text-gray-300" />
                        @if ($hasFilter)
                            <p class="mt-3 text-sm font-medium text-gray-900">Tidak ada peminjaman yang cocok</p>
                            <p class="mt-1 text-sm text-gray-500">Coba ubah kata kunci atau status.</p>
                            <a href="{{ route('user.loans.index') }}" class="mt-3 inline-block text-sm font-medium text-brand-600 hover:underline">Reset filter</a>
                        @else
                            <p class="mt-3 text-sm font-medium text-gray-900">Belum ada riwayat peminjaman</p>
                            <a href="{{ route('user.loans.create') }}" class="mt-3 inline-block text-sm font-medium text-brand-600 hover:underline">Ajukan peminjaman pertama</a>
                        @endif
                    </div>
                @else
                    {{-- HP: daftar kartu --}}
                    <ul class="divide-y divide-gray-100 md:hidden">
                        @foreach ($loans as $loan)
                            <li class="py-4 first:pt-0 last:pb-0">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="truncate font-medium text-gray-900">{{ $loan->item->name }}</p>
                                        <p class="mt-0.5 text-xs text-gray-500">{{ number_format($loan->quantity, 0, ',', '.') }} unit</p>
                                    </div>
                                    <x-loan-status-badge :status="$loan->status" />
                                </div>

                                <dl class="mt-3 grid grid-cols-2 gap-3 text-sm">
                                    <div>
                                        <dt class="text-xs text-gray-500">Tanggal Pinjam</dt>
                                        <dd class="text-gray-900">{{ $loan->loan_date->translatedFormat('j M Y') }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs text-gray-500">Jatuh Tempo</dt>
                                        <dd class="{{ $loan->status === 'terlambat' ? 'font-medium text-red-600' : 'text-gray-900' }}">{{ $loan->due_date->translatedFormat('j M Y') }}</dd>
                                    </div>
                                </dl>

                                @if ($loan->status === 'ditolak' && $loan->decision_note)
                                    <p class="mt-3 rounded-md bg-rose-50 px-3 py-2 text-sm text-rose-700">{{ $loan->decision_note }}</p>
                                @elseif ($loan->status === 'menunggu')
                                    <div class="mt-3 flex items-center justify-between gap-3">
                                        <p class="text-xs text-gray-500">Menunggu persetujuan Admin</p>
                                        <form method="POST" action="{{ route('user.loans.cancel', $loan) }}"
                                              onsubmit="return confirm({{ \Illuminate\Support\Js::from('Batalkan pengajuan peminjaman ' . $loan->item->name . '?') }})">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="rounded-md border border-red-200 px-3 py-1.5 text-sm font-medium text-red-600 transition-colors hover:bg-red-50">Batalkan</button>
                                        </form>
                                    </div>
                                @endif
                            </li>
                        @endforeach
                    </ul>

                    {{-- Layar sedang ke atas: tabel --}}
                    <div class="-mx-6 hidden overflow-x-auto md:block">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left font-semibold">No</th>
                                    <th class="px-4 py-3 text-left font-semibold">Barang</th>
                                    <th class="px-4 py-3 text-left font-semibold">Jumlah</th>
                                    <th class="px-4 py-3 text-left font-semibold">Tanggal Pinjam</th>
                                    <th class="px-4 py-3 text-left font-semibold">Jatuh Tempo</th>
                                    <th class="px-4 py-3 text-left font-semibold">Status</th>
                                    <th class="px-4 py-3 text-left font-semibold">Keterangan</th>
                                    <th class="px-6 py-3 text-left font-semibold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($loans as $loan)
                                    <tr>
                                        <td class="px-6 py-3 text-gray-500">{{ $loans->firstItem() + $loop->index }}</td>
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ $loan->item->name }}</td>
                                        <td class="px-4 py-3">{{ number_format($loan->quantity, 0, ',', '.') }}</td>
                                        <td class="px-4 py-3 text-gray-600">{{ $loan->loan_date->translatedFormat('j M Y') }}</td>
                                        <td class="px-4 py-3 {{ $loan->status === 'terlambat' ? 'font-medium text-red-600' : 'text-gray-600' }}">{{ $loan->due_date->translatedFormat('j M Y') }}</td>
                                        <td class="px-4 py-3"><x-loan-status-badge :status="$loan->status" /></td>
                                        <td class="px-4 py-3">
                                            @if ($loan->status === 'ditolak' && $loan->decision_note)
                                                <span class="text-rose-700">{{ $loan->decision_note }}</span>
                                            @elseif ($loan->status === 'menunggu')
                                                <span class="text-gray-500">Menunggu persetujuan Admin</span>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-3">
                                            @if ($loan->status === 'menunggu')
                                                <form method="POST" action="{{ route('user.loans.cancel', $loan) }}"
                                                      onsubmit="return confirm({{ \Illuminate\Support\Js::from('Batalkan pengajuan peminjaman ' . $loan->item->name . '?') }})">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                            class="rounded-md border border-red-200 px-2.5 py-1 text-xs font-medium text-red-600 transition-colors hover:bg-red-50">Batalkan</button>
                                                </form>
                                            @else
                                                <span class="text-gray-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <div class="mt-4">
                    {{ $loans->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>