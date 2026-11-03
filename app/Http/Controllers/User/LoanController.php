<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoanRequest;
use App\Models\Item;
use App\Models\Loan;
use App\Services\ActivityLogger;
use App\Services\LoanService;
use DomainException;
use Illuminate\Http\Request;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        Loan::markOverdue();

        $loans = $request->user()->loans()
            ->with('item')
            ->when($request->q, function ($query, $search) {
                $query->whereHas('item', fn ($i) => $i->where('name', 'like', "%{$search}%"));
            })
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->orderByDesc('loan_date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('user.loans.index', compact('loans'));
    }

    public function create()
    {
        $items = Item::available()->orderBy('name')->get();

        return view('user.loans.create', compact('items'));
    }

    public function store(LoanRequest $request, LoanService $service)
    {
        $loan = $service->request($request->validated() + ['user_id' => $request->user()->id]);

        ActivityLogger::log(
            'request_loan',
            'Mengajukan peminjaman ' . $loan->item->name . ' sebanyak '
                . number_format($loan->quantity, 0, ',', '.')
        );

        return redirect()->route('user.loans.index')
            ->with('success', 'Pengajuan peminjaman berhasil dikirim dan menunggu persetujuan Admin.');
    }

    public function cancel(Request $request, Loan $loan, LoanService $service)
    {
        // Pengajuan milik orang lain tidak boleh disentuh
        abort_unless($loan->user_id === $request->user()->id, 403);

        try {
            $loan = $service->cancel($loan);
        } catch (DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        ActivityLogger::log(
            'cancel_loan',
            'Membatalkan pengajuan peminjaman ' . $loan->item->name . ' sebanyak '
                . number_format($loan->quantity, 0, ',', '.')
        );

        return redirect()->route('user.loans.index')
            ->with('success', 'Pengajuan peminjaman dibatalkan.');
    }
}