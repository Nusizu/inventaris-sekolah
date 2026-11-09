@php
    $isSelf = isset($user) && $user->is(auth()->user());
    $currentRole = old('role', isset($user) ? ($user->roles->first()?->name ?? '') : '');
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
            Peran <span class="text-red-600">*</span>
        </x-input-label>

        @if ($isSelf)
            <input type="hidden" name="role" value="{{ $currentRole }}">
            <select id="role" disabled
                    class="mt-1 block w-full cursor-not-allowed rounded-md border-gray-300 bg-gray-100 text-sm text-gray-500 shadow-sm">
                <option>{{ ucfirst($currentRole) }}</option>
            </select>
            <p class="mt-1 text-xs text-gray-500">Peran akun yang sedang Anda gunakan tidak dapat diubah.</p>
        @else
            <select id="role" name="role" required
                    class="mt-1 block w-full rounded-md border-gray-300 text-sm shadow-sm focus:border-brand-500 focus:ring-brand-500">
                <option value="">-- Pilih Peran --</option>
                @foreach ($roles as $roleName)
                    <option value="{{ $roleName }}" @selected($currentRole === $roleName)>
                        {{ ucfirst($roleName) }}
                    </option>
                @endforeach
            </select>
        @endif
        <x-input-error :messages="$errors->get('role')" class="mt-2" />
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
        <div>
            <x-input-label for="password">
                Password @unless (isset($user)) <span class="text-red-600">*</span> @endunless
            </x-input-label>
            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full"
                placeholder="Minimal 8 karakter" autocomplete="new-password" :required="! isset($user)" />
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

    @isset($user)
        <p class="-mt-2 text-xs text-gray-500">Kosongkan password jika tidak ingin mengubahnya.</p>
    @endisset
</div>

<x-form-actions :cancel="route('admin.users.index')" />