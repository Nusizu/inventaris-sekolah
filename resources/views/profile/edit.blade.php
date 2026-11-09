<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">Profil Saya</h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-3xl space-y-6 sm:px-6 lg:px-8">
            <x-breadcrumb :items="[
                'Dashboard' => route(auth()->user()->isAdmin() ? 'admin.dashboard' : 'user.dashboard'),
                'Profil' => null,
            ]" />

            {{-- Ringkasan akun --}}
            <div class="card flex items-center gap-4 p-6">
                <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-brand-100 text-xl font-semibold text-brand-700">
                    {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr(auth()->user()->name, 0, 1)) }}
                </span>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-lg font-semibold text-gray-900">{{ auth()->user()->name }}</p>
                    <p class="truncate text-sm text-gray-500">{{ auth()->user()->email }}</p>
                </div>
                {{-- Nama peran dari Spatie (mis. petugas). Kolom users.role hanya berisi area admin/user, jadi dipakai sebagai cadangan. --}}
                <x-role-badge :role="$user->roles->first()?->name ?? $user->role" />
            </div>

            <div class="card p-6 sm:p-8">
                @include('profile.partials.update-profile-information-form')
            </div>

            <div class="card p-6 sm:p-8">
                @include('profile.partials.update-password-form')
            </div>
        </div>
    </div>
</x-app-layout>