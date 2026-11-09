<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Tambah Peminjaman</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <x-breadcrumb :items="[
                'Dashboard' => route('admin.dashboard'),
                'Peminjaman' => route('admin.loans.index'),
                'Tambah' => null,
            ]" />

            <div class="card p-6">
                <form method="POST" action="{{ route('admin.loans.store') }}">
                    @csrf
                    @include('loans._form', ['cancelUrl' => route('admin.loans.index')])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>