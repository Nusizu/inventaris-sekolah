<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\Permissions;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (array_keys(Permissions::all()) as $name) {
            Permission::findOrCreate($name, 'web');
        }

        foreach (['admin', 'petugas', 'user'] as $roleName) {
            Role::findOrCreate($roleName, 'web')->syncPermissions(Permissions::forRole($roleName));
        }

        // Akun yang sudah ada diberi peran sesuai kolom role
        User::with('roles')->get()->each(function (User $user) {
            if ($user->roles->isEmpty()) {
                $user->assignRole($user->role === 'admin' ? 'admin' : 'user');
            }
        });

        // Akun contoh dengan peran terbatas
        User::where('email', 'petugas@inventaris.test')->first()?->syncRoles(['petugas']);
    }
}