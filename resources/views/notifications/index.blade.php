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

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <p class="text-sm text-gray-600">
                        Belum dibaca: <span class="font-semibold">{{ number_format($unreadCount, 0, ',', '.') }}</span>
                    </p>

                    @if ($unreadCount > 0)
                        <form method="POST" action="{{ route('notifications.read-all') }}">
                            @csrf
                            <button type="submit" class="text-sm text-indigo-600 hover:underline">Tandai semua dibaca</button>
                        </form>
                    @endif
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse ($notifications as $notification)
                        <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                            @csrf
                            <button type="submit"
                                    class="block w-full px-6 py-4 text-left hover:bg-gray-50 {{ $notification->read_at ? '' : 'bg-indigo-50' }}">
                                <span class="flex items-center justify-between gap-3">
                                    <span class="text-sm font-medium text-gray-800">{{ $notification->data['title'] ?? 'Notifikasi' }}</span>
                                    @unless ($notification->read_at)
                                        <span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-700">Baru</span>
                                    @endunless
                                </span>
                                <span class="mt-1 block text-sm text-gray-600">{{ $notification->data['message'] ?? '' }}</span>
                                <span class="mt-1 block text-xs text-gray-400">{{ $notification->created_at->diffForHumans() }}</span>
                            </button>
                        </form>
                    @empty
                        <p class="px-6 py-10 text-center text-sm text-gray-500">Belum ada notifikasi.</p>
                    @endforelse
                </div>

                <div class="border-t border-gray-100 px-6 py-4">
                    {{ $notifications->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>