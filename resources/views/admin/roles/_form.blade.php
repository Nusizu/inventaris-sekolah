@php
    $isEdit = isset($role);
    $builtIn = $isEdit && in_array($role->name, \App\Support\Permissions::PROTECTED_ROLES, true);
    $isAdminRole = $isEdit && $role->name === 'admin';
    $isUserRole = $isEdit && $role->name === 'user';
    $selected = old('permissions', $isEdit ? $role->permissions->pluck('name')->all() : []);
@endphp

<div class="space-y-6">
    <div>
        <x-input-label for="name">
            Nama Peran @unless ($builtIn) <span class="text-red-600">*</span> @endunless
        </x-input-label>

        @if ($builtIn)
            <x-text-input id="name" type="text" class="mt-1 block w-full bg-gray-100" :value="$role->name" disabled />
            <p class="mt-1 text-xs text-gray-500">Nama peran bawaan tidak dapat diubah.</p>
        @else
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                :value="old('name', $role->name ?? '')" placeholder="Contoh: petugas lab" required autofocus />
            <p class="mt-1 text-xs text-gray-500">Huruf kecil, angka, spasi, dan tanda hubung.</p>
        @endif
        <x-input-error :messages="$errors->get('name')" class="mt-2" />
    </div>

    <div>
        <p class="text-sm font-medium text-gray-700">Izin</p>

        @if ($isAdminRole)
            <p class="mt-1 text-xs text-gray-500">Peran Admin selalu memegang seluruh izin pengelola dan tidak dapat diubah.</p>
        @elseif ($isUserRole)
            <p class="mt-1 text-xs text-gray-500">Peran User wajib memiliki akses area peminjam dan tidak dapat masuk area pengelola.</p>
        @else
            <p class="mt-1 text-xs text-gray-500">
                Pilih tepat satu izin akses area, lalu izin fitur yang diperlukan.
                Halaman yang dibuka tanpa izin akan menampilkan 403.
            </p>
        @endif

        <x-input-error :messages="$errors->get('permissions')" class="mt-2" />

        <div class="mt-3 grid grid-cols-1 gap-4 md:grid-cols-2">
            @foreach (\App\Support\Permissions::grouped() as $group => $items)
                <fieldset class="rounded-md border border-gray-200 p-4">
                    <legend class="px-2 text-sm font-semibold text-gray-700">{{ $group }}</legend>

                    <div class="space-y-2">
                        @foreach ($items as $name => $label)
                            @php
                                $forced = $isAdminRole
                                    ? in_array($name, \App\Support\Permissions::forRole('admin'), true)
                                    : ($isUserRole && $name === 'user.access');
                                $disabled = $isAdminRole || ($isUserRole && in_array($name, ['user.access', 'admin.access'], true));
                                $checked = $forced || (! $isAdminRole && in_array($name, $selected, true));
                            @endphp

                            <label class="flex items-start gap-2 text-sm text-gray-700">
                                <input type="checkbox" name="permissions[]" value="{{ $name }}"
                                       @checked($checked) @disabled($disabled)
                                       class="mt-0.5 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                <span>
                                    {{ $label }}
                                    <span class="block font-mono text-xs text-gray-400">{{ $name }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>
            @endforeach
        </div>
    </div>
</div>

<div class="mt-6 flex items-center gap-3">
    <x-primary-button>Simpan</x-primary-button>
    <a href="{{ route('admin.roles.index') }}" class="text-sm text-gray-600 hover:text-gray-900">Batal</a>
</div>