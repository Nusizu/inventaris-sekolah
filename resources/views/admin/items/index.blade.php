<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Data Barang</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <x-breadcrumb :items="[
                'Dashboard' => route('admin.dashboard'),
                'Barang' => null,
            ]" />

            <x-flash-message />

            <livewire:item-list scope="admin" />
        </div>
    </div>
</x-app-layout>