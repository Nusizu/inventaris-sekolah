<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Proses Pengembalian</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <x-breadcrumb :items="[
                'Dashboard' => route('admin.dashboard'),
                'Peminjaman' => route('admin.loans.index'),
                'Pengembalian' => null,
            ]" />

            <div class="mb-6 bg-white p-6 shadow-sm sm:rounded-lg">
                <h3 class="mb-4 text-sm font-semibold uppercase tracking-wide text-gray-500">Detail Peminjaman</h3>
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
                <form method="POST" action="{{ route('admin.return-logs.store', $loan) }}">
                    @csrf

                    <div class="space-y-5">
                        <div>
                            <x-input-label for="returned_at">
                                Tanggal Kembali <span class="text-red-600">*</span>
                            </x-input-label>
                            <x-text-input id="returned_at" name="returned_at" type="text" class="js-date"
                                :value="old('returned_at', now()->toDateString())" placeholder="Pilih tanggal"
                                data-max-date="today" required autofocus />
                            <x-input-error :messages="$errors->get('returned_at')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="condition_on_return">
                                Kondisi Barang Saat Dikembalikan <span class="text-red-600">*</span>
                            </x-input-label>
                            <select id="condition_on_return" name="condition_on_return" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">-- Pilih Kondisi --</option>
                                @foreach (\App\Models\Item::CONDITIONS as $condition)
                                    <option value="{{ $condition }}" @selected(old('condition_on_return') === $condition)>
                                        {{ ucfirst($condition) }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('condition_on_return')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="notes">Catatan</x-input-label>
                            <textarea id="notes" name="notes" rows="3"
                                      placeholder="Contoh: Lensa objektif tergores di bagian samping"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('notes') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-6 flex items-center gap-3">
                        <x-primary-button>Simpan Pengembalian</x-primary-button>
                        <a href="{{ route('admin.loans.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>