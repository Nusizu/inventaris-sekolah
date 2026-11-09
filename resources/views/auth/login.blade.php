<x-guest-layout>
    <x-slot name="heading">Masuk</x-slot>
    <x-slot name="subheading">Gunakan akun Anda untuk mengelola dan meminjam inventaris sekolah.</x-slot>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email -->
        <div>
            <x-input-label for="email">
                Email <span class="text-red-600">*</span>
            </x-input-label>
            <x-text-input id="email" class="mt-1 block w-full" type="email" name="email"
                :value="old('email')" placeholder="Contoh: nama@email.com"
                required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password (dengan tombol tampilkan/sembunyikan) -->
        <div x-data="{ show: false }">
            <x-input-label for="password">
                Password <span class="text-red-600">*</span>
            </x-input-label>
            <div class="relative mt-1">
                <x-text-input id="password" class="block w-full pr-10" name="password"
                    ::type="show ? 'text' : 'password'" type="password"
                    placeholder="Masukkan password" required autocomplete="current-password" />
                <button type="button" @click="show = ! show"
                        class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600"
                        :aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'">
                    <x-icon name="eye" class="h-5 w-5" x-show="! show" />
                    <x-icon name="eye-off" class="h-5 w-5" x-show="show" x-cloak />
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <label for="remember_me" class="inline-flex items-center gap-2">
            <input id="remember_me" type="checkbox" name="remember" class="rounded">
            <span class="text-sm text-gray-600">Ingat saya</span>
        </label>

        <x-primary-button class="w-full">Masuk</x-primary-button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-500">
        Belum punya akun?
        <a href="{{ route('register') }}" class="font-medium text-brand-600 hover:text-brand-700 hover:underline">Daftar</a>
    </p>
</x-guest-layout>