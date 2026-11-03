<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Seeder;

class LoanSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $user1 = User::where('email', 'user1@inventaris.test')->first();
        $user2 = User::where('email', 'user2@inventaris.test')->first();

        $loans = [
            // [user, kode barang, jumlah, hari lalu (pinjam), hari lalu (jatuh tempo), status]
            [$user1, 'OLR-001', 2, 10, 3, 'dikembalikan'],
            [$user2, 'OLR-002', 2, 8, 1, 'dikembalikan'],
            [$user1, 'LAB-001', 1, 15, 8, 'dikembalikan'],
            [$user2, 'ELK-001', 1, 2, -5, 'dipinjam'],
            [$user1, 'ELK-002', 1, 1, -6, 'dipinjam'],
            [$user2, 'OLR-003', 1, 20, 6, 'terlambat'],
            [$user1, 'OLR-004', 2, 0, -7, 'menunggu'],
            [$user2, 'LAB-002', 5, 0, -3, 'ditolak'],
        ];

        foreach ($loans as [$user, $code, $qty, $loanDaysAgo, $dueDaysAgo, $status]) {
            $item = Item::where('code', $code)->first();
            $rejected = $status === 'ditolak';

            Loan::create([
                'user_id' => $user->id,
                'item_id' => $item->id,
                'quantity' => $qty,
                'loan_date' => now()->subDays($loanDaysAgo),
                'due_date' => now()->subDays($dueDaysAgo),
                'status' => $status,
                'decided_by' => $rejected ? $admin->id : null,
                'decided_at' => $rejected ? now() : null,
                'decision_note' => $rejected ? 'Stok dibutuhkan untuk kegiatan praktikum minggu ini.' : null,
            ]);

            // Stok hanya berkurang untuk peminjaman yang sedang berjalan
            if (in_array($status, Loan::ACTIVE_STATUSES, true)) {
                $item->decrement('stock', $qty);
            }
        }
    }
}