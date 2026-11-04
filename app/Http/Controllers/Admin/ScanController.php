<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Loan;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    public function index(Request $request)
    {
        $code = is_string($request->query('code')) ? trim($request->query('code')) : null;
        $item = null;
        $activeLoans = collect();
        $pendingCount = 0;

        if ($code) {
            Loan::markOverdue();

            $item = Item::with('category')->where('code', $code)->first();

            if ($item) {
                $activeLoans = Loan::with('user')
                    ->where('item_id', $item->id)
                    ->whereIn('status', Loan::ACTIVE_STATUSES)
                    ->orderBy('due_date')
                    ->get();

                $pendingCount = Loan::where('item_id', $item->id)
                    ->where('status', 'menunggu')
                    ->count();
            }
        }

        return view('admin.scan.index', compact('code', 'item', 'activeLoans', 'pendingCount'));
    }
}