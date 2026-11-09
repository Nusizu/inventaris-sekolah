<div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
    <button type="button" @click="open = ! open"
            class="relative rounded-md p-2 text-gray-500 transition-colors hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500"
            aria-label="Notifikasi" :aria-expanded="open">
        <x-icon name="bell" class="h-6 w-6" />

        @if ($unreadCount > 0)
            <span class="absolute right-1 top-1 inline-flex h-4 min-w-[1rem] items-center justify-center rounded-full bg-red-600 px-1 text-[10px] font-semibold leading-none text-white ring-2 ring-white">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    <div x-show="open" x-cloak
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 z-50 mt-2 w-80 max-w-[90vw] origin-top-right overflow-hidden rounded-lg bg-white shadow-lg">
        <div class="flex items-center justify-between border-b border-gray-100 px-4 py-3">
            <span class="text-sm font-semibold text-gray-900">Notifikasi</span>

            @if ($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="text-xs font-medium text-brand-600 hover:text-brand-700 hover:underline">Tandai semua dibaca</button>
                </form>
            @endif
        </div>

        <div class="max-h-96 divide-y divide-gray-100 overflow-y-auto">
            @forelse ($latestNotifications as $notification)
                <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                    @csrf
                    <button type="submit"
                            class="flex w-full items-start gap-3 px-4 py-3 text-left transition-colors hover:bg-gray-50 {{ $notification->read_at ? '' : 'bg-brand-50/60' }}">
                        <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full {{ $notification->read_at ? 'bg-transparent' : 'bg-brand-500' }}"></span>
                        <span class="min-w-0 flex-1">
                            <span class="block text-sm font-medium text-gray-900">{{ $notification->data['title'] ?? 'Notifikasi' }}</span>
                            <span class="mt-0.5 block text-xs text-gray-600">{{ \Illuminate\Support\Str::limit($notification->data['message'] ?? '', 90) }}</span>
                            <span class="mt-1 block text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
                        </span>
                    </button>
                </form>
            @empty
                <p class="px-4 py-8 text-center text-sm text-gray-500">Belum ada notifikasi.</p>
            @endforelse
        </div>

        <a href="{{ route('notifications.index') }}"
           class="block border-t border-gray-100 px-4 py-3 text-center text-xs font-medium text-brand-600 transition-colors hover:bg-gray-50">
            Lihat semua notifikasi
        </a>
    </div>
</div>