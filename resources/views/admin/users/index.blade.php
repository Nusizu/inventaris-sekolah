<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Daftar Barang</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <x-breadcrumb :items="[
                'Dashboard' => route('user.dashboard'),
                'Daftar Barang' => null,
            ]" />

            <livewire:item-list scope="user" />
        </div>
    </div>
</x-app-layout>