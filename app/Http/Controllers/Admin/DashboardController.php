<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Loan;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        Loan::markOverdue();

        $stats = [
            'total_items' => Item::count(),
            'total_stock' => (int) Item::sum('stock'),
            'borrowed' => Loan::where('status', 'dipinjam')->count(),
            'overdue' => Loan::where('status', 'terlambat')->count(),
        ];

        $topItems = Item::has('loans')
            ->withCount('loans')
            ->orderByDesc('loans_count')
            ->orderBy('name')
            ->take(5)
            ->get();

        $activeUsers = User::where('role', 'user')
            ->has('loans')
            ->withCount('loans')
            ->orderByDesc('loans_count')
            ->orderBy('name')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'topItems', 'activeUsers'));
    }
}