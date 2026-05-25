<?php

namespace App\Http\Controllers;

use App\Models\PromoCode;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class PromoCodeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        $promos = PromoCode::when($search, function ($query) use ($search) {
                $query->where('code', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin-owner.promos.index', compact('promos', 'search'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:promo_codes,code'],
            'type' => ['required', 'in:percentage,fixed'],
            'value' => ['required', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'minimum_transaction' => ['nullable', 'numeric', 'min:0'],
            'expired_at' => ['nullable', 'date'],
            'quota' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['code'] = strtoupper($data['code']);
        $data['used_count'] = 0;
        $data['is_active'] = true;

        $promo = PromoCode::create($data);

        ActivityLog::log('Tambah Promo', 'Tambah promo ' . $promo->code);

        return redirect()->back()->with('success', 'Kode promo berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $promo = PromoCode::findOrFail($id);

        $data = $request->validate([
            'code' => ['required', 'string', 'max:50', 'unique:promo_codes,code,' . $id],
            'type' => ['required', 'in:percentage,fixed'],
            'value' => ['required', 'numeric', 'min:0'],
            'max_discount' => ['nullable', 'numeric', 'min:0'],
            'minimum_transaction' => ['nullable', 'numeric', 'min:0'],
            'expired_at' => ['nullable', 'date'],
            'quota' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['code'] = strtoupper($data['code']);

        $promo->update($data);

        ActivityLog::log('Edit Promo', 'Ubah promo ' . $promo->code);

        return redirect()->back()->with('success', 'Kode promo berhasil diperbarui.');
    }

    public function toggleStatus($id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $promo = PromoCode::findOrFail($id);
        $promo->is_active = !$promo->is_active;
        $promo->save();

        ActivityLog::log(
            $promo->is_active ? 'Aktifkan Promo' : 'Nonaktifkan Promo',
            ($promo->is_active ? 'Aktif ' : 'Nonaktif ') . $promo->code
        );

        return redirect()->back()->with('success', 'Status promo berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $promo = PromoCode::findOrFail($id);
        $code = $promo->code;
        $promo->delete();

        ActivityLog::log('Hapus Promo', 'Hapus promo ' . $code);

        return redirect()->back()->with('success', 'Kode promo berhasil dihapus.');
    }
}
