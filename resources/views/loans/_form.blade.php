<div class="space-y-5">
    @isset($users)
        <div>
            <x-input-label for="user_id">
                Peminjam <span class="text-red-600">*</span>
            </x-input-label>
            <select id="user_id" name="user_id" required autofocus
                    class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
                <option value="">-- Pilih Peminjam --</option>
                @foreach ($users as $user)
                    <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>
                        {{ $user->name }} ({{ $user->email }})
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('user_id')" class="mt-2" />
        </div>
    @endisset

    <div>
        <x-input-label for="item_id">
            Barang <span class="text-red-600">*</span>
        </x-input-label>
        <select id="item_id" name="item_id" required {{ isset($users) ? '' : 'autofocus' }}
                class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
            <option value="">-- Pilih Barang --</option>
            @foreach ($items as $item)
                <option value="{{ $item->id }}" @selected(old('item_id', request('item_id')) == $item->id)>
                    {{ $item->name }} ({{ $item->code }}) - stok: {{ number_format($item->stock, 0, ',', '.') }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('item_id')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="quantity">
            Jumlah <span class="text-red-600">*</span>
        </x-input-label>
        <x-text-input id="quantity" name="quantity" type="number" min="1" class="mt-1 block w-full"
            :value="old('quantity', 1)" placeholder="Contoh: 1" required />
        <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div>
            <x-input-label for="loan_date">
                Tanggal Pinjam <span class="text-red-600">*</span>
            </x-input-label>
            <x-text-input id="loan_date" name="loan_date" type="text" class="js-date"
                :value="old('loan_date', now()->toDateString())" placeholder="Pilih tanggal"
                data-min-date="today" required />
            <x-input-error :messages="$errors->get('loan_date')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="due_date">
                Tanggal Jatuh Tempo <span class="text-red-600">*</span>
            </x-input-label>
            <x-text-input id="due_date" name="due_date" type="text" class="js-date"
                :value="old('due_date', now()->addDays(7)->toDateString())" placeholder="Pilih tanggal"
                data-min-date="today" required />
            <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
        </div>
    </div>
</div>

{{-- Tombol: di HP bertumpuk (Simpan di atas), di layar lebar rata kanan --}}
<div class="mt-8 flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:justify-end">
    <a href="{{ $cancelUrl }}" class="btn btn-secondary">Batal</a>
    <x-primary-button>Simpan</x-primary-button>
</div>