<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $categories = Category::withCount('menus')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('id', 'asc')
            ->paginate(10)
            ->withQueryString();

        return view('admin-owner.categories.index', compact('categories', 'search'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
        ]);

        $category = new Category();
        $category->name = $data['name'];
        $category->slug = \Illuminate\Support\Str::slug($data['name']);
        $category->save();

        ActivityLog::log('Tambah Kategori', 'Tambah kategori ' . $category->name);

        return redirect()->back()->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name,' . $id],
        ]);

        $oldName = $category->name;

        $category->name = $data['name'];
        $category->slug = \Illuminate\Support\Str::slug($data['name']);
        $category->save();

        ActivityLog::log('Edit Kategori', 'Ubah kategori ' . $oldName . ' menjadi ' . $category->name);

        return redirect()->back()->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        // Check if there are menus associated with this category
        if ($category->menus()->count() > 0) {
            return redirect()->back()->with('error', 'Kategori tidak dapat dihapus karena masih memiliki menu di dalamnya.');
        }

        $category->delete();

        ActivityLog::log('Hapus Kategori', 'Hapus kategori ' . $category->name);

        return redirect()->back()->with('success', 'Kategori berhasil dihapus.');
    }
}
