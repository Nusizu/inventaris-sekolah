<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReturnLogRequest;
use App\Models\Item;
use App\Models\Loan;
use App\Models\ReturnLog;
use App\Services\ActivityLogger;
use App\Services\LoanService;
use Illuminate\Http\Request;

class ReturnLogController extends Controller
{
    public function index(Request $request)
    {
        $returnLogs = ReturnLog::with(['loan.user', 'loan.item'])
            ->when($request->q, function ($query, $search) {
                $query->whereHas('loan', function ($loan) use ($search) {
                    $loan->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('item', fn ($i) => $i->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($request->condition, fn ($query, $condition) => $query->where('condition_on_return', $condition))
            ->orderByDesc('returned_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.return-logs.index', compact('returnLogs'));
    }

    public function create(Loan $loan)
    {
        if ($loan->status === 'dikembalikan' || $loan->returnLog()->withTrashed()->exists()) {
            return redirect()->route('admin.loans.index')
                ->with('error', 'Peminjaman ini sudah dikembalikan.');
        }

        $loan->load(['user', 'item']);

        return view('admin.loans.return.create', compact('loan'));
    }

    public function store(ReturnLogRequest $request, Loan $loan, LoanService $service)
    {
        $returnLog = $service->returnLoan($loan, $request->validated());

        ActivityLogger::log(
            'return_loan',
            "Memproses pengembalian {$returnLog->loan->item->name} dari {$returnLog->loan->user->name} dalam kondisi {$returnLog->condition_on_return}"
        );

        $message = 'Pengembalian berhasil dicatat dan stok barang diperbarui.';

        if ($returnLog->condition_on_return !== 'baik') {
            $message .= ' Perbarui kondisi barang di menu Barang jika diperlukan.';
        }

        return redirect()->route('admin.return-logs.index')->with('success', $message);
    }
}