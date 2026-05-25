<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        // Summary counts
        $ownerCount = User::where('role', 'owner')->count();
        $adminCount = User::where('role', 'admin')->count();
        $kasirCount = User::where('role', 'kasir')->count();

        // Fetch users matching search filter (by name or email) with pagination
        $users = User::when($search, function ($query) use ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        })->orderBy('id', 'asc')->paginate(10)->withQueryString();

        return view('admin-owner.users.index', compact('users', 'ownerCount', 'adminCount', 'kasirCount', 'search'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'owner') {
            abort(403, 'Hanya Owner yang dapat menambahkan pegawai.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:owner,admin,kasir'],
        ]);

        $user = new User();
        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        $user->role = $data['role'];
        $user->status = 'aktif';
        $user->save();

        ActivityLog::log('Tambah Pengguna', 'Tambah ' . $user->name);

        return redirect()->back()->with('success', 'Pegawai berhasil ditambahkan.');
    }

    public function toggleStatus($id)
    {
        if (auth()->user()->role !== 'owner') {
            abort(403, 'Hanya Owner yang dapat mengubah status pegawai.');
        }

        $user = User::findOrFail($id);

        // Owner's status cannot be toggled
        if ($user->role === 'owner') {
            return redirect()->back()->with('error', 'Status Owner tidak dapat diubah.');
        }

        $user->status = $user->status === 'aktif' ? 'nonaktif' : 'aktif';
        $user->save();

        ActivityLog::log(
            $user->status === 'aktif' ? 'Aktifkan Pengguna' : 'Nonaktifkan Pengguna', 
            ($user->status === 'aktif' ? 'Aktif ' : 'Nonaktif ') . $user->name
        );

        return redirect()->back()->with('success', 'Status pengguna berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if (auth()->user()->role !== 'owner') {
            abort(403, 'Hanya Owner yang dapat menghapus pegawai.');
        }

        $user = User::findOrFail($id);

        // Protect Owner and current user from deletion
        if ($user->role === 'owner') {
            return redirect()->back()->with('error', 'Akun Owner tidak dapat dihapus.');
        }
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        ActivityLog::log('Hapus Pengguna', 'Hapus ' . $user->name);

        return redirect()->back()->with('success', 'Pengguna berhasil dihapus.');
    }
}
