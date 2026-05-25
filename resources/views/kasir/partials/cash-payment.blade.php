<!-- Cash Payment View -->
<div id="cashPaymentView" style="display:none;" class="flex flex-col h-full w-full">

    <!-- Payment Header -->
    <div class="flex items-center justify-between p-4 md:p-6 border-b border-gray-200 bg-white">
        <div>
            <button onclick="backToPaymentMethod()" class="flex items-center gap-2 text-gray-600 hover:text-gray-900 font-medium text-sm md:text-base transition">
                <i class="ri-arrow-left-s-line text-lg"></i>
                <span>Kembali</span>
            </button>
        </div>
        <div class="text-right">
            <p class="text-xs text-gray-500">POS (kasir)</p>
        </div>
    </div>

    <!-- Payment Content -->
    <div class="flex-1 flex flex-col md:flex-row gap-4 md:gap-6 p-4 md:p-6 overflow-y-auto">

        <!-- Main Content -->
        <div class="flex-1 min-w-0">
            <div class="bg-white rounded-lg border border-gray-200 p-6 md:p-8">
                <!-- Title -->
                <div class="mb-8">
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">Pembayaran Cash</h1>
                    <p class="text-sm text-gray-600">Pilih metode pembayaran untuk menyelesaikan transaksi</p>
                </div>

                <!-- Preset Amounts -->
                <div class="mb-8">
                    <label class="block text-sm font-semibold text-gray-900 mb-4">Uang Diterima</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                        <button onclick="setMoneyReceived(20000)" class="py-3 px-4 border-2 border-gray-200 rounded-lg font-semibold text-gray-700 hover:border-green-500 hover:text-green-700 hover:bg-green-50 transition">
                            Rp 20.000
                        </button>
                        <button onclick="setMoneyReceived(50000)" class="py-3 px-4 border-2 border-gray-200 rounded-lg font-semibold text-gray-700 hover:border-green-500 hover:text-green-700 hover:bg-green-50 transition">
                            Rp 50.000
                        </button>
                        <button onclick="setMoneyReceived(100000)" class="py-3 px-4 border-2 border-gray-200 rounded-lg font-semibold text-gray-700 hover:border-green-500 hover:text-green-700 hover:bg-green-50 transition">
                            Rp 100.000
                        </button>
                        <button onclick="setMoneyReceived(150000)" class="py-3 px-4 border-2 border-gray-200 rounded-lg font-semibold text-gray-700 hover:border-green-500 hover:text-green-700 hover:bg-green-50 transition">
                            Rp 150.000
                        </button>
                        <button onclick="setMoneyReceived(200000)" class="py-3 px-4 border-2 border-gray-200 rounded-lg font-semibold text-gray-700 hover:border-green-500 hover:text-green-700 hover:bg-green-50 transition">
                            Rp 200.000
                        </button>
                        <button onclick="setMoneyReceived(500000)" class="py-3 px-4 border-2 border-gray-200 rounded-lg font-semibold text-gray-700 hover:border-green-500 hover:text-green-700 hover:bg-green-50 transition">
                            Rp 500.000
                        </button>
                    </div>
                </div>

                <!-- Custom Amount Input -->
                <div class="mb-8">
                    <label class="block text-sm font-semibold text-gray-900 mb-3">Masukkan nominal lain</label>
                    <div class="flex gap-2">
                        <div class="flex-1 relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm font-medium">Rp</span>
                            <input
                                type="number"
                                id="customMoneyInput"
                                placeholder="0"
                                oninput="calculateChange()"
                                class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-lg text-lg font-semibold focus:outline-none focus:ring-2 focus:ring-green-400 focus:border-transparent"
                            >
                        </div>
                    </div>
                </div>

                <!-- Change Calculation -->
                <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                    <p class="text-sm text-gray-600 mb-2">Kembalian</p>
                    <p class="text-4xl font-bold text-green-700" id="changeAmount">Rp 0</p>
                </div>
            </div>
        </div>

        <!-- Order Summary Sidebar -->
        <div class="w-full md:w-80 lg:w-96 flex-shrink-0">
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden sticky top-6">
                <!-- Customer Info -->
                <div class="p-4 border-b border-gray-200">
                    <div class="flex justify-between mb-3">
                        <span class="text-sm text-gray-600">Pelanggan</span>
                        <span class="text-sm font-semibold text-gray-900" id="cashCustomerName">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600">Meja</span>
                        <span class="text-sm font-semibold text-gray-900" id="cashTableNumber">-</span>
                    </div>
                </div>

                <!-- Items -->
                <div class="border-b border-gray-200">
                    <div class="max-h-64 overflow-y-auto" id="cashItemsList">
                        <!-- Items will be rendered here -->
                    </div>
                </div>

                <!-- Totals -->
                <div class="p-4 space-y-3 bg-gray-50">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-semibold text-gray-900" id="cashSubtotal">Rp 0</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Diskon</span>
                        <span class="font-semibold text-gray-900">Rp 0</span>
                    </div>
                    <div class="pt-3 border-t border-gray-200 flex justify-between">
                        <span class="font-semibold text-gray-900">Total</span>
                        <span class="text-lg font-bold text-green-700" id="cashTotal">Rp 0</span>
                    </div>
                </div>

                <!-- Confirm Button -->
                <div class="p-4 border-t border-gray-200">
                    <button
                        onclick="confirmCashPayment()"
                        class="w-full py-3 bg-green-800 hover:bg-green-900 text-white font-semibold rounded-lg transition"
                    >
                        Konfirmasi Pembayaran
                    </button>
                </div>
            </div>
        </div>

    </div>

</div>
