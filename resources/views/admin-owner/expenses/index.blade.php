@extends('admin-owner.layouts.app')

@section('title', 'Manajemen Pengeluaran - Pahlawan Kesorean')

@section('navbar-title', 'MANAJEMEN PENGELUARAN')

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
            <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Riwayat Pengeluaran</h1>
            <p class="text-xs font-bold text-gray-500 mt-1">Kelola dan pantau seluruh riwayat pengeluaran dalam sistem.</p>
        </div>
        @if(auth()->user()->role === 'admin')
            <div>
                <button onclick="document.getElementById('add-expense-modal').classList.remove('hidden')" 
                        class="bg-[#125E34] hover:bg-[#0E4A28] text-white px-5 py-2.5 rounded-xl font-black text-xs tracking-wider flex items-center gap-2 shadow-sm transition duration-150">
                    <i class="ri-add-line text-base"></i>
                    <span>Tambah Pengeluaran</span>
                </button>
            </div>
        @endif
    </div>

    <!-- Filters & Total Section -->
    <div class="flex flex-wrap items-center justify-end gap-3">
        <form action="{{ route('admin-owner.expenses.index') }}" method="GET" class="flex flex-wrap items-center gap-3 w-full max-w-2xl justify-end">
            <!-- Search Input -->
            <div class="relative w-64">
                <input type="text" 
                       name="search" 
                       value="{{ $search }}"
                       placeholder="Cari Keterangan.." 
                       class="w-full bg-white border border-gray-200 rounded-xl py-2 px-5 text-xs font-bold text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34]">
                @if($search)
                    <a href="{{ route('admin-owner.expenses.index') }}" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-450 hover:text-gray-700 flex items-center justify-center">
                        <i class="ri-close-line text-sm"></i>
                    </a>
                @endif
            </div>

            <!-- Search Button -->
            <button type="submit" class="w-9 h-9 bg-[#125E34] hover:bg-[#0E4A28] text-white rounded-xl flex items-center justify-center shadow transition-colors shrink-0">
                <i class="ri-search-line text-base"></i>
            </button>

            <!-- Date Picker -->
            <div class="relative">
                <input type="date" 
                       name="date" 
                       value="{{ $date }}"
                       onchange="this.form.submit()"
                       class="bg-white border border-gray-200 rounded-xl py-2 px-5 text-xs font-bold text-gray-800 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34] cursor-pointer">
            </div>

            <!-- Total Card -->
            <div class="bg-[#FFEBEE] border border-[#FFCDD2] text-[#C62828] px-5 py-2 rounded-xl text-xs font-black tracking-wide flex items-center shadow-sm">
                <span>TOTAL: Rp{{ number_format($totalAmount, 0, ',', '.') }}</span>
            </div>
        </form>
    </div>

    <!-- Data Table Card Container -->
    <div class="bg-white border border-gray-200/80 rounded-[1rem] p-6 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse border border-gray-200">
                <thead>
                    <tr class="bg-[#F2F2F2] border-b border-gray-200 text-xs font-extrabold text-gray-900 tracking-wider">
                        <th class="py-3 px-4 border border-gray-200 text-center w-16">No</th>
                        <th class="py-3 px-4 border border-gray-200 text-center w-36">Tanggal</th>
                        <th class="py-3 px-4 border border-gray-200 text-center">Keterangan</th>
                        <th class="py-3 px-4 border border-gray-200 text-center w-44">Jumlah(Rp)</th>
                        @if(auth()->user()->role === 'admin')
                            <th class="py-3 px-4 border border-gray-200 text-center w-48">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-150 text-xs font-bold text-gray-800 bg-white">
                    @forelse($expenses as $index => $expense)
                        <tr class="hover:bg-gray-50/50 transition">
                            <!-- No -->
                            <td class="py-3 px-4 border border-gray-200 text-center text-gray-900">
                                {{ ($expenses->currentPage() - 1) * $expenses->perPage() + $loop->iteration }}
                            </td>
                            
                            <!-- Tanggal -->
                            <td class="py-3 px-4 border border-gray-200 text-center text-gray-800">
                                {{ \Carbon\Carbon::parse($expense->date)->translatedFormat('d M Y') }}
                            </td>
                            
                            <!-- Keterangan -->
                            <td class="py-3 px-4 border border-gray-200 text-gray-900 font-bold">
                                {{ $expense->description }}
                            </td>
                            
                            <!-- Jumlah -->
                            <td class="py-3 px-4 border border-gray-200 text-right pr-6 text-gray-900 font-bold">
                                {{ number_format($expense->amount, 0, ',', '.') }}
                            </td>
                            
                            <!-- Aksi Buttons (Admin Only) -->
                            @if(auth()->user()->role === 'admin')
                                <td class="py-3 px-4 border border-gray-200">
                                    <div class="flex items-center justify-center gap-2">
                                        <!-- Edit Button -->
                                        <button onclick='openEditExpenseModal(@json($expense))' 
                                                class="px-3 py-1.5 bg-[#EF6C00] hover:bg-[#E65100] text-white text-xs font-bold rounded-lg transition-colors flex items-center gap-1 shadow-sm">
                                            <i class="ri-edit-line text-sm"></i>
                                            <span>Edit</span>
                                        </button>
                                        
                                        <!-- Delete Button -->
                                        <button type="button" 
                                                onclick="confirmDelete('{{ route('admin-owner.expenses.destroy', $expense->id) }}', 'Apakah Anda yakin ingin menghapus pengeluaran &ldquo;{{ $expense->description }}&rdquo;?')"
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
                            <td colspan="{{ auth()->user()->role === 'admin' ? 5 : 4 }}" class="py-12 text-center text-gray-400 font-bold border border-gray-200">
                                Tidak ada data pengeluaran ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Links -->
        @if($expenses->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 mt-4">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>
</div>

@if(auth()->user()->role === 'admin')
    <!-- Tambah Pengeluaran Modal -->
    <div id="add-expense-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/50 transition-opacity" onclick="document.getElementById('add-expense-modal').classList.add('hidden')"></div>
        
        <!-- Modal Content Wrapper -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-[1rem] bg-[#F4F3EB] border border-gray-200/40 p-8 text-left shadow-2xl transition-all w-full max-w-md">
                
                <div class="mb-6">
                    <h3 class="text-2xl font-black text-gray-900 tracking-tight">Tambah Pengeluaran</h3>
                    <p class="text-xs text-gray-600 mt-1">Catat transaksi pengeluaran operasional baru.</p>
                </div>

                <form action="{{ route('admin-owner.expenses.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- Tanggal -->
                    <div>
                        <label class="block text-sm font-bold text-[#125E34] mb-1.5">Tanggal</label>
                        <input type="date" 
                               name="date" 
                               required 
                               value="{{ date('Y-m-d') }}"
                               class="w-full bg-white border border-gray-300 rounded-xl py-2.5 px-4 text-xs font-bold text-gray-800 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34]">
                    </div>

                    <!-- Keterangan -->
                    <div>
                        <label class="block text-sm font-bold text-[#125E34] mb-1.5">Keterangan</label>
                        <input type="text" 
                               name="description" 
                               required 
                               placeholder="Contoh: Beli kopi" 
                               class="w-full bg-white border border-gray-300 rounded-xl py-2.5 px-4 text-xs font-bold text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34]">
                    </div>

                    <!-- Jumlah -->
                    <div>
                        <label class="block text-sm font-bold text-[#125E34] mb-1.5">Jumlah (Rp)</label>
                        <input type="number" 
                               name="amount" 
                               required 
                               min="0"
                               placeholder="Contoh: 100000" 
                               class="w-full bg-white border border-gray-300 rounded-xl py-2.5 px-4 text-xs font-bold text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34]">
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200/40">
                        <button type="button" 
                                onclick="document.getElementById('add-expense-modal').classList.add('hidden')"
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

    <!-- Edit Pengeluaran Modal -->
    <div id="edit-expense-modal" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/50 transition-opacity" onclick="document.getElementById('edit-expense-modal').classList.add('hidden')"></div>
        
        <!-- Modal Content Wrapper -->
        <div class="flex min-h-full items-center justify-center p-4">
            <div class="relative transform overflow-hidden rounded-[1rem] bg-[#F4F3EB] border border-gray-200/40 p-8 text-left shadow-2xl transition-all w-full max-w-md">
                
                <div class="mb-6">
                    <h3 class="text-2xl font-black text-gray-900 tracking-tight">Edit Pengeluaran</h3>
                    <p class="text-xs text-gray-600 mt-1">Perbarui data pengeluaran operasional yang dipilih.</p>
                </div>

                <form id="edit-expense-form" action="" method="POST" class="space-y-4">
                    @csrf
                    <!-- action URL set via JS -->

                    <!-- Tanggal -->
                    <div>
                        <label class="block text-sm font-bold text-[#125E34] mb-1.5">Tanggal</label>
                        <input type="date" 
                               id="edit-expense-date"
                               name="date" 
                               required 
                               class="w-full bg-white border border-gray-300 rounded-xl py-2.5 px-4 text-xs font-bold text-gray-800 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34]">
                    </div>

                    <!-- Keterangan -->
                    <div>
                        <label class="block text-sm font-bold text-[#125E34] mb-1.5">Keterangan</label>
                        <input type="text" 
                               id="edit-expense-description"
                               name="description" 
                               required 
                               placeholder="Contoh: Beli kopi" 
                               class="w-full bg-white border border-gray-300 rounded-xl py-2.5 px-4 text-xs font-bold text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34]">
                    </div>

                    <!-- Jumlah -->
                    <div>
                        <label class="block text-sm font-bold text-[#125E34] mb-1.5">Jumlah (Rp)</label>
                        <input type="number" 
                               id="edit-expense-amount"
                               name="amount" 
                               required 
                               min="0"
                               placeholder="Contoh: 100000" 
                               class="w-full bg-white border border-gray-300 rounded-xl py-2.5 px-4 text-xs font-bold text-gray-800 placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-[#125E34] focus:border-[#125E34]">
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200/40">
                        <button type="button" 
                                onclick="document.getElementById('edit-expense-modal').classList.add('hidden')"
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
        function openEditExpenseModal(expense) {
            const form = document.getElementById('edit-expense-form');
            form.action = `{{ route('admin-owner.expenses.update', ':id') }}`.replace(':id', expense.id);
            
            // Format date to YYYY-MM-DD
            let dateVal = expense.date;
            if (typeof dateVal === 'string' && dateVal.includes('T')) {
                dateVal = dateVal.split('T')[0];
            }
            
            document.getElementById('edit-expense-date').value = dateVal;
            document.getElementById('edit-expense-description').value = expense.description;
            document.getElementById('edit-expense-amount').value = Math.round(expense.amount);
            
            document.getElementById('edit-expense-modal').classList.remove('hidden');
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
@endif
@endsection
