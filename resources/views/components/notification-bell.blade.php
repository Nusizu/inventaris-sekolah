<div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
    <button type="button" @click="open = ! open"
            class="relative rounded-md p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700 focus:outline-none"
            aria-label="Notifikasi">
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
        </svg>

        @if ($unreadCount > 0)
            <span class="absolute -right-0.5 -top-0.5 inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-red-600 px-1 text-xs font-semibold text-white">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div x-show="open" x-cloak x-transition
         class="absolute right-0 z-50 mt-2 w-80 max-w-[90vw] overflow-hidden rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5">
        <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
            <span class="text-sm font-semibold text-gray-800">Notifikasi</span>

            @if ($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="text-xs text-indigo-600 hover:underline">Tandai semua dibaca</button>
                </form>
            @endif
        </div>

        <div class="max-h-96 divide-y divide-gray-100 overflow-y-auto">
            @forelse ($latestNotifications as $notification)
                <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                    @csrf
                    <button type="submit"
                            class="block w-full px-4 py-3 text-left hover:bg-gray-50 {{ $notification->read_at ? '' : 'bg-indigo-50' }}">
                        <span class="block text-sm font-medium text-gray-800">{{ $notification->data['title'] ?? 'Notifikasi' }}</span>
                        <span class="mt-0.5 block text-xs text-gray-600">{{ \Illuminate\Support\Str::limit($notification->data['message'] ?? '', 90) }}</span>
                        <span class="mt-1 block text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
                    </button>
                </form>
            @empty
                <p class="px-4 py-6 text-center text-sm text-gray-500">Belum ada notifikasi.</p>
            @endforelse
        </div>

        <a href="{{ route('notifications.index') }}"
           class="block border-t border-gray-100 px-4 py-3 text-center text-xs font-medium text-indigo-600 hover:bg-gray-50">
            Lihat semua notifikasi
        </a>
    </div>
</div>