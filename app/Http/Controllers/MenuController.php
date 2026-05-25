<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Category;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $menus = Menu::with('category')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%");
                    });
            })
            ->orderBy('updated_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $categories = Category::orderBy('name', 'asc')->get();

        return view('admin-owner.menus.index', compact('menus', 'search', 'categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_available' => ['required', 'in:1,0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $menu = new Menu();
        $menu->name = $data['name'];
        $menu->category_id = $data['category_id'];
        $menu->price = $data['price'];
        $menu->stock = $data['stock'];
        $menu->is_available = $data['is_available'];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('menus', 'public');
            $menu->image = $path;
        }

        $menu->save();

        ActivityLog::log('Tambah Menu', 'Tambah menu ' . $menu->name);

        return redirect()->back()->with('success', 'Menu berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'is_available' => ['required', 'in:1,0'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        $oldStock = $menu->stock;

        $menu->name = $data['name'];
        $menu->category_id = $data['category_id'];
        $menu->price = $data['price'];
        $menu->stock = $data['stock'];
        $menu->is_available = $data['is_available'];

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($menu->image && Storage::disk('public')->exists($menu->image)) {
                Storage::disk('public')->delete($menu->image);
            }
            $path = $request->file('image')->store('menus', 'public');
            $menu->image = $path;
        }

        $menu->save();

        if ($oldStock != $menu->stock) {
            ActivityLog::log('Edit Menu', "Stok {$menu->name}: {$oldStock} -> {$menu->stock}");
        } else {
            ActivityLog::log('Edit Menu', "Edit menu {$menu->name}");
        }

        return redirect()->back()->with('success', 'Menu berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $menu = Menu::findOrFail($id);

        // Delete image file if exists
        if ($menu->image && Storage::disk('public')->exists($menu->image)) {
            Storage::disk('public')->delete($menu->image);
        }

        $menu->delete();

        ActivityLog::log('Hapus Menu', 'Hapus ' . $menu->name);

        return redirect()->back()->with('success', 'Menu berhasil dihapus.');
    }
}
