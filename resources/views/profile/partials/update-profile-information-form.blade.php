<section class="grid gap-6 lg:grid-cols-3">
    <header>
        <h2 class="text-base font-semibold text-gray-900">Informasi Profil</h2>
        <p class="mt-1 text-sm text-gray-500">Perbarui nama dan alamat email akun Anda.</p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-5 lg:col-span-2">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name">
                Nama <span class="text-red-600">*</span>
            </x-input-label>
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                :value="old('name', $user->name)" placeholder="Nama lengkap"
                required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email">
                Email <span class="text-red-600">*</span>
            </x-input-label>
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                :value="old('email', $user->email)" placeholder="Contoh: nama@email.com"
                required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Simpan</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition
                   x-init="setTimeout(() => show = false, 2500)"
                   class="inline-flex items-center gap-1.5 text-sm font-medium text-emerald-700">
                    <x-icon name="check-circle" class="h-4 w-4" /> Tersimpan.
                </p>
            @endif
        </div>
    </form>
</section>