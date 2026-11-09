{{--
    Ringkasan satu peminjaman (dipakai halaman Tolak Pengajuan & Proses Pengembalian).
    Pemakaian: @include('loans._summary', ['loan' => $loan, 'title' => 'Detail Pengajuan'])
--}}
<div class="card mb-6 p-6">
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-500">{{ $title }}</p>
            <h3 class="mt-1 truncate text-lg font-semibold text-gray-900">{{ $loan->item->name }}</h3>
            <p class="mt-0.5 font-mono text-xs text-gray-500">{{ $loan->item->code }}</p>
        </div>
        <x-loan-status-badge :status="$loan->status" />
    </div>

    <dl class="mt-5 grid grid-cols-2 gap-x-6 gap-y-4 border-t border-gray-100 pt-5 text-sm sm:grid-cols-4">
        <div>
            <dt class="text-gray-500">Peminjam</dt>
            <dd class="mt-0.5 font-medium text-gray-900">{{ $loan->user->name }}</dd>
        </div>
        <div>
            <dt class="text-gray-500">Jumlah</dt>
            <dd class="mt-0.5 font-medium text-gray-900">{{ number_format($loan->quantity, 0, ',', '.') }}</dd>
        </div>
        <div>
            <dt class="text-gray-500">Tanggal Pinjam</dt>
            <dd class="mt-0.5 font-medium text-gray-900">{{ $loan->loan_date->translatedFormat('j M Y') }}</dd>
        </div>
        <div>
            <dt class="text-gray-500">Jatuh Tempo</dt>
            <dd class="mt-0.5 font-medium {{ $loan->status === 'terlambat' ? 'text-red-600' : 'text-gray-900' }}">{{ $loan->due_date->translatedFormat('j M Y') }}</dd>
        </div>
    </dl>
</div>