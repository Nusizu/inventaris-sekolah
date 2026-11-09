<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Data Peminjaman</h2>
    </x-slot>

    @php
        $hasFilter = request()->anyFilled(['q', 'status']);
        $pill = 'whitespace-nowrap rounded-full px-3 py-1 text-sm font-medium transition-colors';
    @endphp

    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <x-breadcrumb :items="[
                'Dashboard' => route('admin.dashboard'),
                'Peminjaman' => null,
            ]" />

            <x-flash-message />

            <div class="card p-6">
                <div class="mb-5 space-y-4">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                        <form method="GET" action="{{ route('admin.loans.index') }}" class="flex flex-1 flex-wrap items-center gap-2">
                            {{-- Status yang sedang dipilih (lewat pill di bawah) ikut terkirim saat mencari --}}
                            @if (request('status'))
                                <input type="hidden" name="status" value="{{ request('status') }}">
                            @endif

                            <div class="relative min-w-0 flex-1 sm:w-72 sm:flex-none">
                                <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                                <input type="text" name="q" value="{{ request('q') }}" autofocus
                                       placeholder="Cari peminjam atau barang..."
                                       class="w-full rounded-md border-gray-300 bg-white pl-9 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
                            </div>

                            <x-secondary-button type="submit">Cari</x-secondary-button>

                            @if ($hasFilter)
                                <a href="{{ route('admin.loans.index') }}"
                                   class="inline-flex items-center gap-1 rounded-md px-2 py-1.5 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900">
                                    <x-icon name="close" class="h-4 w-4" /> Reset
                                </a>
                            @endif
                        </form>

                        <a href="{{ route('admin.loans.create') }}" class="btn btn-primary lg:shrink-0">
                            + Tambah Peminjaman
                        </a>
                    </div>

                    {{-- Filter status: satu klik, tanpa dropdown --}}
                    <nav class="-mx-1 flex gap-1 overflow-x-auto px-1 pb-1" aria-label="Filter status peminjaman">
                        <a href="{{ route('admin.loans.index', array_filter(['q' => request('q')])) }}"
                           class="{{ $pill }} {{ request('status') ? 'text-gray-600 hover:bg-gray-100' : 'bg-gray-800 text-white' }}">Semua</a>

                        @foreach (\App\Models\Loan::STATUSES as $status)
                            <a href="{{ route('admin.loans.index', array_filter(['q' => request('q'), 'status' => $status])) }}"
                               class="{{ $pill }} {{ request('status') === $status ? 'bg-gray-800 text-white' : 'text-gray-600 hover:bg-gray-100' }}">{{ ucfirst($status) }}</a>
                        @endforeach
                    </nav>
                </div>

                <div class="-mx-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold">No</th>
                                <th class="px-4 py-3 text-left font-semibold">Peminjam</th>
                                <th class="px-4 py-3 text-left font-semibold">Barang</th>
                                <th class="px-4 py-3 text-left font-semibold">Jumlah</th>
                                <th class="px-4 py-3 text-left font-semibold">Tanggal Pinjam</th>
                                <th class="px-4 py-3 text-left font-semibold">Jatuh Tempo</th>
                                <th class="px-4 py-3 text-left font-semibold">Status</th>
                                <th class="px-6 py-3 text-left font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($loans as $loan)
                                @php
                                    $confirmDelete = 'Hapus data peminjaman ' . $loan->item->name . ' oleh ' . $loan->user->name . '?'
                                        . ($loan->isActive() ? ' Stok barang akan dikembalikan.' : '');
                                    $confirmApprove = 'Setujui peminjaman ' . $loan->item->name . ' oleh ' . $loan->user->name . '?';
                                @endphp
                                <tr>
                                    <td class="px-6 py-3 text-gray-500">{{ $loans->firstItem() + $loop->index }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ $loan->user->name }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $loan->item->name }}</td>
                                    <td class="px-4 py-3">{{ number_format($loan->quantity, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3 text-gray-600">{{ $loan->loan_date->translatedFormat('j M Y') }}</td>
                                    <td class="px-4 py-3 {{ $loan->status === 'terlambat' ? 'font-medium text-red-600' : 'text-gray-600' }}">{{ $loan->due_date->translatedFormat('j M Y') }}</td>
                                    <td class="px-4 py-3"><x-loan-status-badge :status="$loan->status" /></td>
                                    <td class="px-6 py-3">
                                        <div class="flex flex-wrap items-center gap-2 whitespace-nowrap">
                                            @if ($loan->status === 'menunggu')
                                                <form method="POST" action="{{ route('admin.loans.approve', $loan) }}"
                                                      onsubmit="return confirm({{ \Illuminate\Support\Js::from($confirmApprove) }})">
                                                    @csrf
                                                    <button type="submit"
                                                            class="rounded-md bg-emerald-600 px-2.5 py-1 text-xs font-medium text-white transition-colors hover:bg-emerald-700">Setujui</button>
                                                </form>
                                                <a href="{{ route('admin.loans.reject.create', $loan) }}"
                                                   class="rounded-md border border-gray-300 bg-white px-2.5 py-1 text-xs font-medium text-gray-700 transition-colors hover:bg-gray-50">Tolak</a>
                                            @elseif ($loan->isActive())
                                                <a href="{{ route('admin.loans.return.create', $loan) }}"
                                                   class="rounded-md border border-brand-600/30 bg-brand-50 px-2.5 py-1 text-xs font-medium text-brand-700 transition-colors hover:bg-brand-100">Kembalikan</a>
                                            @elseif ($loan->status === 'dikembalikan')
                                                <span class="text-xs text-gray-400">Selesai</span>
                                            @else
                                                <span class="text-xs text-gray-400">-</span>
                                            @endif

                                            <form method="POST" action="{{ route('admin.loans.destroy', $loan) }}"
                                                  onsubmit="return confirm({{ \Illuminate\Support\Js::from($confirmDelete) }})">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="rounded-md px-2 py-1 text-xs font-medium text-gray-400 transition-colors hover:bg-red-50 hover:text-red-600">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-14 text-center">
                                        <x-icon name="swap" class="mx-auto h-8 w-8 text-gray-300" />
                                        @if ($hasFilter)
                                            <p class="mt-3 text-sm font-medium text-gray-900">Tidak ada peminjaman yang cocok</p>
                                            <p class="mt-1 text-sm text-gray-500">Coba ubah kata kunci atau status.</p>
                                            <a href="{{ route('admin.loans.index') }}" class="mt-3 inline-block text-sm font-medium text-brand-600 hover:underline">Reset filter</a>
                                        @else
                                            <p class="mt-3 text-sm font-medium text-gray-900">Belum ada data peminjaman</p>
                                            <p class="mt-1 text-sm text-gray-500">Peminjaman akan muncul di sini setelah ada pengajuan atau Anda menambahkannya.</p>
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
</x-app-layout>