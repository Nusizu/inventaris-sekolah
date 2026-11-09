<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Notifikasi</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl sm:px-6 lg:px-8">
            <x-breadcrumb :items="[
                'Dashboard' => route(auth()->user()->isAdmin() ? 'admin.dashboard' : 'user.dashboard'),
                'Notifikasi' => null,
            ]" />

            <x-flash-message />

            <div class="card overflow-hidden">
                <div class="flex items-center justify-between gap-3 border-b border-gray-100 px-6 py-4">
                    <p class="text-sm text-gray-500">
                        Belum dibaca: <span class="font-semibold text-gray-900">{{ number_format($unreadCount, 0, ',', '.') }}</span>
                    </p>

                    @if ($unreadCount > 0)
                        <form method="POST" action="{{ route('notifications.read-all') }}">
                            @csrf
                            <button type="submit" class="text-sm font-medium text-brand-600 hover:text-brand-700 hover:underline">Tandai semua dibaca</button>
                        </form>
                    @endif
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse ($notifications as $notification)
                        <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                            @csrf
                            <button type="submit"
                                    class="flex w-full items-start gap-3 px-6 py-4 text-left transition-colors hover:bg-gray-50 {{ $notification->read_at ? '' : 'bg-brand-50/60' }}">
                                <span class="mt-1.5 h-2 w-2 shrink-0 rounded-full {{ $notification->read_at ? 'bg-transparent' : 'bg-brand-500' }}"></span>

                                <span class="min-w-0 flex-1">
                                    <span class="flex items-start justify-between gap-3">
                                        <span class="text-sm font-medium text-gray-900">{{ $notification->data['title'] ?? 'Notifikasi' }}</span>
                                        <span class="shrink-0 text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
                                    </span>
                                    <span class="mt-1 block text-sm text-gray-600">{{ $notification->data['message'] ?? '' }}</span>
                                </span>
                            </button>
                        </form>
                    @empty
                        <div class="px-6 py-14 text-center">
                            <x-icon name="bell" class="mx-auto h-8 w-8 text-gray-300" />
                            <p class="mt-3 text-sm font-medium text-gray-900">Belum ada notifikasi</p>
                            <p class="mt-1 text-sm text-gray-500">Pemberitahuan tentang peminjaman Anda akan muncul di sini.</p>
                        </div>
                    @endforelse
                </div>

                @if ($notifications->hasPages())
                    <div class="border-t border-gray-100 px-6 py-4">
                        {{ $notifications->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>