@extends('layouts.app')

@section('title', 'Login Kasir')

@push('styles')
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow: hidden;
        }

        body {
            background: url('/images/auth.jpg') no-repeat center center fixed;
            background-size: cover;
            position: relative;
        }

        body::before,
        body::after {
            content: "";
            position: fixed;
            left: 0;
            width: 100%;
            height: 40px;
            z-index: 50;

            background-color: #ffffff;
            background-image:
                linear-gradient(45deg, #0b6b0b 25%, transparent 25%),
                linear-gradient(-45deg, #0b6b0b 25%, transparent 25%),
                linear-gradient(45deg, transparent 75%, #0b6b0b 75%),
                linear-gradient(-45deg, transparent 75%, #0b6b0b 75%);

            /* tile = 40px, half-offset = 20px — harus sama persis */
            background-size: 40px 40px;
            background-position:
                0 0,
                0 20px,
                20px -20px,
                -20px 0;
        }

        body::before {
            top: 0;
        }

        body::after {
            bottom: 0;
        }

        .login-glass {
            background: rgba(255, 255, 255, 0.88);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
            border-radius: 18px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(6px);
        }
    </style>
@endpush

@section('content')
    <div
        style="height: 100dvh; display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 48px 16px;">

        <div class="flex flex-col items-center mb-3">
            <span class="text-2xl sm:text-3xl font-bold text-red-700 tracking-wide mb-1">
                WELCOME!
            </span>
            <img src="/images/logo.png" alt="Logo" class="w-24 sm:w-32 drop-shadow-lg">
        </div>

        {{-- Card Login --}}
        <div class="login-glass w-full px-5 py-6 sm:px-8 sm:py-7 flex flex-col items-center" style="max-width: 420px;">

            <h2 class="text-xl sm:text-2xl font-bold text-green-800 mb-3 text-center">
                Masuk ke Akun Anda
            </h2>

            <form method="POST" action="{{ route('login') }}" class="w-full">
                @csrf
                 @if(session('error'))
                    <div class="bg-red-100 text-red-700 px-4 py-2 rounded mb-4 text-sm">
                        {{ session('error') }}
                    </div>
                @endif
                @if(session('success'))
                    <div class="bg-emerald-100 text-emerald-800 px-4 py-2 rounded mb-4 text-sm">
                        {{ session('success') }}
                    </div>
                @endif
                <div class="mb-3">
                    <label for="email" class="block text-gray-700 font-medium text-sm mb-1">
                        Email
                    </label>
                    <input id="email" type="text" name="email" value="{{ old('email') }}" required autofocus
                        class="w-full px-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 bg-white/80">
                    @error('email')
                        <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="mb-2">
                    <label for="password" class="block text-gray-700 font-medium text-sm mb-1">
                        Password
                    </label>
                    <div class="relative">
                        <input id="password"
                            type="password"
                            name="password"
                            required
                            class="w-full px-4 py-2 pr-10 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400 bg-white/80">
                        <button type="button"
                            id="togglePassword"
                            onclick="togglePass()"
                            class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-green-700 transition-colors"
                            aria-label="Tampilkan password">
                            <i id="eyeIcon" class="ri-eye-line text-lg"></i>
                        </button>
                    </div>
                    @error('password')
                        <span class="text-red-600 text-xs mt-1 block">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex items-center justify-between mb-4">
                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                        <input type="checkbox" name="remember" id="remember" class="rounded">
                        Ingat saya
                    </label>
                    <a href="#" onclick="openForgotPasswordModal(event)" class="text-sm text-green-700 hover:underline">Lupa Password?</a>
                </div>

                <button type="submit"
                    class="w-full py-2 px-4 bg-green-700 hover:bg-green-800 text-white font-semibold text-sm rounded-lg shadow transition">
                    Masuk
                </button>
            </form>
        </div>
    </div>

    {{-- Lupa Password Modal --}}
    <div id="forgot-password-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeForgotPasswordModal()"></div>
        
        <!-- Modal Content Wrapper -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-2xl bg-[#F4F3EB] border border-gray-200/45 p-6 sm:p-8 text-left shadow-2xl transition-all w-full max-w-sm">
                
                <div class="mb-4 text-center">
                    <h3 class="text-xl font-bold text-green-800">Reset Password</h3>
                    <p class="text-xs text-gray-600 mt-1">Ubah password akun Anda langsung tanpa verifikasi email.</p>
                </div>

                <form action="{{ route('password.reset.direct') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <!-- Email -->
                    <div>
                        <label class="block text-gray-700 font-semibold text-xs mb-1">Email Terdaftar</label>
                        <input type="email" 
                               name="reset_email" 
                               value="{{ old('reset_email') }}"
                               required 
                               placeholder="Contoh: user@gmail.com" 
                               class="w-full bg-white border border-gray-300 rounded-lg py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                        @error('reset_email')
                            <span class="text-red-650 text-xs mt-1 block font-bold text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password Baru -->
                    <div>
                        <label class="block text-gray-700 font-semibold text-xs mb-1">Password Baru</label>
                        <div class="relative">
                            <input type="password" 
                                   name="reset_password" 
                                   id="reset_password"
                                   required 
                                   placeholder="Minimal 6 karakter" 
                                   class="w-full bg-white border border-gray-300 rounded-lg py-2 pl-3 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                            <button type="button"
                                    id="toggleResetPassword"
                                    onclick="toggleResetPass('reset_password', 'eyeResetPasswordIcon', 'toggleResetPassword')"
                                    class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-green-700 transition-colors"
                                    aria-label="Tampilkan password baru">
                                <i id="eyeResetPasswordIcon" class="ri-eye-line text-lg"></i>
                            </button>
                        </div>
                        @error('reset_password')
                            <span class="text-red-650 text-xs mt-1 block font-bold text-red-600">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Konfirmasi Password Baru -->
                    <div>
                        <label class="block text-gray-700 font-semibold text-xs mb-1">Konfirmasi Password Baru</label>
                        <div class="relative">
                            <input type="password" 
                                   name="reset_password_confirmation" 
                                   id="reset_password_confirmation"
                                   required 
                                   placeholder="Ketik ulang password baru" 
                                   class="w-full bg-white border border-gray-300 rounded-lg py-2 pl-3 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-green-400">
                            <button type="button"
                                    id="toggleResetConfirmPassword"
                                    onclick="toggleResetPass('reset_password_confirmation', 'eyeResetConfirmPasswordIcon', 'toggleResetConfirmPassword')"
                                    class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-500 hover:text-green-700 transition-colors"
                                    aria-label="Tampilkan konfirmasi password">
                                <i id="eyeResetConfirmPasswordIcon" class="ri-eye-line text-lg"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-200">
                        <button type="button" 
                                onclick="closeForgotPasswordModal()"
                                class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold text-xs transition w-1/2">
                            Batal
                        </button>
                        <button type="submit" 
                                class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg font-bold text-xs transition shadow-sm w-1/2">
                            Simpan Password
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function togglePass() {
        const input = document.getElementById('password');
        const icon  = document.getElementById('eyeIcon');
        const btn   = document.getElementById('togglePassword');

        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'ri-eye-off-line text-lg';
            btn.setAttribute('aria-label', 'Sembunyikan password');
        } else {
            input.type = 'password';
            icon.className = 'ri-eye-line text-lg';
            btn.setAttribute('aria-label', 'Tampilkan password');
        }
    }

    function toggleResetPass(inputId, iconId, btnId) {
        const input = document.getElementById(inputId);
        const icon  = document.getElementById(iconId);
        const btn   = document.getElementById(btnId);

        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'ri-eye-off-line text-lg';
            btn.setAttribute('aria-label', 'Sembunyikan password');
        } else {
            input.type = 'password';
            icon.className = 'ri-eye-line text-lg';
            btn.setAttribute('aria-label', 'Tampilkan password');
        }
    }

    function openForgotPasswordModal(e) {
        if (e) e.preventDefault();
        document.getElementById('forgot-password-modal').classList.remove('hidden');
    }

    function closeForgotPasswordModal() {
        document.getElementById('forgot-password-modal').classList.add('hidden');
    }

    @if($errors->has('reset_email') || $errors->has('reset_password'))
        document.addEventListener('DOMContentLoaded', function() {
            openForgotPasswordModal();
        });
    @endif
</script>
@endpush