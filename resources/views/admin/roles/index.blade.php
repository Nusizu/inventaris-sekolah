<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Peran dan Izin</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <x-breadcrumb :items="[
                'Dashboard' => route('admin.dashboard'),
                'Peran' => null,
            ]" />

            <x-flash-message />

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-gray-600">
                        Peran menentukan halaman dan aksi yang boleh diakses. Berikan peran ke user lewat menu Manajemen User.
                    </p>

                    <a href="{{ route('admin.roles.create') }}"
                       class="inline-flex items-center justify-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700">
                        + Tambah Peran
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">No</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Peran</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Jenis</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Jumlah Izin</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Jumlah User</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($roles as $role)
                                @php
                                    $builtIn = in_array($role->name, \App\Support\Permissions::PROTECTED_ROLES, true);
                                @endphp
                                <tr>
                                    <td class="px-4 py-3">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3 font-medium">{{ ucfirst($role->name) }}</td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-full px-2 py-1 text-xs font-medium {{ $builtIn ? 'bg-purple-100 text-purple-800' : 'bg-gray-100 text-gray-700' }}">
                                            {{ $builtIn ? 'Bawaan' : 'Kustom' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">{{ number_format($role->permissions_count, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3">{{ number_format($role->users_count, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <a href="{{ route('admin.roles.edit', $role) }}" class="text-indigo-600 hover:underline">Edit</a>

                                            @unless ($builtIn)
                                                <form method="POST" action="{{ route('admin.roles.destroy', $role) }}"
                                                      onsubmit="return confirm({{ \Illuminate\Support\Js::from('Hapus peran ' . $role->name . '?') }})">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                                </form>
                                            @endunless
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>