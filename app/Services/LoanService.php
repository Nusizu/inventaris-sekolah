<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Loan;
use App\Models\ReturnLog;
use App\Models\User;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LoanService
{
    /**
     * Peminjaman langsung oleh Admin: dianggap sudah disetujui, stok langsung dipotong.
     */
    public function create(array $data): Loan
    {
        return DB::transaction(function () use ($data) {
            $item = Item::lockForUpdate()->findOrFail($data['item_id']);

            $this->guardBorrowable($item, (int) $data['quantity']);

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

    /**
     * Pengajuan oleh User: status menunggu, stok belum dipotong.
     */
    public function request(array $data): Loan
    {
        $item = Item::findOrFail($data['item_id']);

        $this->guardBorrowable($item, (int) $data['quantity']);

        $loan = Loan::create([
            'user_id' => $data['user_id'],
            'item_id' => $item->id,
            'quantity' => $data['quantity'],
            'loan_date' => $data['loan_date'],
            'due_date' => $data['due_date'],
            'status' => 'menunggu',
        ]);

        return $loan->load(['user', 'item']);
    }

    /**
     * Admin menyetujui pengajuan: stok dicek ulang dan dipotong.
     */
    public function approve(Loan $loan, User $admin): Loan
    {
        return DB::transaction(function () use ($loan, $admin) {
            $loan = Loan::lockForUpdate()->findOrFail($loan->id);

            if ($loan->status !== 'menunggu') {
                throw new DomainException('Pengajuan ini sudah diproses sebelumnya.');
            }

            $item = Item::lockForUpdate()->find($loan->item_id);

            if (! $item) {
                throw new DomainException('Barang pada pengajuan ini sudah tidak tersedia.');
            }

            if ($item->condition === 'rusak berat') {
                throw new DomainException('Barang berkondisi rusak berat tidak dapat dipinjam.');
            }

            if ($loan->quantity > $item->stock) {
                throw new DomainException(
                    'Stok tidak mencukupi. Stok tersedia: ' . number_format($item->stock, 0, ',', '.') . '.'
                );
            }

            $item->decrement('stock', $loan->quantity);

            $loan->update([
                'status' => 'dipinjam',
                'decided_by' => $admin->id,
                'decided_at' => now(),
            ]);

            return $loan->load(['user', 'item']);
        });
    }

    /**
     * Admin menolak pengajuan beserta alasannya. Stok tidak berubah.
     */
    public function reject(Loan $loan, User $admin, string $note): Loan
    {
        return DB::transaction(function () use ($loan, $admin, $note) {
            $loan = Loan::lockForUpdate()->findOrFail($loan->id);

            if ($loan->status !== 'menunggu') {
                throw new DomainException('Pengajuan ini sudah diproses sebelumnya.');
            }

            $loan->update([
                'status' => 'ditolak',
                'decided_by' => $admin->id,
                'decided_at' => now(),
                'decision_note' => $note,
            ]);

            return $loan->load(['user', 'item']);
        });
    }

    /**
     * User membatalkan pengajuannya sendiri yang masih menunggu.
     */
    public function cancel(Loan $loan): Loan
    {
        return DB::transaction(function () use ($loan) {
            $loan = Loan::lockForUpdate()->findOrFail($loan->id);

            if ($loan->status !== 'menunggu') {
                throw new DomainException('Hanya pengajuan yang masih menunggu yang dapat dibatalkan.');
            }

            $loan->update(['status' => 'dibatalkan']);

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

            if (! $loan->isActive()) {
                throw ValidationException::withMessages([
                    'returned_at' => 'Hanya peminjaman yang sedang berjalan yang dapat dikembalikan.',
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

    /**
     * Soft delete peminjaman. Stok dikembalikan hanya jika stoknya sempat dipotong.
     */
    public function deleteLoan(Loan $loan): bool
    {
        return DB::transaction(function () use ($loan) {
            $loan = Loan::lockForUpdate()->findOrFail($loan->id);
            $stockRestored = false;

            if ($loan->isActive()) {
                Item::withTrashed()->lockForUpdate()
                    ->findOrFail($loan->item_id)
                    ->increment('stock', $loan->quantity);

                $stockRestored = true;
            }

            $loan->returnLog?->delete();
            $loan->delete();

            return $stockRestored;
        });
    }

    private function guardBorrowable(Item $item, int $quantity): void
    {
        if ($item->condition === 'rusak berat') {
            throw ValidationException::withMessages([
                'item_id' => 'Barang dengan kondisi rusak berat tidak dapat dipinjam.',
            ]);
        }

        if ($quantity > $item->stock) {
            throw ValidationException::withMessages([
                'quantity' => 'Stok tidak mencukupi. Stok tersedia: '
                    . number_format($item->stock, 0, ',', '.') . '.',
            ]);
        }
    }
}