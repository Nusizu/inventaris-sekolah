<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Manajemen User</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <x-breadcrumb :items="[
                'Dashboard' => route('admin.dashboard'),
                'Manajemen User' => null,
            ]" />

            <x-flash-message />

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <div class="mb-4 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-wrap gap-2">
                        <input type="text" name="q" value="{{ request('q') }}" autofocus
                               placeholder="Cari nama atau email..."
                               class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                        <select name="role"
                                class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="">Semua Role</option>
                            <option value="admin" @selected(request('role') === 'admin')>Admin</option>
                            <option value="user" @selected(request('role') === 'user')>User</option>
                        </select>

                        <x-secondary-button type="submit">Filter</x-secondary-button>

                        @if (request()->hasAny(['q', 'role']))
                            <a href="{{ route('admin.users.index') }}" class="self-center text-sm text-gray-600 hover:text-gray-900">Reset</a>
                        @endif
                    </form>

                    <a href="{{ route('admin.users.create') }}"
                       class="inline-flex items-center justify-center rounded-md bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-gray-700">
                        + Tambah User
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">No</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Nama</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Email</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Role</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Terdaftar</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($users as $user)
                                @php $isSelf = $user->is(auth()->user()); @endphp
                                <tr>
                                    <td class="px-4 py-3">{{ $users->firstItem() + $loop->index }}</td>
                                    <td class="px-4 py-3">
                                        {{ $user->name }}
                                        @if ($isSelf)
                                            <span class="ml-1 text-xs text-gray-400">(Anda)</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3">{{ $user->email }}</td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-full px-2 py-1 text-xs font-medium {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-sky-100 text-sky-800' }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">{{ $user->created_at->translatedFormat('j M Y') }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <a href="{{ route('admin.users.edit', $user) }}" class="text-indigo-600 hover:underline">Edit</a>
                                            @unless ($isSelf)
                                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                                      onsubmit="return confirm({{ \Illuminate\Support\Js::from('Hapus user ' . $user->name . '?') }})">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                                                </form>
                                            @endunless
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-6 text-center text-gray-500">Belum ada data user.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>