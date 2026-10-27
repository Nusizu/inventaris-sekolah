@php
    $isSelf = isset($user) && $user->is(auth()->user());
@endphp

<div class="space-y-5">
    <div>
        <x-input-label for="name">
            Nama <span class="text-red-600">*</span>
        </x-input-label>
        <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
            :value="old('name', $user->name ?? '')" placeholder="Contoh: Budi Santoso" required autofocus />
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="email">
            Email <span class="text-red-600">*</span>
        </x-input-label>
        <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
            :value="old('email', $user->email ?? '')" placeholder="Contoh: budi@sekolah.test" required />
        <x-input-error :messages="$errors->get('email')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="role">
            Role <span class="text-red-600">*</span>
        </x-input-label>

        @if ($isSelf)
            <input type="hidden" name="role" value="{{ $user->role }}">
            <select id="role" disabled
                    class="mt-1 block w-full cursor-not-allowed rounded-md border-gray-300 bg-gray-100 shadow-sm">
                <option>{{ ucfirst($user->role) }}</option>
            </select>
            <p class="mt-1 text-xs text-gray-500">Role akun yang sedang Anda gunakan tidak dapat diubah.</p>
        @else
            <select id="role" name="role" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">-- Pilih Role --</option>
                @foreach (['admin' => 'Admin', 'user' => 'User'] as $value => $label)
                    <option value="{{ $value }}" @selected(old('role', $user->role ?? '') === $value)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        @endif
        <x-input-error :messages="$errors->get('role')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="password">
            Password @unless (isset($user)) <span class="text-red-600">*</span> @endunless
        </x-input-label>
        <x-text-input id="password" name="password" type="password" class="mt-1 block w-full"
            placeholder="Minimal 8 karakter" autocomplete="new-password" :required="! isset($user)" />
        @isset($user)
            <p class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ingin mengubah password.</p>
        @endisset
        <x-input-error :messages="$errors->get('password')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="password_confirmation">
            Konfirmasi Password @unless (isset($user)) <span class="text-red-600">*</span> @endunless
        </x-input-label>
        <x-text-input id="password_confirmation" name="password_confirmation" type="password"
            class="mt-1 block w-full" placeholder="Ulangi password" autocomplete="new-password"
            :required="! isset($user)" />
    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <x-primary-button>Simpan</x-primary-button>
    <a href="{{ route('admin.users.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Batal</a>
</div>