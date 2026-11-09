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

<x-form-actions :cancel="route('admin.categories.index')" />