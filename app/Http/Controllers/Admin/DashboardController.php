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
            'pending' => Loan::where('status', 'menunggu')->count(),
            'borrowed' => Loan::where('status', 'dipinjam')->count(),
            'overdue' => Loan::where('status', 'terlambat')->count(),
        ];

        // Hanya peminjaman yang benar-benar terjadi (bukan menunggu/ditolak/dibatalkan)
        $counted = ['dipinjam', 'terlambat', 'dikembalikan'];

        $topItems = Item::whereHas('loans', fn ($q) => $q->whereIn('status', $counted))
            ->withCount(['loans' => fn ($q) => $q->whereIn('status', $counted)])
            ->orderByDesc('loans_count')
            ->orderBy('name')
            ->take(5)
            ->get();

        $activeUsers = User::where('role', 'user')
            ->whereHas('loans', fn ($q) => $q->whereIn('status', $counted))
            ->withCount(['loans' => fn ($q) => $q->whereIn('status', $counted)])
            ->orderByDesc('loans_count')
            ->orderBy('name')
            ->take(5)
            ->get();

        $trend = $this->monthlyTrend(12);
        $hasTrendData = collect($trend['count'])->flatten()->sum() > 0;

        return view('admin.dashboard', compact('stats', 'topItems', 'activeUsers', 'trend', 'hasTrendData'));
    }

    /**
     * Jumlah peminjaman dan jumlah unit per bulan (menurut tanggal pinjam),
     * dipisah per status, untuk $months bulan terakhir.
     */
    private function monthlyTrend(int $months): array
    {
        $start = now()->startOfMonth()->subMonthsNoOverflow($months - 1);

        $rows = Loan::query()
            ->whereIn('status', ['dipinjam', 'terlambat', 'dikembalikan'])
            ->where('loan_date', '>=', $start->toDateString())
            ->selectRaw("DATE_FORMAT(loan_date, '%Y-%m') as ym, status, COUNT(*) as total, SUM(quantity) as units")
            ->groupBy('ym', 'status')
            ->get();

        $keys = [];
        $labels = [];

        for ($i = 0; $i < $months; $i++) {
            $month = $start->copy()->addMonthsNoOverflow($i);
            $keys[] = $month->format('Y-m');
            $labels[] = $month->translatedFormat('M Y');
        }

        $count = [];
        $units = [];

        foreach (['dipinjam', 'terlambat', 'dikembalikan'] as $status) {
            foreach ($keys as $key) {
                $row = $rows->first(fn ($r) => $r->ym === $key && $r->status === $status);

                $count[$status][] = (int) ($row->total ?? 0);
                $units[$status][] = (int) ($row->units ?? 0);
            }
        }

        return ['labels' => $labels, 'count' => $count, 'units' => $units];
    }
}