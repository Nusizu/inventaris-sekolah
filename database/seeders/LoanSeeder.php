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
        ];

        foreach ($loans as [$user, $code, $qty, $loanDaysAgo, $dueDaysAgo, $status]) {
            $item = Item::where('code', $code)->first();

            Loan::create([
                'user_id' => $user->id,
                'item_id' => $item->id,
                'quantity' => $qty,
                'loan_date' => now()->subDays($loanDaysAgo),
                'due_date' => now()->subDays($dueDaysAgo),
                'status' => $status,
            ]);

            // Stok hanya berkurang untuk barang yang belum dikembalikan
            if ($status !== 'dikembalikan') {
                $item->decrement('stock', $qty);
            }
        }
    }
}