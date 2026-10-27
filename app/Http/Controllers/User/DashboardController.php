<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        Loan::markOverdue();

        $user = $request->user();

        $counts = $user->loans()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $stats = [
            'borrowed' => (int) ($counts['dipinjam'] ?? 0),
            'overdue' => (int) ($counts['terlambat'] ?? 0),
            'returned' => (int) ($counts['dikembalikan'] ?? 0),
            'total' => (int) $counts->sum(),
        ];

        $recentLoans = $user->loans()
            ->with('item')
            ->orderByDesc('loan_date')
            ->orderByDesc('id')
            ->take(5)
            ->get();

        return view('user.dashboard', compact('stats', 'recentLoans'));
    }
}