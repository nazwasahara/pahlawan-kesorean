@extends('admin-owner.layouts.app')

@section('title', 'Manajemen Menu - Pahlawan Kesorean')

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
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Data Menu</h1>
            <p class="text-xs font-bold text-gray-500 mt-1">Kelola dan pantau seluruh data menu dalam sistem.</p>
        </div>
        @if(auth()->user()->role === 'admin')
        <div>
            <button onclick="document.getElementById('add-menu-modal').classList.remove('hidden')" 
                    class="bg-[#125E34] hover:bg-[#0E4A28] text-white px-5 py-2.5 rounded-xl font-black text-xs tracking-wider flex items-center gap-2 shadow-sm transition duration-150">
                <i class="ri-add-line text-base"></i>
                <span>Tambah Menu</span>
            </button>
        </div>
        @endif
    </div>

    <!-- Search & Filter Section -->
    <div class="flex justify-end">
        <form action="{{ route('admin-owner.menus.index') }}" 
            method="GET" 
            class="flex flex-wrap items-center gap-3 w-full max-w-2xl justify-end">

            <!-- Search Input -->
            <div class="relative flex-1 min-w-[220px]">
                <input type="text" 
                    name="search" 
                    value="{{ $search }}"
                    placeholder="Cari Menu.." 
                    class="w-full bg-white border border-gray-200 rounded-xl py-2 px-5 pr-10 text-xs font-semibold text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34]">

                @if($search)
                    <a href="{{ route('admin-owner.menus.index') }}" 
                    class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-700 flex items-center justify-center">
                        <i class="ri-close-line text-sm"></i>
                    </a>
                @endif
            </div>

            <!-- Search Button -->
            <button type="submit" 
                    class="w-9 h-9 bg-[#125E34] hover:bg-[#0E4A28] text-white rounded-xl flex items-center justify-center shadow transition-colors shrink-0">
                <i class="ri-search-line text-base"></i>
            </button>

            <!-- Category Dropdown -->
            <div class="relative">
                <select name="category" 
                        onchange="this.form.submit()"
                        class="appearance-none bg-white border border-gray-200 rounded-xl py-2 pl-5 pr-10 text-xs font-bold text-gray-800 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34] cursor-pointer">

                    <option value="">Semua Kategori</option>

                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}"
                            {{ request('category') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach

                </select>

                <!-- Custom Dropdown Icon -->
                <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                    <i class="ri-arrow-down-s-line text-sm"></i>
                </div>
            </div>
        </form>
    </div>

    <!-- Data Table Card Container -->
    <div class="bg-white border border-gray-200/80 rounded-[1rem] p-6 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-[#F2F2F2] border-b border-gray-200 text-xs font-extrabold text-gray-900 tracking-wider">
                        <th class="py-3.5 pr-2 w-16 text-center">No</th>
                        <th class="py-3.5 px-4 text-center w-24">Gambar</th>
                        <th class="py-3.5 px-4 text-center">Menu</th>
                        <th class="py-3.5 px-4 text-center">Kategori</th>
                        <th class="py-3.5 px-4 text-center">Harga</th>
                        <th class="py-3.5 px-4 text-center">Stok</th>
                        <th class="py-3.5 px-4 text-center w-32">Status</th>
                        @if(auth()->user()->role === 'admin')
                        <th class="py-3.5 pl-4 text-center w-48">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs font-bold text-gray-800">
                    @forelse($menus as $menu)
                        <tr>
                            <!-- No -->
                            <td class="py-4 pr-2 text-center text-gray-900 font-medium">
                                {{ ($menus->currentPage() - 1) * $menus->perPage() + $loop->iteration }}
                            </td>
                            
                            <!-- Gambar -->
                            <td class="py-2 px-4 text-center">
                                <div class="w-12 h-12 rounded-full overflow-hidden border border-gray-250 bg-white flex items-center justify-center shadow-inner mx-auto">
                                    @if($menu->image)
                                        <img src="{{ Str::startsWith($menu->image, ['http://', 'https://']) ? $menu->image : asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="ri-image-line text-lg text-gray-300"></i>
                                    @endif
                                </div>
                            </td>
                            
                            <!-- Menu Name -->
                            <td class="py-4 px-4 text-gray-900 font-bold">
                                {{ $menu->name }}
                            </td>
                            
                            <!-- Category -->
                            <td class="py-4 px-4 text-gray-900 font-bold">
                                {{ $menu->category->name ?? 'N/A' }}
                            </td>
                            
                            <!-- Harga -->
                            <td class="py-4 px-4 text-gray-900 font-bold">
                                Rp{{ number_format($menu->price, 0, ',', '.') }}
                            </td>
                            
                            <!-- Stok -->
                            <td class="py-4 px-4 text-center font-bold">
                                {{ $menu->stock }}
                            </td>
                            
                            <!-- Status Badge -->
                            <td class="py-4 px-4 text-center">
                                @if($menu->is_available && $menu->stock > 0)
                                    <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold bg-[#E8F5E9] text-[#125E34] shadow-sm">
                                        Tersedia
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-4 py-1.5 rounded-full text-xs font-bold bg-red-50 text-red-650 shadow-sm">
                                        Habis
                                    </span>
                                @endif
                            </td>

                            @if(auth()->user()->role === 'admin')
                            <!-- Aksi Buttons -->
                            <td class="py-4 pl-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Edit Button -->
                                    <button onclick="openEditMenuModal({{ $menu }})" 
                                            class="px-3 py-1.5 bg-[#EF6C00] hover:bg-[#E65100] text-white text-xs font-bold rounded-lg transition-colors flex items-center gap-1 shadow-sm">
                                        <i class="ri-edit-line text-sm"></i>
                                        <span>Edit</span>
                                    </button>

                                    <!-- Hapus Button -->
                                    <button type="button" 
                                            onclick="confirmDelete('{{ route('admin-owner.menus.destroy', $menu->id) }}', 'Apakah Anda yakin ingin menghapus menu &ldquo;{{ $menu->name }}&rdquo;?')"
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
                            <td colspan="8" class="py-12 text-center text-gray-400 font-bold">
                                Tidak ada data menu ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        @if($menus->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 mt-4">
                {{ $menus->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Tambah Menu Modal -->
<div id="add-menu-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/50 transition-opacity" onclick="document.getElementById('add-menu-modal').classList.add('hidden')"></div>
    
    <!-- Modal Content Wrapper -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative transform overflow-hidden rounded-[1rem] bg-[#F4F3EB] border border-gray-200/40 p-8 text-left shadow-2xl transition-all w-full max-w-3xl">
            
            <div class="mb-6">
                <h3 class="text-2xl font-black text-gray-900 tracking-tight">Tambah Menu</h3>
                <p class="text-xs text-gray-600 mt-1">Isi informasi menu baru untuk ditambahkan ke dalam sistem.</p>
            </div>

            <form action="{{ route('admin-owner.menus.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 mb-6">
                    <!-- Left Column -->
                    <div class="md:col-span-7 space-y-4">
                        <!-- Nama Menu -->
                        <div>
                            <label class="block text-sm font-bold text-[#125E34] mb-1.5">Nama Menu</label>
                            <input type="text" 
                                   name="name" 
                                   required 
                                   placeholder="Contoh: Kebab Ayam" 
                                   class="w-full bg-white border border-gray-300 rounded-xl py-2.5 px-4 text-xs font-bold text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34]">
                        </div>

                        <!-- Kategori -->
                        <div>
                            <label class="block text-sm font-bold text-[#125E34] mb-1.5">Kategori</label>
                            <div class="relative">
                                <select name="category_id" 
                                        required 
                                        class="w-full bg-white border border-gray-300 rounded-xl py-2.5 px-4 pr-10 text-xs font-bold text-gray-800 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34] appearance-none">
                                    <option value="" disabled selected>Pilih Kategori</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-700">
                                    <i class="ri-arrow-down-s-line text-lg font-black text-[#125E34]"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Harga -->
                        <div>
                            <label class="block text-sm font-bold text-[#125E34] mb-1.5">Harga</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-500">Rp</span>
                                <input type="number" 
                                       name="price" 
                                       required 
                                       placeholder="0" 
                                       min="0"
                                       class="w-full bg-white border border-gray-300 rounded-xl py-2.5 pl-10 pr-4 text-xs font-bold text-gray-800 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34]">
                            </div>
                        </div>

                        <!-- Stok -->
                        <div>
                            <label class="block text-sm font-bold text-[#125E34] mb-1.5">Stok</label>
                            <input type="number"
                                   id="add-menu-stock" 
                                   name="stock" 
                                   required 
                                   placeholder="0" 
                                   min="0"
                                   class="w-full bg-white border border-gray-300 rounded-xl py-2.5 px-4 text-xs font-bold text-gray-800 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34]">
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-bold text-[#125E34] mb-1.5">Status</label>
                            <div class="relative">
                                <select id="add-menu-available"
                                        name="is_available"
                                        required 
                                        class="w-full bg-white border border-gray-300 rounded-xl py-2.5 px-4 pr-10 text-xs font-bold text-gray-800 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34] appearance-none">
                                    <option value="1">Tersedia</option>
                                    <option value="0">Tidak Tersedia</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-700">
                                    <i class="ri-arrow-down-s-line text-lg font-black text-[#125E34]"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="md:col-span-5 flex flex-col justify-between">
                        <div>
                            <label class="block text-sm font-bold text-[#125E34] mb-1.5">Gambar</label>
                            <label class="relative cursor-pointer flex flex-col items-center justify-center border border-gray-300 rounded-2xl bg-white p-6 h-[15.5rem] hover:border-[#125E34] transition group overflow-hidden">
                                <input type="file" 
                                       name="image" 
                                       accept="image/*" 
                                       class="hidden" 
                                       onchange="previewImage(this, 'add-image-preview', 'add-image-placeholder')">
                                
                                <!-- Preview Element -->
                                <img id="add-image-preview" src="" class="absolute inset-0 w-full h-full object-cover hidden">
                                
                                <!-- Placeholder Element -->
                                <div id="add-image-placeholder" class="flex flex-col items-center text-center">
                                    <div class="w-14 h-14 rounded-full bg-[#E8F5E9] text-[#125E34] flex items-center justify-center mb-3">
                                        <i class="ri-image-add-line text-2xl"></i>
                                    </div>
                                    <span class="text-xs font-extrabold text-gray-800">Upload gambar menu</span>
                                    <span class="text-[10px] text-gray-400 font-bold mt-1">JPG, JPEG, atau PNG</span>
                                    <span class="text-[10px] text-gray-400 font-bold">Maks. 2 MB</span>
                                </div>
                            </label>
                        </div>

                        <!-- Info Banner -->
                        <div class="flex items-center gap-3 bg-[#E8F5E9]/80 border border-emerald-100 rounded-xl p-3.5 mt-4">
                            <i class="ri-information-line text-lg text-[#125E34] shrink-0 font-extrabold"></i>
                            <span class="text-[10px] sm:text-xs text-[#125E34] font-semibold leading-snug">Gunakan gambar dengan rasio 1:1 untuk hasil terbaik.</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200/40">
                    <button type="button" 
                            onclick="document.getElementById('add-menu-modal').classList.add('hidden')"
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

<!-- Edit Menu Modal -->
<div id="edit-menu-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black/50 transition-opacity" onclick="document.getElementById('edit-menu-modal').classList.add('hidden')"></div>
    
    <!-- Modal Content Wrapper -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative transform overflow-hidden rounded-[1rem] bg-[#F4F3EB] border border-gray-200/40 p-8 text-left shadow-2xl transition-all w-full max-w-3xl">
            
            <div class="mb-6">
                <h3 class="text-2xl font-black text-gray-900 tracking-tight">Edit Menu</h3>
                <p class="text-xs text-gray-600 mt-1">Ubah detail informasi menu yang dipilih.</p>
            </div>

            <form id="edit-menu-form" action="" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-12 gap-6 mb-6">
                    <!-- Left Column -->
                    <div class="md:col-span-7 space-y-4">
                        <!-- Nama Menu -->
                        <div>
                            <label class="block text-sm font-bold text-[#125E34] mb-1.5">Nama Menu</label>
                            <input type="text" 
                                   id="edit-menu-name"
                                   name="name" 
                                   required 
                                   placeholder="Contoh: Kebab Ayam" 
                                   class="w-full bg-white border border-gray-300 rounded-xl py-2.5 px-4 text-xs font-bold text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34]">
                        </div>

                        <!-- Kategori -->
                        <div>
                            <label class="block text-sm font-bold text-[#125E34] mb-1.5">Kategori</label>
                            <div class="relative">
                                <select id="edit-menu-category"
                                        name="category_id" 
                                        required 
                                        class="w-full bg-white border border-gray-300 rounded-xl py-2.5 px-4 pr-10 text-xs font-bold text-gray-800 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34] appearance-none">
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-700">
                                    <i class="ri-arrow-down-s-line text-lg font-black text-[#125E34]"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Harga -->
                        <div>
                            <label class="block text-sm font-bold text-[#125E34] mb-1.5">Harga</label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold text-gray-500">Rp</span>
                                <input type="number" 
                                       id="edit-menu-price"
                                       name="price" 
                                       required 
                                       placeholder="0" 
                                       min="0"
                                       class="w-full bg-white border border-gray-300 rounded-xl py-2.5 pl-10 pr-4 text-xs font-bold text-gray-800 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34]">
                            </div>
                        </div>

                        <!-- Stok -->
                        <div>
                            <label class="block text-sm font-bold text-[#125E34] mb-1.5">Stok</label>
                            <input type="number" 
                                   id="edit-menu-stock"
                                   name="stock" 
                                   required 
                                   placeholder="0" 
                                   min="0"
                                   class="w-full bg-white border border-gray-300 rounded-xl py-2.5 px-4 text-xs font-bold text-gray-800 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34]">
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-bold text-[#125E34] mb-1.5">Status</label>
                            <div class="relative">
                                <select id="edit-menu-available"
                                        name="is_available" 
                                        required 
                                        class="w-full bg-white border border-gray-300 rounded-xl py-2.5 px-4 pr-10 text-xs font-bold text-gray-800 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34] appearance-none">
                                    <option value="1">Tersedia</option>
                                    <option value="0">Tidak Tersedia</option>
                                </select>
                                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-700">
                                    <i class="ri-arrow-down-s-line text-lg font-black text-[#125E34]"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="md:col-span-5 flex flex-col justify-between">
                        <div>
                            <label class="block text-sm font-bold text-[#125E34] mb-1.5">Gambar</label>
                            <label class="relative cursor-pointer flex flex-col items-center justify-center border border-gray-300 rounded-2xl bg-white p-6 h-[15.5rem] hover:border-[#125E34] transition group overflow-hidden">
                                <input type="file" 
                                       name="image" 
                                       accept="image/*" 
                                       class="hidden" 
                                       onchange="previewImage(this, 'edit-image-preview', 'edit-image-placeholder')">
                                
                                <!-- Preview Element -->
                                <img id="edit-image-preview" src="" class="absolute inset-0 w-full h-full object-cover hidden">
                                
                                <!-- Placeholder Element -->
                                <div id="edit-image-placeholder" class="flex flex-col items-center text-center">
                                    <div class="w-14 h-14 rounded-full bg-[#E8F5E9] text-[#125E34] flex items-center justify-center mb-3">
                                        <i class="ri-image-add-line text-2xl"></i>
                                    </div>
                                    <span class="text-xs font-extrabold text-gray-800">Upload gambar menu</span>
                                    <span class="text-[10px] text-gray-400 font-bold mt-1">JPG, JPEG, atau PNG</span>
                                    <span class="text-[10px] text-gray-400 font-bold">Maks. 2 MB</span>
                                </div>
                            </label>
                        </div>

                        <!-- Info Banner -->
                        <div class="flex items-center gap-3 bg-[#E8F5E9]/80 border border-emerald-100 rounded-xl p-3.5 mt-4">
                            <i class="ri-information-line text-lg text-[#125E34] shrink-0 font-extrabold"></i>
                            <span class="text-[10px] sm:text-xs text-[#125E34] font-semibold leading-snug">Gunakan gambar dengan rasio 1:1 untuk hasil terbaik.</span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200/40">
                    <button type="button" 
                            onclick="document.getElementById('edit-menu-modal').classList.add('hidden')"
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
    // Sync Add Menu Modal fields
    document.getElementById('add-menu-stock').addEventListener('input', function() {
        const stockVal = parseInt(this.value) || 0;
        if (stockVal === 0) {
            document.getElementById('add-menu-available').value = '0';
        } else if (stockVal > 0) {
            document.getElementById('add-menu-available').value = '1';
        }
    });

    document.getElementById('add-menu-available').addEventListener('change', function() {
        const availableVal = this.value;
        const stockInput = document.getElementById('add-menu-stock');
        const stockVal = parseInt(stockInput.value) || 0;
        if (availableVal === '0') {
            stockInput.value = '0';
        } else if (availableVal === '1' && stockVal === 0) {
            stockInput.value = '1';
        }
    });

    // Sync Edit Menu Modal fields
    document.getElementById('edit-menu-stock').addEventListener('input', function() {
        const stockVal = parseInt(this.value) || 0;
        if (stockVal === 0) {
            document.getElementById('edit-menu-available').value = '0';
        } else if (stockVal > 0) {
            document.getElementById('edit-menu-available').value = '1';
        }
    });

    document.getElementById('edit-menu-available').addEventListener('change', function() {
        const availableVal = this.value;
        const stockInput = document.getElementById('edit-menu-stock');
        const stockVal = parseInt(stockInput.value) || 0;
        if (availableVal === '0') {
            stockInput.value = '0';
        } else if (availableVal === '1' && stockVal === 0) {
            stockInput.value = '1';
        }
    });

    function previewImage(input, previewId, placeholderId) {
        const file = input.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const preview = document.getElementById(previewId);
                const placeholder = document.getElementById(placeholderId);
                preview.src = e.target.result;
                preview.classList.remove('hidden');
                placeholder.classList.add('hidden');
            }
            reader.readAsDataURL(file);
        }
    }

    function openEditMenuModal(menu) {
        const form = document.getElementById('edit-menu-form');
        form.action = `{{ route('admin-owner.menus.update', ':id') }}`.replace(':id', menu.id);
        
        document.getElementById('edit-menu-name').value = menu.name;
        document.getElementById('edit-menu-category').value = menu.category_id;
        document.getElementById('edit-menu-price').value = Math.round(menu.price);
        document.getElementById('edit-menu-stock').value = menu.stock;
        document.getElementById('edit-menu-available').value = menu.is_available ? '1' : '0';
        
        const preview = document.getElementById('edit-image-preview');
        const placeholder = document.getElementById('edit-image-placeholder');
        
        if (menu.image) {
            const isHttp = menu.image.startsWith('http://') || menu.image.startsWith('https://');
            preview.src = isHttp ? menu.image : `{{ asset('storage') }}/${menu.image}`;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
        } else {
            preview.src = '';
            preview.classList.add('hidden');
            placeholder.classList.remove('hidden');
        }
        
        document.getElementById('edit-menu-modal').classList.remove('hidden');
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
