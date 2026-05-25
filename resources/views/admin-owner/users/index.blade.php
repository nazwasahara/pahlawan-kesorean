@extends('admin-owner.layouts.app')

@section('title', 'Manajemen Pengguna - Pahlawan Kesorean')

@section('navbar-title', 'MANAJEMEN PENGGUNA')

@section('admin-owner-content')
<div class="space-y-6">
    <!-- Flash Messages -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-xl text-xs font-bold flex items-center justify-between shadow-sm">
            <span class="flex items-center gap-2">
                <i class="ri-checkbox-circle-fill text-emerald-600 text-lg"></i>
                {{ session('success') }}
            </span>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800"><i class="ri-close-line text-lg"></i></button>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-xl text-xs font-bold flex items-center justify-between shadow-sm">
            <span class="flex items-center gap-2">
                <i class="ri-error-warning-fill text-red-600 text-lg"></i>
                {{ session('error') }}
            </span>
            <button onclick="this.parentElement.remove()" class="text-red-500 hover:text-red-800"><i class="ri-close-line text-lg"></i></button>
        </div>
    @endif

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Data Pengguna</h1>
            <p class="text-xs font-bold text-gray-500 mt-1">Kelola dan pantau seluruh data pengguna dalam sistem.</p>
        </div>
        @if(auth()->user()->role === 'owner')
        <div>
            <button onclick="document.getElementById('add-user-modal').classList.remove('hidden')" 
                    class="bg-[#125E34] hover:bg-[#0E4A28] text-white px-5 py-2.5 rounded-full font-black text-xs tracking-wider flex items-center gap-2 shadow-sm transition duration-150">
                <i class="ri-add-line text-base"></i>
                <span>Tambah Pegawai</span>
            </button>
        </div>
        @endif
    </div>

    <!-- Summary Statistics Wrapped in a single White Box -->
    <div class="bg-white rounded-[1rem] p-6 border border-gray-200/60 shadow-sm">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card 1: Owner Count -->
            <div class="bg-[#E3F2FD] rounded-xl p-4 flex items-center gap-4 min-w-0">
                <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-[#0D47A1] shrink-0 shadow-sm border border-blue-100">
                    <i class="ri-user-fill text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-black text-gray-900 leading-tight">
                        {{ $ownerCount }}
                    </p>
                    <p class="text-xs font-semibold text-gray-500 mt-0.5">
                        Owner
                    </p>
                </div>
            </div>

            <!-- Card 2: Admin Count -->
            <div class="bg-[#FFE8D6] rounded-xl p-4 flex items-center gap-4 min-w-0">
                <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-[#E65100] shrink-0 shadow-sm border border-orange-100">
                    <i class="ri-user-fill text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-black text-gray-900 leading-tight">
                        {{ $adminCount }}
                    </p>
                    <p class="text-xs font-semibold text-gray-500 mt-0.5">
                        Admin
                    </p>
                </div>
            </div>

            <!-- Card 3: Kasir Count -->
            <div class="bg-[#EAEAEA] rounded-xl p-4 flex items-center gap-4 min-w-0">
                <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center text-black shrink-0 shadow-sm border border-gray-200">
                    <i class="ri-user-fill text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-black text-gray-900 leading-tight">
                        {{ $kasirCount }}
                    </p>
                    <p class="text-xs font-semibold text-gray-500 mt-0.5">
                        Kasir
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Section -->
    <div class="flex justify-end">
        <form action="{{ route('admin-owner.users.index') }}" method="GET" class="flex items-center gap-2 w-full max-w-sm">
            <div class="relative flex-1">
                <input type="text" 
                       name="search" 
                       value="{{ $search }}"
                       placeholder="Cari Nama/Email" 
                       class="w-full bg-white border border-gray-200 rounded-xl py-2 px-4 pr-10 text-xs font-semibold text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34]">
                @if($search)
                    <a href="{{ route('admin-owner.users.index') }}" class="absolute right-3 top-1 text-gray-450 hover:text-gray-700">
                        <i class="ri-close-line text-sm"></i>
                    </a>
                @endif
            </div>
            <button type="submit" class="w-9 h-9 bg-[#125E34] hover:bg-[#0E4A28] text-white rounded-xl flex items-center justify-center shadow transition-colors shrink-0">
                <i class="ri-search-line text-base"></i>
            </button>
        </form>
    </div>

    <!-- Data Table Card Container -->
    <div class="bg-white border border-gray-200/80 rounded-[1rem] p-6 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-[#F2F2F2] border-b border-gray-200 text-xs font-extrabold text-gray-900 tracking-wider">
                        <th class="py-3.5 pr-2 w-16 text-center">No</th>
                        <th class="py-3.5 px-4">Nama Pengguna</th>
                        <th class="py-3.5 px-4">Email</th>
                        <th class="py-3.5 px-4 text-center">Role</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 pl-4 text-center w-48">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs font-bold text-gray-800">
                    @forelse($users as $user)
                        <tr>
                            <!-- No -->
                            <td class="py-4 pr-2 text-center text-gray-900 font-medium">
                                {{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}
                            </td>
                            
                            <!-- Nama Pengguna -->
                            <td class="py-4 px-4 text-gray-900 font-bold">
                                {{ $user->name }}
                            </td>
                            
                            <!-- Email -->
                            <td class="py-4 px-4 text-gray-500 font-medium">
                                {{ $user->email }}
                            </td>
                            
                            <!-- Role Badge -->
                            <td class="py-4 px-4 text-center">
                                @if($user->role === 'owner')
                                    <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-semibold bg-[#E3F2FD] text-[#0D47A1]">
                                        owner
                                    </span>
                                @elseif($user->role === 'admin')
                                    <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-semibold bg-[#FFE8D6] text-[#E65100]">
                                        admin
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-semibold bg-[#ECEFF1] text-[#424242]">
                                        kasir
                                    </span>
                                @endif
                            </td>
                            
                            <!-- Status (Plain text) -->
                            <td class="py-4 px-4 text-center font-extrabold">
                                @if($user->status === 'aktif')
                                    <span class="text-[#2E7D32]">
                                        Aktif
                                    </span>
                                @else
                                    <span class="text-red-600">
                                        Nonaktif
                                    </span>
                                @endif
                            </td>
                            
                            <!-- Aksi Buttons -->
                            <td class="py-4 pl-4 text-center">
                                <div class="flex items-center justify-center gap-2.5">
                                    @if(auth()->user()->role === 'owner')
                                        @if($user->role === 'owner')
                                            <!-- Lock Action for Owner -->
                                            <button disabled class="w-8 h-8 rounded-lg bg-[#E0E0E0] text-gray-600 flex items-center justify-center cursor-not-allowed shadow-sm border border-gray-300/30">
                                                <i class="ri-lock-fill text-sm"></i>
                                            </button>
                                        @else
                                            <!-- Toggle Status (Nonaktif / Aktif) -->
                                            <form action="{{ route('admin-owner.users.toggle-status', $user->id) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" 
                                                        class="px-3 py-1.5 bg-[#EF6C00] hover:bg-[#E65100] text-white text-xs font-bold rounded-lg transition-colors flex items-center gap-1.5 shadow-sm">
                                                    <i class="ri-shut-down-line text-sm"></i>
                                                    <span>{{ $user->status === 'aktif' ? 'Nonaktif' : 'Aktif' }}</span>
                                                </button>
                                            </form>

                                            <!-- Hapus User -->
                                            <button type="button" 
                                                    onclick="confirmDelete('{{ route('admin-owner.users.destroy', $user->id) }}', 'Apakah Anda yakin ingin menghapus pengguna &ldquo;{{ $user->name }}&rdquo;?')"
                                                    class="px-3 py-1.5 bg-[#D32F2F] hover:bg-[#B71C1C] text-white text-xs font-bold rounded-lg transition-colors flex items-center gap-1.5 shadow-sm">
                                                <i class="ri-delete-bin-line text-sm"></i>
                                                <span>Hapus</span>
                                            </button>
                                        @endif
                                    @else
                                        <!-- Admin cannot perform actions -->
                                        <button disabled class="w-8 h-8 rounded-lg bg-[#E0E0E0] text-gray-600 flex items-center justify-center cursor-not-allowed shadow-sm border border-gray-300/30" title="Aksi hanya dapat dilakukan oleh Owner">
                                            <i class="ri-lock-line text-sm"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-gray-400 font-bold">
                                Tidak ada data pengguna ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Links -->
        @if($users->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 mt-4">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Tambah Pegawai Modal -->
<div id="add-user-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/50 transition-opacity" onclick="document.getElementById('add-user-modal').classList.add('hidden')"></div>
    
    <!-- Modal Content Wrapper -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative transform overflow-hidden rounded-[1rem] bg-[#F4F3EB] border border-gray-200/40 p-8 text-left shadow-2xl transition-all w-full max-w-lg">
            
            <div class="mb-6">
                <h3 class="text-2xl font-black text-gray-900 tracking-tight">Tambah Pegawai</h3>
                <p class="text-xs text-gray-600 mt-1">Isi informasi pegawai baru untuk ditambahkan ke dalam sistem.</p>
            </div>

            <form action="{{ route('admin-owner.users.store') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Name Input -->
                <div>
                    <label class="block text-sm font-bold text-[#125E34] mb-2">Nama Lengkap</label>
                    <input type="text" 
                           name="name" 
                           required 
                           placeholder="Contoh: John Doe" 
                           class="w-full bg-white border border-gray-300 rounded-xl py-2.5 px-4 text-xs font-bold text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34]">
                </div>

                <!-- Email Input -->
                <div>
                    <label class="block text-sm font-bold text-[#125E34] mb-2">Email</label>
                    <input type="email" 
                           name="email" 
                           required 
                           placeholder="Contoh: johndoe@email.com" 
                           class="w-full bg-white border border-gray-300 rounded-xl py-2.5 px-4 text-xs font-bold text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34]">
                </div>

                <!-- Role Dropdown -->
                <div>
                    <label class="block text-sm font-bold text-[#125E34] mb-2">Role/Jabatan</label>
                    <div class="relative">
                        <select name="role" 
                                required 
                                class="w-full bg-white border border-gray-300 rounded-xl py-2.5 px-4 text-xs font-bold text-gray-800 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34] appearance-none">
                            <option value="" disabled selected>Pilih Role</option>
                            <option value="kasir">Kasir</option>
                            <option value="admin">Admin</option>
                            <option value="owner">Owner</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-700">
                            <i class="ri-arrow-down-s-line text-lg font-black text-[#125E34]"></i>
                        </div>
                    </div>
                </div>

                <!-- Password Input -->
                <div>
                    <label class="block text-sm font-bold text-[#125E34] mb-2">Password</label>
                    <div class="relative">
                        <input type="password" 
                               id="modal-password"
                               name="password" 
                               required 
                               placeholder="Minimal 8 karakter" 
                               class="w-full bg-white border border-gray-300 rounded-xl py-2.5 px-4 pr-10 text-xs font-bold text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34]">
                        <button type="button" 
                                onclick="togglePasswordVisibility('modal-password', this)"
                                class="absolute right-3 top-2.5 text-[#125E34] hover:text-[#0E4A28] focus:outline-none">
                            <i class="ri-eye-fill text-lg"></i>
                        </button>
                    </div>
                </div>

                <!-- Password Confirmation Input -->
                <div>
                    <label class="block text-sm font-bold text-[#125E34] mb-2">Konfirmasi Password</label>
                    <div class="relative">
                        <input type="password" 
                               id="modal-password-confirmation"
                               name="password_confirmation" 
                               required 
                               placeholder="Ulangi password" 
                               class="w-full bg-white border border-gray-300 rounded-xl py-2.5 px-4 pr-10 text-xs font-bold text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34]">
                        <button type="button" 
                                onclick="togglePasswordVisibility('modal-password-confirmation', this)"
                                class="absolute right-3 top-2.5 text-[#125E34] hover:text-[#0E4A28] focus:outline-none">
                            <i class="ri-eye-fill text-lg"></i>
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4">
                    <button type="button" 
                            onclick="document.getElementById('add-user-modal').classList.add('hidden')"
                            class="px-6 py-2 rounded-xl bg-[#D2D2D2] hover:bg-gray-300 text-gray-800 font-extrabold text-sm tracking-wide transition">
                        Batal
                    </button>
                    <button type="submit" 
                            class="bg-[#125E34] hover:bg-[#0E4A28] text-white px-6 py-2 rounded-xl font-extrabold text-sm tracking-wide transition shadow-sm">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function togglePasswordVisibility(inputId, buttonElement) {
        const input = document.getElementById(inputId);
        const icon = buttonElement.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('ri-eye-fill');
            icon.classList.add('ri-eye-off-fill');
        } else {
            input.type = 'password';
            icon.classList.remove('ri-eye-off-fill');
            icon.classList.add('ri-eye-fill');
        }
    }
</script>

<!-- Delete Confirmation Modal -->
<div id="delete-confirm-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/50 transition-opacity" onclick="closeDeleteModal()"></div>
    
    <!-- Modal Wrapper -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative transform overflow-hidden rounded-[1rem] bg-white border border-gray-200 p-8 text-center shadow-2xl transition-all w-full max-w-sm">
            <!-- Warning Icon -->
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-red-50 mb-4">
                <i class="ri-error-warning-fill text-2xl text-red-600"></i>
            </div>
            
            <div class="mb-6">
                <h3 class="text-xl font-black text-gray-900 tracking-tight">Hapus Data?</h3>
                <p id="delete-modal-message" class="text-xs text-gray-500 font-bold mt-2">Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>
            </div>

            <form id="delete-confirm-form" action="" method="POST">
                @csrf
                @method('DELETE')
                
                <div class="flex items-center justify-center gap-3">
                    <button type="button" 
                            onclick="closeDeleteModal()"
                            class="px-6 py-2.5 rounded-xl bg-gray-150 hover:bg-gray-200 text-gray-800 font-extrabold text-sm tracking-wide transition w-1/2">
                        Batal
                    </button>
                    <button type="submit" 
                            class="bg-[#D32F2F] hover:bg-[#B71C1C] text-white px-6 py-2.5 rounded-xl font-extrabold text-sm tracking-wide transition shadow-sm w-1/2">
                        Hapus
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function confirmDelete(actionUrl, message = '') {
        const form = document.getElementById('delete-confirm-form');
        form.action = actionUrl;
        if (message) {
            document.getElementById('delete-modal-message').innerText = message;
        } else {
            document.getElementById('delete-modal-message').innerText = "Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.";
        }
        document.getElementById('delete-confirm-modal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('delete-confirm-modal').classList.add('hidden');
    }
</script>
@endsection
