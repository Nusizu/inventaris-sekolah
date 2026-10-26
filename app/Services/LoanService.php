<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Loan;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\ReturnLog;

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

    public function returnLoan(Loan $loan, array $data): ReturnLog
    {
        return DB::transaction(function () use ($loan, $data) {
            $loan = Loan::lockForUpdate()->findOrFail($loan->id);

            if ($loan->status === 'dikembalikan' || $loan->returnLog()->withTrashed()->exists()) {
                throw ValidationException::withMessages([
                    'returned_at' => 'Peminjaman ini sudah dikembalikan sebelumnya.',
                ]);
            }

            $returnLog = ReturnLog::create([
                'loan_id' => $loan->id,
                'returned_at' => $data['returned_at'],
                'condition_on_return' => $data['condition_on_return'],
                'notes' => $data['notes'] ?? null,
            ]);

            $loan->update(['status' => 'dikembalikan']);

            Item::withTrashed()->lockForUpdate()
                ->findOrFail($loan->item_id)
                ->increment('stock', $loan->quantity);

            return $returnLog->load(['loan.user', 'loan.item']);
        });
    }
}