<?php

namespace Database\Seeders;

use App\Models\Loan;
use App\Models\ReturnLog;
use Illuminate\Database\Seeder;

class ReturnLogSeeder extends Seeder
{
    public function run(): void
    {
        $returns = [
            ['OLR-001', 4, 'baik', 'Dikembalikan tepat waktu.'],
            ['OLR-002', 2, 'baik', null],
            ['LAB-001', 9, 'rusak ringan', 'Lensa objektif tergores.'],
        ];

        foreach ($returns as [$code, $daysAgo, $condition, $notes]) {
            $loan = Loan::where('status', 'dikembalikan')
                ->whereHas('item', fn ($q) => $q->where('code', $code))
                ->first();

            ReturnLog::create([
                'loan_id' => $loan->id,
                'returned_at' => now()->subDays($daysAgo),
                'condition_on_return' => $condition,
                'notes' => $notes,
            ]);
        }
    }
}