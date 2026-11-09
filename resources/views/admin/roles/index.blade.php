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

            <div class="card p-6">
                <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="max-w-2xl text-sm text-gray-500">
                        Peran menentukan halaman dan aksi yang boleh diakses. Berikan peran ke user lewat menu Manajemen User.
                    </p>

                    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary sm:shrink-0">
                        + Tambah Peran
                    </a>
                </div>

                <div class="-mx-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold">No</th>
                                <th class="px-4 py-3 text-left font-semibold">Peran</th>
                                <th class="px-4 py-3 text-left font-semibold">Jenis</th>
                                <th class="px-4 py-3 text-left font-semibold">Jumlah Izin</th>
                                <th class="px-4 py-3 text-left font-semibold">Jumlah User</th>
                                <th class="px-6 py-3 text-left font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($roles as $role)
                                @php
                                    $builtIn = in_array($role->name, \App\Support\Permissions::PROTECTED_ROLES, true);
                                @endphp
                                <tr>
                                    <td class="px-6 py-3 text-gray-500">{{ $loop->iteration }}</td>
                                    <td class="px-4 py-3"><x-role-badge :role="$role->name" /></td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center whitespace-nowrap rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $builtIn ? 'bg-violet-50 text-violet-700 ring-violet-600/20' : 'bg-gray-100 text-gray-600 ring-gray-500/20' }}">
                                            {{ $builtIn ? 'Bawaan' : 'Kustom' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">{{ number_format($role->permissions_count, 0, ',', '.') }}</td>
                                    <td class="px-4 py-3">{{ number_format($role->users_count, 0, ',', '.') }}</td>
                                    <td class="px-6 py-3">
                                        <div class="flex items-center gap-4 whitespace-nowrap font-medium">
                                            <a href="{{ route('admin.roles.edit', $role) }}" class="text-brand-600 hover:text-brand-700 hover:underline">Edit</a>

                                            @unless ($builtIn)
                                                <form method="POST" action="{{ route('admin.roles.destroy', $role) }}"
                                                      onsubmit="return confirm({{ \Illuminate\Support\Js::from('Hapus peran ' . $role->name . '?') }})">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-700 hover:underline">Hapus</button>
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