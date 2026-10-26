<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Tambah Barang</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <x-breadcrumb :items="[
                'Dashboard' => route('admin.dashboard'),
                'Barang' => route('admin.items.index'),
                'Tambah' => null,
            ]" />

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form method="POST" action="{{ route('admin.items.store') }}">
                    @csrf
                    @include('admin.items._form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>