<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Tolak Pengajuan</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <x-breadcrumb :items="[
                'Dashboard' => route('admin.dashboard'),
                'Peminjaman' => route('admin.loans.index'),
                'Tolak Pengajuan' => null,
            ]" />

            <div class="mb-6 bg-white p-6 shadow-sm sm:rounded-lg">
                <h3 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-500">Detail Pengajuan</h3>
                <dl class="grid grid-cols-1 gap-4 text-sm sm:grid-cols-2">
                    <div>
                        <dt class="text-gray-500">Peminjam</dt>
                        <dd class="font-medium text-gray-900">{{ $loan->user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Barang</dt>
                        <dd class="font-medium text-gray-900">{{ $loan->item->name }} ({{ $loan->item->code }})</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Jumlah</dt>
                        <dd class="font-medium text-gray-900">{{ number_format($loan->quantity, 0, ',', '.') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Status</dt>
                        <dd><x-loan-status-badge :status="$loan->status" /></dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Tanggal Pinjam</dt>
                        <dd class="font-medium text-gray-900">{{ $loan->loan_date->translatedFormat('j M Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-gray-500">Jatuh Tempo</dt>
                        <dd class="font-medium text-gray-900">{{ $loan->due_date->translatedFormat('j M Y') }}</dd>
                    </div>
                </dl>
            </div>

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('admin.loans.reject.store', $loan) }}">
                    @csrf

                    <div>
                        <x-input-label for="decision_note">
                            Alasan Penolakan <span class="text-red-600">*</span>
                        </x-input-label>
                        <textarea id="decision_note" name="decision_note" rows="3" required autofocus
                                  placeholder="Contoh: Stok dibutuhkan untuk kegiatan praktikum minggu ini"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('decision_note') }}</textarea>
                        <x-input-error :messages="$errors->get('decision_note')" class="mt-2" />
                    </div>

                    <div class="mt-6 flex items-center gap-3">
                        <x-danger-button>Tolak Pengajuan</x-danger-button>
                        <a href="{{ route('admin.loans.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>