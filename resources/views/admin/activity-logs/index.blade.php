<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Activity Log</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <x-breadcrumb :items="[
                'Dashboard' => route('admin.dashboard'),
                'Activity Log' => null,
            ]" />

            <x-flash-message />

            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form method="GET" action="{{ route('admin.activity-logs.index') }}"
                      class="mb-4 flex flex-wrap items-end gap-2">
                    <input type="text" name="q" value="{{ request('q') }}" autofocus
                           placeholder="Cari deskripsi atau pengguna..."
                           class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">

                    <select name="action"
                            class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Semua Aksi</option>
                        @foreach ($actions as $action)
                            <option value="{{ $action }}" @selected(request('action') === $action)>
                                {{ \App\Models\ActivityLog::ACTION_LABELS[$action] ?? ucwords(str_replace('_', ' ', $action)) }}
                            </option>
                        @endforeach
                    </select>

                    <select name="user_id"
                            class="rounded-md border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Semua Pengguna</option>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}" @selected(request('user_id') == $user->id)>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>

                    <div>
                        <label for="date_from" class="block text-xs text-gray-500">Dari tanggal</label>
                        <input type="text" id="date_from" name="date_from" value="{{ request('date_from') }}"
                               placeholder="Pilih tanggal" class="js-date-filter">
                    </div>

                    <div>
                        <label for="date_to" class="block text-xs text-gray-500">Sampai tanggal</label>
                        <input type="text" id="date_to" name="date_to" value="{{ request('date_to') }}"
                               placeholder="Pilih tanggal" class="js-date-filter">
                    </div>

                    <x-secondary-button type="submit">Filter</x-secondary-button>

                    @if (request()->hasAny(['q', 'action', 'user_id', 'date_from', 'date_to']))
                        <a href="{{ route('admin.activity-logs.index') }}"
                           class="self-center text-sm text-gray-600 hover:text-gray-900">Reset</a>
                    @endif
                </form>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">No</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Waktu</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Pengguna</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Aksi</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Deskripsi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($logs as $log)
                                @php
                                    $badge = match (\Illuminate\Support\Str::before($log->action, '_')) {
                                        'create' => 'bg-green-100 text-green-800',
                                        'update' => 'bg-blue-100 text-blue-800',
                                        'delete' => 'bg-red-100 text-red-800',
                                        default => 'bg-indigo-100 text-indigo-800',
                                    };
                                @endphp
                                <tr>
                                    <td class="px-4 py-3">{{ $logs->firstItem() + $loop->index }}</td>
                                    <td class="whitespace-nowrap px-4 py-3">{{ $log->created_at->translatedFormat('j M Y, H:i') }}</td>
                                    <td class="px-4 py-3">
                                        {{ $log->user->name }}
                                        <span class="block text-xs text-gray-400">{{ ucfirst($log->user->role) }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="whitespace-nowrap rounded-full px-2 py-1 text-xs font-medium {{ $badge }}">
                                            {{ $log->action_label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">{{ $log->description }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-6 text-center text-gray-500">Belum ada aktivitas yang tercatat.</td>
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