@extends('admin-owner.layouts.app')

@section('title', 'Manajemen Promo - Pahlawan Kesorean')

@section('navbar-title', 'MANAJEMEN PROMO')

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
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Data Promo</h1>
            <p class="text-xs font-bold text-gray-500 mt-1">Kelola dan pantau kode promo belanja di sistem POS.</p>
        </div>
        @if(auth()->user()->role === 'admin')
        <div>
            <button onclick="document.getElementById('add-promo-modal').classList.remove('hidden')" 
                    class="bg-[#125E34] hover:bg-[#0E4A28] text-white px-5 py-2.5 rounded-xl font-black text-xs tracking-wider flex items-center gap-2 shadow-sm transition duration-150">
                <i class="ri-add-line text-base"></i>
                <span>Tambah Promo</span>
            </button>
        </div>
        @endif
    </div>

    <!-- Search Section -->
    <div class="flex justify-end">
        <form action="{{ route('admin-owner.promos.index') }}" method="GET" class="flex items-center gap-2 w-full max-w-sm">
            <div class="relative flex-1">
                <input type="text" 
                       name="search" 
                       value="{{ $search }}"
                       placeholder="Cari Kode Promo.." 
                       class="w-full bg-white border border-gray-200 rounded-xl py-2 px-5 pr-10 text-xs font-semibold text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34]">
                @if($search)
                    <a href="{{ route('admin-owner.promos.index') }}" class="absolute right-3 top-1 text-gray-400 hover:text-gray-700">
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
            <table class="w-full text-left border-collapse border border-gray-200">
                <thead>
                    <tr class="bg-[#F2F2F2] border-b border-gray-200 text-xs font-extrabold text-gray-900 tracking-wider">
                        <th class="py-3 px-4 border border-gray-200 w-16 text-center">No</th>
                        <th class="py-3 px-4 border border-gray-200 text-center">Kode</th>
                        <th class="py-3 px-4 border border-gray-200 text-center">Tipe</th>
                        <th class="py-3 px-4 border border-gray-200 text-center">Nilai Diskon</th>
                        <th class="py-3 px-4 border border-gray-200 text-center">Min Belanja</th>
                        <th class="py-3 px-4 border border-gray-200 text-center">Max Potongan</th>
                        <th class="py-3 px-4 border border-gray-200 text-center">Kuota (Terpakai/Total)</th>
                        <th class="py-3 px-4 border border-gray-200 text-center">Kedaluwarsa</th>
                        <th class="py-3 px-4 border border-gray-200 text-center w-24">Status</th>
                        @if(auth()->user()->role === 'admin')
                        <th class="py-3 px-4 border border-gray-200 text-center w-44">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-150 text-xs font-bold text-gray-850 bg-white">
                    @forelse($promos as $promo)
                        <tr class="hover:bg-gray-50/50 transition">
                            <!-- No -->
                            <td class="py-3 px-4 border border-gray-200 text-center font-bold text-gray-900">
                                {{ ($promos->currentPage() - 1) * $promos->perPage() + $loop->iteration }}
                            </td>
                            
                            <!-- Kode -->
                            <td class="py-3 px-4 border border-gray-200 font-extrabold text-[#125E34] uppercase tracking-wider">
                                {{ $promo->code }}
                            </td>
                            
                            <!-- Tipe -->
                            <td class="py-3 px-4 border border-gray-200 font-bold">
                                {{ $promo->type == 'percentage' ? 'Persentase (%)' : 'Potongan Tetap (Nominal)' }}
                            </td>

                            <!-- Nilai Diskon -->
                            <td class="py-3 px-4 border border-gray-200 text-right font-bold text-gray-900">
                                {{ $promo->type == 'percentage' ? number_format($promo->value, 0) . '%' : 'Rp ' . number_format($promo->value, 0, ',', '.') }}
                            </td>

                            <!-- Min Belanja -->
                            <td class="py-3 px-4 border border-gray-200 text-right font-bold text-gray-700">
                                Rp {{ number_format($promo->minimum_transaction, 0, ',', '.') }}
                            </td>

                            <!-- Max Potongan -->
                            <td class="py-3 px-4 border border-gray-200 text-right font-bold text-gray-700">
                                {{ $promo->max_discount ? 'Rp ' . number_format($promo->max_discount, 0, ',', '.') : '-' }}
                            </td>

                            <!-- Kuota (Terpakai/Total) -->
                            <td class="py-3 px-4 border border-gray-200 text-center font-bold">
                                <span class="text-green-700 font-extrabold">{{ $promo->used_count }}</span>
                                <span class="text-gray-400">/</span>
                                <span class="text-gray-800">{{ $promo->quota ?? '∞' }}</span>
                            </td>

                            <!-- Kedaluwarsa -->
                            <td class="py-3 px-4 border border-gray-200 text-center font-bold text-gray-500">
                                {{ $promo->expired_at ? $promo->expired_at->format('Y-m-d') : 'Tidak Ada' }}
                            </td>

                            <!-- Status -->
                            <td class="py-3 px-4 border border-gray-200 text-center">
                                @if(auth()->user()->role === 'admin')
                                    <form action="{{ route('admin-owner.promos.toggle-status', $promo->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                                class="px-2.5 py-1 rounded-full text-[10px] font-black tracking-wider transition uppercase cursor-pointer
                                                    {{ $promo->is_active ? 'bg-emerald-100 text-emerald-800 hover:bg-emerald-200' : 'bg-red-100 text-red-800 hover:bg-red-200' }}">
                                            {{ $promo->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </button>
                                    </form>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black tracking-wider uppercase
                                                {{ $promo->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $promo->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                @endif
                            </td>
                            
                            <!-- Aksi Buttons -->
                            @if(auth()->user()->role === 'admin')
                            <td class="py-3 px-4 border border-gray-200">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Edit Button -->
                                    <button onclick='openEditPromoModal(@json($promo))' 
                                            class="px-2.5 py-1.5 bg-[#EF6C00] hover:bg-[#E65100] text-white text-xs font-bold rounded-lg transition flex items-center gap-1 shadow-sm">
                                        <i class="ri-edit-line text-sm"></i>
                                        <span>Edit</span>
                                    </button>
                                    
                                    <!-- Delete Button -->
                                    <button type="button" 
                                            onclick="confirmDelete('{{ route('admin-owner.promos.destroy', $promo->id) }}', 'Apakah Anda yakin ingin menghapus kode promo &ldquo;{{ $promo->code }}&rdquo;?')"
                                            class="px-2.5 py-1.5 bg-[#D32F2F] hover:bg-[#B71C1C] text-white text-xs font-bold rounded-lg transition flex items-center gap-1 shadow-sm">
                                        <i class="ri-delete-bin-line text-sm"></i>
                                        <span>Hapus</span>
                                    </button>
                                </div>
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ auth()->user()->role === 'admin' ? '10' : '9' }}" class="py-12 text-center text-gray-400 font-bold border border-gray-200">
                                Tidak ada data promo ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        @if($promos->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 mt-4">
                {{ $promos->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Tambah Promo Modal -->
<div id="add-promo-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="fixed inset-0 bg-black/50 transition-opacity" onclick="document.getElementById('add-promo-modal').classList.add('hidden')"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative transform overflow-hidden rounded-[1rem] bg-[#F4F3EB] border border-gray-200/40 p-8 text-left shadow-2xl transition-all w-full max-w-md">
            
            <div class="mb-6">
                <h3 class="text-2xl font-black text-gray-900 tracking-tight">Tambah Promo</h3>
                <p class="text-xs text-gray-650 mt-1 font-bold">Buat kode promo belanja baru.</p>
            </div>

            <form action="{{ route('admin-owner.promos.store') }}" method="POST" class="space-y-4 text-xs font-bold">
                @csrf

                <!-- Kode Promo -->
                <div>
                    <label class="block text-sm font-bold text-[#125E34] mb-1.5">Kode Promo</label>
                    <input type="text" 
                           name="code" 
                           required 
                           placeholder="Contoh: MERDEKA50" 
                           class="w-full bg-white border border-gray-300 rounded-xl py-2.5 px-4 text-xs font-bold text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34] uppercase">
                </div>

                <!-- Tipe Promo -->
                <div>
                    <label class="block text-sm font-bold text-[#125E34] mb-1.5">Tipe Diskon</label>
                    <select name="type" 
                            required
                            class="w-full bg-white border border-gray-300 rounded-xl py-2.5 px-4 text-xs font-bold text-gray-800 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34]">
                        <option value="percentage">Persentase (%)</option>
                        <option value="fixed">Nominal Potongan Tetap (Rp)</option>
                    </select>
                </div>

                <!-- Nilai Diskon -->
                <div>
                    <label class="block text-sm font-bold text-[#125E34] mb-1.5">Nilai Diskon</label>
                    <input type="number" 
                           name="value" 
                           required 
                           step="0.01"
                           placeholder="Masukkan nominal / persentase diskon" 
                           class="w-full bg-white border border-gray-300 rounded-xl py-2.5 px-4 text-xs font-bold text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34]">
                </div>

                <!-- Minimum Belanja -->
                <div>
                    <label class="block text-sm font-bold text-[#125E34] mb-1.5">Minimum Belanja (Rp)</label>
                    <input type="number" 
                           name="minimum_transaction" 
                           step="0.01"
                           value="0"
                           placeholder="0" 
                           class="w-full bg-white border border-gray-300 rounded-xl py-2.5 px-4 text-xs font-bold text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34]">
                </div>

                <!-- Maksimal Potongan -->
                <div>
                    <label class="block text-sm font-bold text-[#125E34] mb-1.5">Maksimal Potongan Diskon (Rp - Opsional)</label>
                    <input type="number" 
                           name="max_discount" 
                           step="0.01"
                           placeholder="Batas nominal diskon maksimal" 
                           class="w-full bg-white border border-gray-300 rounded-xl py-2.5 px-4 text-xs font-bold text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34]">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Kuota -->
                    <div>
                        <label class="block text-sm font-bold text-[#125E34] mb-1.5">Kuota (Opsional)</label>
                        <input type="number" 
                               name="quota" 
                               placeholder="Contoh: 100" 
                               class="w-full bg-white border border-gray-300 rounded-xl py-2.5 px-4 text-xs font-bold text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34]">
                    </div>

                    <!-- Masa Berlaku -->
                    <div>
                        <label class="block text-sm font-bold text-[#125E34] mb-1.5">Hingga Tanggal</label>
                        <input type="date" 
                               name="expired_at" 
                               class="w-full bg-white border border-gray-300 rounded-xl py-2.5 px-4 text-xs font-bold text-gray-800 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34]">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200/40">
                    <button type="button" 
                            onclick="document.getElementById('add-promo-modal').classList.add('hidden')"
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

<!-- Edit Promo Modal -->
<div id="edit-promo-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="fixed inset-0 bg-black/50 transition-opacity" onclick="document.getElementById('edit-promo-modal').classList.add('hidden')"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative transform overflow-hidden rounded-[1rem] bg-[#F4F3EB] border border-gray-200/40 p-8 text-left shadow-2xl transition-all w-full max-w-md">
            
            <div class="mb-6">
                <h3 class="text-2xl font-black text-gray-900 tracking-tight">Edit Promo</h3>
                <p class="text-xs text-gray-650 mt-1 font-bold">Ubah informasi kode promo yang dipilih.</p>
            </div>

            <form id="edit-promo-form" action="" method="POST" class="space-y-4 text-xs font-bold">
                @csrf

                <!-- Kode Promo -->
                <div>
                    <label class="block text-sm font-bold text-[#125E34] mb-1.5">Kode Promo</label>
                    <input type="text" 
                           id="edit-promo-code"
                           name="code" 
                           required 
                           placeholder="Contoh: MERDEKA50" 
                           class="w-full bg-white border border-gray-300 rounded-xl py-2.5 px-4 text-xs font-bold text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34] uppercase">
                </div>

                <!-- Tipe Promo -->
                <div>
                    <label class="block text-sm font-bold text-[#125E34] mb-1.5">Tipe Diskon</label>
                    <select name="type" 
                            id="edit-promo-type"
                            required
                            class="w-full bg-white border border-gray-300 rounded-xl py-2.5 px-4 text-xs font-bold text-gray-800 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34]">
                        <option value="percentage">Persentase (%)</option>
                        <option value="fixed">Nominal Potongan Tetap (Rp)</option>
                    </select>
                </div>

                <!-- Nilai Diskon -->
                <div>
                    <label class="block text-sm font-bold text-[#125E34] mb-1.5">Nilai Diskon</label>
                    <input type="number" 
                           name="value" 
                           id="edit-promo-value"
                           required 
                           step="0.01"
                           placeholder="Masukkan nominal / persentase diskon" 
                           class="w-full bg-white border border-gray-300 rounded-xl py-2.5 px-4 text-xs font-bold text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34]">
                </div>

                <!-- Minimum Belanja -->
                <div>
                    <label class="block text-sm font-bold text-[#125E34] mb-1.5">Minimum Belanja (Rp)</label>
                    <input type="number" 
                           name="minimum_transaction" 
                           id="edit-promo-min-transaction"
                           step="0.01"
                           placeholder="0" 
                           class="w-full bg-white border border-gray-300 rounded-xl py-2.5 px-4 text-xs font-bold text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34]">
                </div>

                <!-- Maksimal Potongan -->
                <div>
                    <label class="block text-sm font-bold text-[#125E34] mb-1.5">Maksimal Potongan Diskon (Rp - Opsional)</label>
                    <input type="number" 
                           name="max_discount" 
                           id="edit-promo-max-discount"
                           step="0.01"
                           placeholder="Batas nominal diskon maksimal" 
                           class="w-full bg-white border border-gray-300 rounded-xl py-2.5 px-4 text-xs font-bold text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34]">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <!-- Kuota -->
                    <div>
                        <label class="block text-sm font-bold text-[#125E34] mb-1.5">Kuota (Opsional)</label>
                        <input type="number" 
                               name="quota" 
                               id="edit-promo-quota"
                               placeholder="Contoh: 100" 
                               class="w-full bg-white border border-gray-300 rounded-xl py-2.5 px-4 text-xs font-bold text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34]">
                    </div>

                    <!-- Masa Berlaku -->
                    <div>
                        <label class="block text-sm font-bold text-[#125E34] mb-1.5">Hingga Tanggal</label>
                        <input type="date" 
                               name="expired_at" 
                               id="edit-promo-expired-at"
                               class="w-full bg-white border border-gray-300 rounded-xl py-2.5 px-4 text-xs font-bold text-gray-800 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34]">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200/40">
                    <button type="button" 
                            onclick="document.getElementById('edit-promo-modal').classList.add('hidden')"
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

<!-- Delete Confirmation Modal -->
<div id="delete-confirm-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
    <div class="fixed inset-0 bg-black/50 transition-opacity" onclick="closeDeleteModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative transform overflow-hidden rounded-[1rem] bg-white border border-gray-200 p-8 text-center shadow-2xl transition-all w-full max-w-sm">
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
    function openEditPromoModal(promo) {
        const form = document.getElementById('edit-promo-form');
        form.action = `{{ route('admin-owner.promos.update', ':id') }}`.replace(':id', promo.id);
        
        document.getElementById('edit-promo-code').value = promo.code;
        document.getElementById('edit-promo-type').value = promo.type;
        document.getElementById('edit-promo-value').value = promo.value;
        document.getElementById('edit-promo-min-transaction').value = promo.minimum_transaction;
        document.getElementById('edit-promo-max-discount').value = promo.max_discount || '';
        document.getElementById('edit-promo-quota').value = promo.quota || '';
        
        if (promo.expired_at) {
            const dateStr = promo.expired_at.substring(0, 10);
            document.getElementById('edit-promo-expired-at').value = dateStr;
        } else {
            document.getElementById('edit-promo-expired-at').value = '';
        }
        
        document.getElementById('edit-promo-modal').classList.remove('hidden');
    }

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
