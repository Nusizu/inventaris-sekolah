<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\Loan;
use App\Models\ReturnLog;
use App\Models\User;
use Illuminate\Database\Seeder;

class LoanHistorySeeder extends Seeder
{
    public function run(): void
    {
        $users = User::where('role', 'user')->orderBy('id')->get();
        $items = Item::orderBy('id')->get();

        // [berapa bulan lalu, tanggal dalam bulan, jumlah unit]
        $history = [
            [5, 3, 2], [5, 18, 1],
            [4, 5, 1], [4, 12, 3], [4, 25, 2],
            [3, 2, 2], [3, 9, 1], [3, 16, 2], [3, 27, 1],
            [2, 7, 1], [2, 14, 4], [2, 21, 2],
            [1, 4, 2], [1, 10, 1], [1, 15, 3], [1, 22, 1], [1, 24, 2],
        ];

        foreach ($history as $i => [$monthsAgo, $day, $qty]) {
            $user = $users[$i % $users->count()];
            $item = $items[$i % $items->count()];

            $loanDate = now()->subMonthsNoOverflow($monthsAgo)->startOfMonth()->addDays($day - 1);

            $loan = Loan::create([
                'user_id' => $user->id,
                'item_id' => $item->id,
                'quantity' => $qty,
                'loan_date' => $loanDate,
                'due_date' => $loanDate->copy()->addDays(7),
                'status' => 'dikembalikan',
            ]);

            ReturnLog::create([
                'loan_id' => $loan->id,
                'returned_at' => $loanDate->copy()->addDays(5),
                'condition_on_return' => 'baik',
                'notes' => null,
            ]);
        }
    }
}