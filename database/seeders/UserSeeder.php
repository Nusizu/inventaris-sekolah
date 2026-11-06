<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Administrator', 'email' => 'admin@inventaris.test', 'role' => 'admin'],
            ['name' => 'User Satu', 'email' => 'user1@inventaris.test', 'role' => 'user'],
            ['name' => 'User Dua', 'email' => 'user2@inventaris.test', 'role' => 'user'],
            ['name' => 'Petugas Inventaris', 'email' => 'petugas@inventaris.test', 'role' => 'admin'],
        ];

        foreach ($users as $user) {
            User::create([
                'name' => $user['name'],
                'email' => $user['email'],
                'password' => Hash::make('password'),
                'role' => $user['role'],
            ]);
        }
    }
}