<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ItemRequest;
use App\Models\Category;
use App\Models\Item;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $items = Item::with('category')
            ->when($request->q, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->when($request->category_id, fn ($query, $id) => $query->where('category_id', $id))
            ->when($request->condition, fn ($query, $condition) => $query->where('condition', $condition))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('admin.items.index', compact('items', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.items.create', compact('categories'));
    }

    public function store(ItemRequest $request)
    {
        $item = Item::create($request->validated());

        ActivityLogger::log('create_item', "Menambahkan barang {$item->name} ({$item->code})");

        return redirect()->route('admin.items.index')
            ->with('success', 'Barang berhasil ditambahkan.');
    }

    public function edit(Item $item)
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.items.edit', compact('item', 'categories'));
    }

    public function update(ItemRequest $request, Item $item)
    {
        $item->update($request->validated());

        ActivityLogger::log('update_item', "Mengubah barang {$item->name} ({$item->code})");

        return redirect()->route('admin.items.index')
            ->with('success', 'Barang berhasil diperbarui.');
    }

    public function destroy(Item $item)
    {
        $hasActiveLoan = $item->loans()
            ->whereIn('status', ['dipinjam', 'terlambat'])
            ->exists();

        if ($hasActiveLoan) {
            return back()->with('error', 'Barang tidak dapat dihapus karena masih sedang dipinjam.');
        }

        $item->delete(); // soft delete

        ActivityLogger::log('delete_item', "Menghapus barang {$item->name} ({$item->code})");

        return redirect()->route('admin.items.index')
            ->with('success', 'Barang berhasil dihapus.');
    }
}