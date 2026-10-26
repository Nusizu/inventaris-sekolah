<div class="space-y-5">
    @isset($users)
        <div>
            <x-input-label for="user_id">
                Peminjam <span class="text-red-600">*</span>
            </x-input-label>
            <select id="user_id" name="user_id" required autofocus
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
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
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">-- Pilih Barang --</option>
            @foreach ($items as $item)
                <option value="{{ $item->id }}" @selected(old('item_id') == $item->id)>
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
            <x-text-input id="loan_date" name="loan_date" type="date" class="mt-1 block w-full"
                :value="old('loan_date', now()->toDateString())" required />
            <x-input-error :messages="$errors->get('loan_date')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="due_date">
                Tanggal Jatuh Tempo <span class="text-red-600">*</span>
            </x-input-label>
            <x-text-input id="due_date" name="due_date" type="date" class="mt-1 block w-full"
                :value="old('due_date', now()->addDays(7)->toDateString())" required />
            <x-input-error :messages="$errors->get('due_date')" class="mt-2" />
        </div>
    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <x-primary-button>Simpan</x-primary-button>
    <a href="{{ $cancelUrl }}" class="text-sm text-gray-600 hover:text-gray-900">Batal</a>
</div>