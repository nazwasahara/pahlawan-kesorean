@extends('admin-owner.layouts.app')

@section('title', 'Manajemen Kategori Menu - Pahlawan Kesorean')

@section('navbar-title', 'MANAJEMEN MENU')

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
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Data Kategori Menu</h1>
            <p class="text-xs font-bold text-gray-500 mt-1">Kelola dan pantau seluruh data kategori menu dalam sistem.</p>
        </div>
        @if(auth()->user()->role === 'admin')
        <div>
            <button onclick="document.getElementById('add-category-modal').classList.remove('hidden')" 
                    class="bg-[#125E34] hover:bg-[#0E4A28] text-white px-5 py-2.5 rounded-xl font-black text-xs tracking-wider flex items-center gap-2 shadow-sm transition duration-150">
                <i class="ri-add-line text-base"></i>
                <span>Tambah Kategori</span>
            </button>
        </div>
        @endif
    </div>

    <!-- Search Section -->
    <div class="flex justify-end">
        <form action="{{ route('admin-owner.categories.index') }}" method="GET" class="flex items-center gap-2 w-full max-w-sm">
            <div class="relative flex-1">
                <input type="text" 
                       name="search" 
                       value="{{ $search }}"
                       placeholder="Cari Kategori.." 
                       class="w-full bg-white border border-gray-200 rounded-xl py-2 px-5 pr-10 text-xs font-semibold text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34]">
                @if($search)
                    <a href="{{ route('admin-owner.categories.index') }}" class="absolute right-3 top-1 text-gray-450 hover:text-gray-700">
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
                        <th class="py-3.5 px-4 text-center">Kategori</th>
                        <th class="py-3.5 px-4 text-center w-48">Jumlah Menu</th>
                        @if(auth()->user()->role === 'admin')
                        <th class="py-3.5 pl-4 text-center w-48">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs font-bold text-gray-800">
                    @forelse($categories as $index => $category)
                        <tr>
                            <!-- No -->
                            <td class="py-4 pr-2 text-center text-gray-900 font-medium">
                                {{ ($categories->currentPage() - 1) * $categories->perPage() + $loop->iteration }}
                            </td>
                            
                            <!-- Kategori -->
                            <td class="py-4 px-4 text-gray-900 font-bold">
                                {{ $category->name }}
                            </td>
                            
                            <!-- Jumlah Menu -->
                            <td class="py-4 px-4 text-center font-bold">
                                {{ $category->menus_count }}
                            </td>
                            
                            @if(auth()->user()->role === 'admin')
                            <!-- Aksi Buttons -->
                            <td class="py-4 px-4">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Edit Button -->
                                    <button onclick='openEditCategoryModal(@json($category))' 
                                            class="px-3 py-1.5 bg-[#EF6C00] hover:bg-[#E65100] text-white text-xs font-bold rounded-lg transition-colors flex items-center gap-1 shadow-sm">
                                        <i class="ri-edit-line text-sm"></i>
                                        <span>Edit</span>
                                    </button>
                                    
                                    <!-- Delete Button -->
                                    <button type="button" 
                                            onclick="confirmDelete('{{ route('admin-owner.categories.destroy', $category->id) }}', 'Apakah Anda yakin ingin menghapus kategori &ldquo;{{ $category->name }}&rdquo;?')"
                                            class="px-3 py-1.5 bg-[#D32F2F] hover:bg-[#B71C1C] text-white text-xs font-bold rounded-lg transition-colors flex items-center gap-1 shadow-sm">
                                        <i class="ri-delete-bin-line text-sm"></i>
                                        <span>Hapus</span>
                                    </button>
                                </div>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-gray-400 font-bold">
                                Tidak ada data kategori ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        @if($categories->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 mt-4">
                {{ $categories->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Tambah Kategori Modal -->
<div id="add-category-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/50 transition-opacity" onclick="document.getElementById('add-category-modal').classList.add('hidden')"></div>
    
    <!-- Modal Content Wrapper -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative transform overflow-hidden rounded-[1rem] bg-[#F4F3EB] border border-gray-200/40 p-8 text-left shadow-2xl transition-all w-full max-w-md">
            
            <div class="mb-6">
                <h3 class="text-2xl font-black text-gray-900 tracking-tight">Tambah Kategori</h3>
                <p class="text-xs text-gray-600 mt-1">Buat kategori baru untuk menu hidangan Anda.</p>
            </div>

            <form action="{{ route('admin-owner.categories.store') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Nama Kategori -->
                <div>
                    <label class="block text-sm font-bold text-[#125E34] mb-1.5">Nama Kategori</label>
                    <input type="text" 
                           name="name" 
                           required 
                           placeholder="Contoh: Burger" 
                           class="w-full bg-white border border-gray-300 rounded-xl py-2.5 px-4 text-xs font-bold text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34]">
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200/40">
                    <button type="button" 
                            onclick="document.getElementById('add-category-modal').classList.add('hidden')"
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

<!-- Edit Kategori Modal -->
<div id="edit-category-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/50 transition-opacity" onclick="document.getElementById('edit-category-modal').classList.add('hidden')"></div>
    
    <!-- Modal Content Wrapper -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative transform overflow-hidden rounded-[1rem] bg-[#F4F3EB] border border-gray-200/40 p-8 text-left shadow-2xl transition-all w-full max-w-md">
            
            <div class="mb-6">
                <h3 class="text-2xl font-black text-gray-900 tracking-tight">Edit Kategori</h3>
                <p class="text-xs text-gray-600 mt-1">Ubah nama kategori menu yang dipilih.</p>
            </div>

            <form id="edit-category-form" action="" method="POST" class="space-y-4">
                @csrf
                <!-- action URL set via JS -->

                <!-- Nama Kategori -->
                <div>
                    <label class="block text-sm font-bold text-[#125E34] mb-1.5">Nama Kategori</label>
                    <input type="text" 
                           id="edit-category-name"
                           name="name" 
                           required 
                           placeholder="Contoh: Burger" 
                           class="w-full bg-white border border-gray-300 rounded-xl py-2.5 px-4 text-xs font-bold text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34]">
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200/40">
                    <button type="button" 
                            onclick="document.getElementById('edit-category-modal').classList.add('hidden')"
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
    function openEditCategoryModal(category) {
        const form = document.getElementById('edit-category-form');
        form.action = `{{ route('admin-owner.categories.update', ':id') }}`.replace(':id', category.id);
        
        document.getElementById('edit-category-name').value = category.name;
        
        document.getElementById('edit-category-modal').classList.remove('hidden');
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
