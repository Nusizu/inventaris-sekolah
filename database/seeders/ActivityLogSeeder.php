<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class ActivityLogSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $user1 = User::where('email', 'user1@inventaris.test')->first();
        $user2 = User::where('email', 'user2@inventaris.test')->first();

        $logs = [
            [$admin, 'create_category', 'Menambahkan kategori Alat Lab', 25],
            [$admin, 'create_item', 'Menambahkan barang Mikroskop Binokuler (LAB-001)', 24],
            [$user1, 'create_loan', 'Mengajukan peminjaman Bola Basket sebanyak 2', 10],
            [$admin, 'verify_loan', 'Memverifikasi peminjaman Bola Basket oleh User Satu', 10],
            [$admin, 'return_loan', 'Memproses pengembalian Bola Basket dalam kondisi baik', 4],
            [$user2, 'create_loan', 'Mengajukan peminjaman Proyektor Epson sebanyak 1', 2],
            [$admin, 'update_item', 'Mengubah kondisi barang Bola Voli menjadi rusak ringan', 1],
        ];

        foreach ($logs as [$user, $action, $description, $daysAgo]) {
            ActivityLog::forceCreate([
                'user_id' => $user->id,
                'action' => $action,
                'description' => $description,
                'created_at' => now()->subDays($daysAgo),
                'updated_at' => now()->subDays($daysAgo),
            ]);
        }
    }
}