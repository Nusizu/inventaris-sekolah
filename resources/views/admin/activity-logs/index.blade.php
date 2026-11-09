<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Activity Log</h2>
    </x-slot>

    @php
        $hasFilter = request()->anyFilled(['q', 'action', 'user_id', 'date_from', 'date_to']);
    @endphp

    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <x-breadcrumb :items="[
                'Dashboard' => route('admin.dashboard'),
                'Activity Log' => null,
            ]" />

            <x-flash-message />

            <div class="card p-6">
                {{-- [&_input]:w-full membuat kolom tanggal (flatpickr) selebar kolomnya --}}
                <form method="GET" action="{{ route('admin.activity-logs.index') }}"
                      class="mb-5 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-6 lg:items-end [&_input]:w-full">
                    <div class="relative sm:col-span-2 lg:col-span-2">
                        <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" />
                        <input type="text" name="q" value="{{ request('q') }}" autofocus
                               placeholder="Cari deskripsi atau pengguna..."
                               class="rounded-md border-gray-300 bg-white pl-9 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
                    </div>

                    <select name="action"
                            class="w-full rounded-md border-gray-300 bg-white text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 lg:col-span-2">
                        <option value="">Semua Aksi</option>
                        @foreach ($actions as $action)
                            <option value="{{ $action }}" @selected(request('action') === $action)>
                                {{ \App\Models\ActivityLog::ACTION_LABELS[$action] ?? ucwords(str_replace('_', ' ', $action)) }}
                            </option>
                        @endforeach
                    </select>

                    <select name="user_id"
                            class="w-full rounded-md border-gray-300 bg-white text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500 lg:col-span-2">
                        <option value="">Semua Pengguna</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>

                    <div class="lg:col-span-2">
                        <label for="date_from" class="mb-1 block text-xs font-medium text-gray-500">Dari tanggal</label>
                        <input type="text" id="date_from" name="date_from" value="{{ request('date_from') }}"
                               placeholder="Pilih tanggal" class="js-date-filter">
                    </div>

                    <div class="lg:col-span-2">
                        <label for="date_to" class="mb-1 block text-xs font-medium text-gray-500">Sampai tanggal</label>
                        <input type="text" id="date_to" name="date_to" value="{{ request('date_to') }}"
                               placeholder="Pilih tanggal" class="js-date-filter">
                    </div>

                    <div class="flex items-center gap-2 sm:col-span-2 lg:col-span-2">
                        <x-secondary-button type="submit">Filter</x-secondary-button>

                        @if ($hasFilter)
                            <a href="{{ route('admin.activity-logs.index') }}"
                               class="inline-flex items-center gap-1 rounded-md px-2 py-1.5 text-sm font-medium text-gray-600 hover:bg-gray-100 hover:text-gray-900">
                                <x-icon name="close" class="h-4 w-4" /> Reset
                            </a>
                        @endif
                    </div>
                </form>

                <div class="-mx-6 overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-semibold">No</th>
                                <th class="px-4 py-3 text-left font-semibold">Waktu</th>
                                <th class="px-4 py-3 text-left font-semibold">Pengguna</th>
                                <th class="px-4 py-3 text-left font-semibold">Aksi</th>
                                <th class="px-6 py-3 text-left font-semibold">Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($logs as $log)
                                @php
                                    $badge = match (\Illuminate\Support\Str::before($log->action, '_')) {
                                        'create' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                                        'update' => 'bg-sky-50 text-sky-700 ring-sky-600/20',
                                        'delete' => 'bg-red-50 text-red-700 ring-red-600/20',
                                        default => 'bg-brand-50 text-brand-700 ring-brand-600/20',
                                    };
                                @endphp
                                <tr>
                                    <td class="px-6 py-3 text-gray-500">{{ $logs->firstItem() + $loop->index }}</td>
                                    <td class="whitespace-nowrap px-4 py-3">
                                        <span class="text-gray-900">{{ $log->created_at->translatedFormat('j M Y') }}</span>
                                        <span class="block text-xs text-gray-500">{{ $log->created_at->translatedFormat('H:i') }}</span>
                                    </td>
                                    <td class="whitespace-nowrap px-4 py-3">
                                        <span class="font-medium text-gray-900">{{ $log->user->name }}</span>
                                        <span class="block text-xs text-gray-500">{{ ucfirst($log->user->role) }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex items-center whitespace-nowrap rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset {{ $badge }}">
                                            {{ $log->action_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-gray-700">{{ $log->description }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-14 text-center">
                                        <x-icon name="clock" class="mx-auto h-8 w-8 text-gray-300" />
                                        @if ($hasFilter)
                                            <p class="mt-3 text-sm font-medium text-gray-900">Tidak ada aktivitas yang cocok</p>
                                            <p class="mt-1 text-sm text-gray-500">Coba ubah kata kunci, aksi, atau rentang tanggal.</p>
                                            <a href="{{ route('admin.activity-logs.index') }}" class="mt-3 inline-block text-sm font-medium text-brand-600 hover:underline">Reset filter</a>
                                        @else
                                            <p class="mt-3 text-sm font-medium text-gray-900">Belum ada aktivitas yang tercatat</p>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $logs->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>