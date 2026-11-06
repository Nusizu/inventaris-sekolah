<?php

namespace App\Http\Requests;

use App\Support\Permissions;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class RoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // akses sudah dibatasi middleware permission:roles.manage
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('name'))) {
            $this->merge(['name' => Str::lower(trim($this->input('name')))]);
        }
    }

    public function rules(): array
    {
        $role = $this->route('role');
        $builtIn = $role && in_array($role->name, Permissions::PROTECTED_ROLES, true);

        return [
            'name' => $builtIn
                ? ['nullable']
                : [
                    'required',
                    'string',
                    'max:50',
                    'regex:/^[a-z0-9][a-z0-9 \-]*$/',
                    Rule::unique('roles', 'name')->ignore($role?->id),
                ],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', Rule::in(array_keys(Permissions::all()))],
        ];
    }

    /** Peran kustom harus memilih tepat satu izin akses area. */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $role = $this->route('role');

                if ($role && in_array($role->name, Permissions::PROTECTED_ROLES, true)) {
                    return;
                }

                $permissions = (array) $this->input('permissions', []);
                $hasAdmin = in_array('admin.access', $permissions, true);
                $hasUser = in_array('user.access', $permissions, true);

                if ($hasAdmin === $hasUser) {
                    $validator->errors()->add(
                        'permissions',
                        'Pilih salah satu izin akses area: area pengelola atau area peminjam.'
                    );
                }
            },
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'nama peran',
            'permissions' => 'izin',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama peran wajib diisi.',
            'name.max' => 'Nama peran maksimal 50 karakter.',
            'name.regex' => 'Nama peran hanya boleh berisi huruf, angka, spasi, dan tanda hubung.',
            'name.unique' => 'Nama peran sudah digunakan.',
            'permissions.*.in' => 'Izin yang dipilih tidak valid.',
        ];
    }
}