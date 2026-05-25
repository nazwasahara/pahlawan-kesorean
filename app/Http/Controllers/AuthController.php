<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\ActivityLog;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            return match ($user->role) {
                'owner', 'admin' => redirect()->intended(route('admin-owner.dashboard')),
                'kasir' => redirect()->intended(route('kasir.dashboard')),
                default => redirect()->intended('/'),
            };
        }
        return back()->with('error', 'Email atau password salah.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    public function resetPasswordDirect(Request $request)
    {
        $request->validate([
            'reset_email' => ['required', 'email', 'exists:users,email'],
            'reset_password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'reset_email.exists' => 'Email tidak ditemukan.',
            'reset_password.confirmed' => 'Konfirmasi password tidak cocok.',
            'reset_password.min' => 'Password minimal harus 6 karakter.',
        ]);

        $user = User::where('email', $request->reset_email)->first();
        if ($user) {
            $user->password = Hash::make($request->reset_password);
            $user->save();

            // Log activity (checking if ActivityLog model handles logging correctly)
            try {
                ActivityLog::log('Reset Password', 'User ' . $user->name . ' melakukan reset password.');
            } catch (\Exception $e) {
                // If ActivityLog fails or isn't available, we still allow reset
            }

            return redirect()->route('login')->with('success', 'Password berhasil diubah. Silakan masuk dengan password baru.');
        }

        return back()->with('error', 'Gagal mengubah password.');
    }
}
