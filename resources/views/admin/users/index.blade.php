<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Manajemen User</h2>
    </x-slot>

    @php
        $hasFilter = request()->anyFilled(['q', 'role']);
        $pill = 'whitespace-nowrap rounded-full px-3 py-1 text-sm font-medium transition-colors';
    @endphp

    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <x-breadcrumb :items="[
                'Dashboard' => route('admin.dashboard'),
                'Manajemen User' => null,
            ]" />

            <x-flash-message />

            <div class="card p-6">
                <div class="mb-5 space-y-4">
                    <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                        <form method="GET" action="{{ route('admin.users.index') }}" class="flex flex-1 flex-wrap items-center gap-2">
                            {{-- Peran yang sedang dipilih (lewat pill di bawah) ikut terkirim saat mencari --}}
                            @if (request('role'))
                                <input type="hidden" name="role" value="{{ request('role') }}">
                            @endif

                            <div class="relative min-w-0 flex-1 sm:w-72 sm:flex-none">
                                <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                                <input type="text" name="q" value="{{ request('q') }}" autofocus
                                       placeholder="Cari nama atau email..."
                                       class="w-full rounded-md border-gray-300 bg-white pl-9 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
                            </div>

                            <x-secondary-button type="submit">Cari</x-secondary-button>

                            @if ($hasFilter)
                                <a href="{{ route('admin.users.index') }}"
                                   class="inline-flex items-center gap-1 rounded-md px-2 py-1.5 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900">
                                    <x-icon name="close" class="h-4 w-4" /> Reset
                                </a>
                            @endif
                        </form>

                        <div class="flex flex-wrap gap-2 lg:shrink-0 lg:flex-nowrap">
                            @can('roles.manage')
                                <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                                    <x-icon name="shield" class="h-4 w-4" /> Kelola Peran
                                </a>
                            @endcan
                            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                                + Tambah User
                            </a>
                        </div>
                    </div>

                    {{-- Filter peran: satu klik, tanpa dropdown --}}
                    <nav class="-mx-1 flex gap-1 overflow-x-auto px-1 pb-1" aria-label="Filter peran">
                        <a href="{{ route('admin.users.index', array_filter(['q' => request('q')])) }}"
                           class="{{ $pill }} {{ request('role') ? 'text-gray-600 hover:bg-gray-100' : 'bg-gray-800 text-white' }}">Semua</a>

                        @foreach ($roles as $roleName)
                            <a href="{{ route('admin.users.index', array_filter(['q' => request('q'), 'role' => $roleName])) }}"
                               class="{{ $pill }} {{ request('role') === $roleName ? 'bg-gray-800 text-white' : 'text-gray-600 hover:bg-gray-100' }}">{{ ucfirst($roleName) }}</a>
                        @endforeach
                    </nav>
                </div>

                <div class="-mx-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold">No</th>
                                <th class="px-4 py-3 text-left font-semibold">Pengguna</th>
                                <th class="px-4 py-3 text-left font-semibold">Peran</th>
                                <th class="px-4 py-3 text-left font-semibold">Terdaftar</th>
                                <th class="px-6 py-3 text-left font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($users as $user)
                                @php $isSelf = $user->is(auth()->user()); @endphp
                                <tr>
                                    <td class="px-6 py-3 text-gray-500">{{ $users->firstItem() + $loop->index }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-brand-100 text-sm font-semibold text-brand-700">
                                                {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($user->name, 0, 1)) }}
                                            </span>
                                            <div class="min-w-0">
                                                <p class="whitespace-nowrap font-medium text-gray-900">
                                                    {{ $user->name }}
                                                    @if ($isSelf)
                                                        <span class="ml-1 rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-500">Anda</span>
                                                    @endif
                                                </p>
                                                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach ($user->roles as $userRole)
                                                <x-role-badge :role="$userRole->name" />
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3 text-gray-600">{{ $user->created_at->translatedFormat('j M Y') }}</td>
                                    <td class="px-6 py-3">
                                        <div class="flex items-center gap-4 whitespace-nowrap font-medium">
                                            <a href="{{ route('admin.users.edit', $user) }}" class="text-brand-600 hover:text-brand-700 hover:underline">Edit</a>
                                            @unless ($isSelf)
                                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                                      onsubmit="return confirm({{ \Illuminate\Support\Js::from('Hapus user ' . $user->name . '?') }})">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-700 hover:underline">Hapus</button>
                                                </form>
                                            @endunless
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-14 text-center">
                                        <x-icon name="users" class="mx-auto h-8 w-8 text-gray-300" />
                                        @if ($hasFilter)
                                            <p class="mt-3 text-sm font-medium text-gray-900">Tidak ada user yang cocok</p>
                                            <p class="mt-1 text-sm text-gray-500">Coba ubah kata kunci atau peran.</p>
                                            <a href="{{ route('admin.users.index') }}" class="mt-3 inline-block text-sm font-medium text-brand-600 hover:underline">Reset filter</a>
                                        @else
                                            <p class="mt-3 text-sm font-medium text-gray-900">Belum ada data user</p>
                                        @endif
                                    </td>
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