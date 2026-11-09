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

            @include('loans._summary', ['loan' => $loan, 'title' => 'Detail Pengajuan'])

            <div class="card p-6">
                <form method="POST" action="{{ route('admin.loans.reject.store', $loan) }}">
                    @csrf

                    <div>
                        <x-input-label for="decision_note">
                            Alasan Penolakan <span class="text-red-600">*</span>
                        </x-input-label>
                        <textarea id="decision_note" name="decision_note" rows="3" required autofocus
                                  placeholder="Contoh: Stok dibutuhkan untuk kegiatan praktikum minggu ini"
                                  class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">{{ old('decision_note') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Alasan ini akan ditampilkan kepada peminjam.</p>
                        <x-input-error :messages="$errors->get('decision_note')" class="mt-2" />
                    </div>

                    <div class="mt-8 flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end">
                        <a href="{{ route('admin.loans.index') }}" class="btn btn-secondary">Batal</a>
                        <x-danger-button>Tolak Pengajuan</x-danger-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>