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
        public function index()
    {
        return view('admin.items.index');
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
            ->whereIn('status', ['menunggu', 'dipinjam', 'terlambat'])
            ->exists();

        if ($hasActiveLoan) {
            return back()->with('error', 'Barang tidak dapat dihapus karena masih memiliki peminjaman aktif atau pengajuan yang menunggu.');
        }

        $item->delete(); // soft delete

        ActivityLogger::log('delete_item', "Menghapus barang {$item->name} ({$item->code})");

        return redirect()->route('admin.items.index')
            ->with('success', 'Barang berhasil dihapus.');
    }
}