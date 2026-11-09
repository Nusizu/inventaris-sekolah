<section class="grid gap-6 lg:grid-cols-3">
    <header>
        <h2 class="text-base font-semibold text-gray-900">Ubah Password</h2>
        <p class="mt-1 text-sm text-gray-500">Gunakan password yang panjang dan sulit ditebak agar akun tetap aman.</p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-5 lg:col-span-2">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password">
                Password Saat Ini <span class="text-red-600">*</span>
            </x-input-label>
            <x-text-input id="update_password_current_password" name="current_password" type="password"
                class="mt-1 block w-full" placeholder="Masukkan password saat ini"
                required autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
            <div>
                <x-input-label for="update_password_password">
                    Password Baru <span class="text-red-600">*</span>
                </x-input-label>
                <x-text-input id="update_password_password" name="password" type="password"
                    class="mt-1 block w-full" placeholder="Minimal 8 karakter"
                    required autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="update_password_password_confirmation">
                    Konfirmasi Password Baru <span class="text-red-600">*</span>
                </x-input-label>
                <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password"
                    class="mt-1 block w-full" placeholder="Ulangi password baru"
                    required autocomplete="new-password" />
                <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Simpan</x-primary-button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition
                   x-init="setTimeout(() => show = false, 2500)"
                   class="inline-flex items-center gap-1.5 text-sm font-medium text-emerald-700">
                    <x-icon name="check-circle" class="h-4 w-4" /> Tersimpan.
                </p>
            @endif
        </div>
    </form>
</section>