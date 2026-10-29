<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoanRequest;
use App\Models\Item;
use App\Models\Loan;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\LoanService;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        Loan::markOverdue();

        $loans = Loan::with(['user', 'item'])
            ->when($request->q, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('item', fn ($i) => $i->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->orderByDesc('loan_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.loans.index', compact('loans'));
    }

    public function create()
    {
        $users = User::where('role', 'user')->orderBy('name')->get();
        $items = Item::available()->orderBy('name')->get();

        return view('admin.loans.create', compact('users', 'items'));
    }

    public function store(LoanRequest $request, LoanService $service)
    {
        $loan = $service->create($request->validated());

        ActivityLogger::log(
            'create_loan',
            'Menambahkan peminjaman ' . $loan->item->name . ' sebanyak '
                . number_format($loan->quantity, 0, ',', '.') . ' untuk ' . $loan->user->name
        );

        return redirect()->route('admin.loans.index')
            ->with('success', 'Peminjaman berhasil ditambahkan dan stok barang diperbarui.');
    }

    public function destroy(Loan $loan, LoanService $service)
    {
        $loan->load(['user', 'item']);
        $description = "Menghapus data peminjaman {$loan->item->name} oleh {$loan->user->name}";

        $stockRestored = $service->deleteLoan($loan);

        ActivityLogger::log(
            'delete_loan',
            $description . ($stockRestored ? ' (stok barang dikembalikan)' : '')
        );

        return redirect()->route('admin.loans.index')
            ->with('success', 'Data peminjaman berhasil dihapus.' . ($stockRestored ? ' Stok barang dikembalikan.' : ''));
    }
}