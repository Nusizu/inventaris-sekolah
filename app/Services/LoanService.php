<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Loan;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LoanService
{
    public function create(array $data): Loan
    {
        return DB::transaction(function () use ($data) {
            $item = Item::lockForUpdate()->findOrFail($data['item_id']);

            if ($item->condition === 'rusak berat') {
                throw ValidationException::withMessages([
                    'item_id' => 'Barang dengan kondisi rusak berat tidak dapat dipinjam.',
                ]);
            }

            if ($data['quantity'] > $item->stock) {
                throw ValidationException::withMessages([
                    'quantity' => 'Stok tidak mencukupi. Stok tersedia: '
                        . number_format($item->stock, 0, ',', '.') . '.',
                ]);
            }

            $item->decrement('stock', $data['quantity']);

            $loan = Loan::create([
                'user_id' => $data['user_id'],
                'item_id' => $item->id,
                'quantity' => $data['quantity'],
                'loan_date' => $data['loan_date'],
                'due_date' => $data['due_date'],
                'status' => 'dipinjam',
            ]);

            return $loan->load(['user', 'item']);
        });
    }
}