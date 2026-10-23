<div>
    <x-input-label for="name">
        Nama Kategori <span class="text-red-600">*</span>
    </x-input-label>
    <x-text-input
        id="name"
        name="name"
        type="text"
        class="mt-1 block w-full"
        :value="old('name', $category->name ?? '')"
        placeholder="Contoh: Alat Lab"
        required
        autofocus
    />
    <x-input-error :messages="$errors->get('name')" class="mt-2" />
</div>

<div class="mt-6 flex items-center gap-3">
    <x-primary-button>Simpan</x-primary-button>
    <a href="{{ route('admin.categories.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Batal</a>
</div>