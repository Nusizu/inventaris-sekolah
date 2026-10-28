<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::withCount('items')
            ->when($request->q, fn ($query, $search) => $query->where('name', 'like', "%{$search}%"))
            ->when($request->usage === 'terpakai', fn ($query) => $query->has('items'))
            ->when($request->usage === 'kosong', fn ($query) => $query->doesntHave('items'))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.categories.create');
    }

    public function store(CategoryRequest $request)
    {
        $category = Category::create($request->validated());

        ActivityLogger::log('create_category', "Menambahkan kategori {$category->name}");

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Category $category)
    {
        return view('admin.categories.edit', compact('category'));
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $oldName = $category->name;
        $category->update($request->validated());

        ActivityLogger::log('update_category', "Mengubah kategori {$oldName} menjadi {$category->name}");

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        if ($category->items()->withTrashed()->exists()) {
            return back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki barang.');
        }

        $name = $category->name;
        $category->delete();

        ActivityLogger::log('delete_category', "Menghapus kategori {$name}");

        return redirect()->route('admin.categories.index')
            ->with('success', 'Kategori berhasil dihapus.');
    }
}