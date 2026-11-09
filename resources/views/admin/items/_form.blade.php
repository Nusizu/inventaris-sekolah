<div class="space-y-5">
    <div>
        <x-input-label for="category_id">
            Kategori <span class="text-red-600">*</span>
        </x-input-label>
        <select id="category_id" name="category_id" required autofocus
                class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
            <option value="">-- Pilih Kategori --</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}"
                    @selected(old('category_id', $item->category_id ?? '') == $category->id)>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        <x-input-error :messages="$errors->get('category_id')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="name">
            Nama Barang <span class="text-red-600">*</span>
        </x-input-label>
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
            :value="old('name', $item->name ?? '')" placeholder="Contoh: Mikroskop Binokuler" required />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    {{-- Kode, stok, dan kondisi sejajar di layar lebar; bertumpuk di HP --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">
        <div>
            <x-input-label for="code">
                Kode Barang <span class="text-red-600">*</span>
            </x-input-label>
            <x-text-input id="code" name="code" type="text" class="mt-1 block w-full font-mono"
                :value="old('code', $item->code ?? '')" placeholder="Contoh: LAB-001" required />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="stock">
                Stok <span class="text-red-600">*</span>
            </x-input-label>
            <x-text-input id="stock" name="stock" type="number" min="0" class="mt-1 block w-full"
                :value="old('stock', $item->stock ?? '')" placeholder="Contoh: 10" required />
            <x-input-error :messages="$errors->get('stock')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="condition">
                Kondisi <span class="text-red-600">*</span>
            </x-input-label>
            <select id="condition" name="condition" required
                    class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
                <option value="">-- Pilih Kondisi --</option>
                @foreach (\App\Models\Item::CONDITIONS as $condition)
                    <option value="{{ $condition }}"
                        @selected(old('condition', $item->condition ?? '') === $condition)>
                        {{ ucfirst($condition) }}
                    </option>
                @endforeach
            </select>
            <x-input-error :messages="$errors->get('condition')" class="mt-2" />
        </div>
    </div>
</div>

<x-form-actions :cancel="route('admin.items.index')" />