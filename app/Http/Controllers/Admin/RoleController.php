<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoleRequest;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Support\Permissions;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount(['permissions', 'users'])->orderBy('name')->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('admin.roles.create');
    }

    public function store(RoleRequest $request)
    {
        $role = Role::create(['name' => $request->validated('name'), 'guard_name' => 'web']);
        $role->syncPermissions($request->validated('permissions', []));

        ActivityLogger::log('create_role', "Menambahkan peran {$role->name}");

        return redirect()->route('admin.roles.index')
            ->with('success', 'Peran berhasil ditambahkan.');
    }

    public function edit(Role $role)
    {
        return view('admin.roles.edit', compact('role'));
    }

    public function update(RoleRequest $request, Role $role)
    {
        $permissions = $request->validated('permissions', []);

        if ($role->name === 'admin') {
            // Admin selalu memegang seluruh izin pengelola
            $permissions = Permissions::forRole('admin');
        } elseif ($role->name === 'user') {
            // User wajib tetap punya akses area peminjam dan tidak boleh masuk area pengelola
            $permissions = array_values(array_unique([...$permissions, 'user.access']));
            $permissions = array_values(array_diff($permissions, ['admin.access']));
        } else {
            $role->update(['name' => $request->validated('name')]);
        }

        $role->syncPermissions($permissions);

        // Kolom role milik user pemegang peran ini ikut disesuaikan
        User::role($role->name)->with('roles')->get()->each->syncRoleColumn();

        ActivityLogger::log('update_role', "Mengubah peran {$role->name}");

        return redirect()->route('admin.roles.index')
            ->with('success', 'Peran berhasil diperbarui.');
    }

    public function destroy(Role $role)
    {
        if (in_array($role->name, Permissions::PROTECTED_ROLES, true)) {
            return back()->with('error', 'Peran bawaan tidak dapat dihapus.');
        }

        if ($role->users()->exists()) {
            return back()->with('error', 'Peran tidak dapat dihapus karena masih dipakai oleh user.');
        }

        $name = $role->name;
        $role->delete();

        ActivityLogger::log('delete_role', "Menghapus peran {$name}");

        return redirect()->route('admin.roles.index')
            ->with('success', 'Peran berhasil dihapus.');
    }
}