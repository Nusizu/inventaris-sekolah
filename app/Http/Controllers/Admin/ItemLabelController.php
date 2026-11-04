<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Item;
use App\Services\QrCode;
use Illuminate\Http\Request;

class ItemLabelController extends Controller
{
    /** Batas jumlah label dalam satu halaman cetak. */
    private const MAX_LABELS = 300;

    public function index(Request $request)
    {
        $ids = array_values(array_filter((array) $request->query('ids', []), 'is_numeric'));
        $search = is_string($request->query('q')) ? trim($request->query('q')) : '';
        $categoryId = is_numeric($request->query('category_id')) ? (int) $request->query('category_id') : null;

        $items = Item::with('category')
            ->when($ids, fn ($query) => $query->whereIn('id', $ids))
            ->when(! $ids && $search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->when(! $ids && $categoryId, fn ($query) => $query->where('category_id', $categoryId))
            ->orderBy('name')
            ->limit(self::MAX_LABELS)
            ->get();

        $labels = $items->map(fn (Item $item) => [
            'item' => $item,
            'qr' => QrCode::dataUri($item->code, 160),
        ]);

        $categories = Category::orderBy('name')->get(['id', 'name']);
        $selectedOnly = (bool) $ids;
        $maxLabels = self::MAX_LABELS;

        return view('admin.items.labels', compact('labels', 'categories', 'selectedOnly', 'maxLabels'));
    }
}