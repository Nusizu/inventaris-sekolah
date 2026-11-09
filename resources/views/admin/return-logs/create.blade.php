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

            @include('loans._summary', ['loan' => $loan, 'title' => 'Detail Peminjaman'])

            <div class="card p-6">
                <form method="POST" action="{{ route('admin.loans.return.store', $loan) }}">
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
                                    class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
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
                                      class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">{{ old('notes') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-8 flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end">
                        <a href="{{ route('admin.loans.index') }}" class="btn btn-secondary">Batal</a>
                        <x-primary-button>Simpan Pengembalian</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>