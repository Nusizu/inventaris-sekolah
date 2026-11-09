<x-guest-layout>
    <x-slot name="heading">Buat akun</x-slot>
    <x-slot name="subheading">Daftar untuk mulai mengajukan peminjaman barang.</x-slot>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <!-- Nama -->
        <div>
            <x-input-label for="name">
                Nama <span class="text-red-600">*</span>
            </x-input-label>
            <x-text-input id="name" class="mt-1 block w-full" type="text" name="name"
                :value="old('name')" placeholder="Nama lengkap"
                required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email -->
        <div>
            <x-input-label for="email">
                Email <span class="text-red-600">*</span>
            </x-input-label>
            <x-text-input id="email" class="mt-1 block w-full" type="email" name="email"
                :value="old('email')" placeholder="Contoh: nama@email.com"
                required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password">
                Password <span class="text-red-600">*</span>
            </x-input-label>
            <x-text-input id="password" class="mt-1 block w-full" type="password" name="password"
                placeholder="Minimal 8 karakter" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Konfirmasi Password -->
        <div>
            <x-input-label for="password_confirmation">
                Konfirmasi Password <span class="text-red-600">*</span>
            </x-input-label>
            <x-text-input id="password_confirmation" class="mt-1 block w-full" type="password"
                name="password_confirmation" placeholder="Ulangi password"
                required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <x-primary-button class="w-full">Daftar</x-primary-button>
    </form>

    <p class="mt-6 text-center text-sm text-gray-500">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-medium text-brand-600 hover:text-brand-700 hover:underline">Masuk</a>
    </p>
</x-guest-layout>