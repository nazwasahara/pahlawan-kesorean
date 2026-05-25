<!-- Payment Method View - Full Page -->
<div id="paymentFullView" style="display:none;" class="flex flex-col h-full w-full">

    <!-- Payment Header -->
    <div class="flex items-center justify-between p-4 md:p-6 border-b border-gray-200 bg-white">
        <div>
            <button onclick="backToCheckout()" class="flex items-center gap-2 text-gray-600 hover:text-gray-900 font-medium text-sm md:text-base transition">
                <i class="ri-arrow-left-s-line text-lg"></i>
                <span>Kembali</span>
            </button>
        </div>
        <div class="text-right">
            <p class="text-xs text-gray-500 mb-1">POS (kasir)</p>
        </div>
    </div>

    <!-- Payment Content -->
    <div class="flex-1 flex items-start md:items-center justify-center p-4 md:p-6 bg-gradient-to-b from-gray-50 to-white overflow-y-auto">
        <div class="w-full max-w-2xl">
            <!-- Title -->
            <div class="text-center mb-6 md:mb-12 mt-4 md:mt-[50px]">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-3">Pilih Metode Pembayaran</h1>
                <p class="text-sm md:text-base text-gray-600">
                    Pilih metode pembayaran untuk menyelesaikan transaksi
                </p>
            </div>

            <!-- Payment Methods Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-xl mx-auto">
                <!-- Cash Method -->
                <div
                    class="payment-method-card rounded-xl bg-white border-2 border-gray-200 p-8 cursor-pointer hover:border-green-500 hover:shadow-lg transition-all text-center"
                    onclick="selectPayment('cash')"
                >
                    <div class="flex justify-center mb-6">
                        <div class="bg-green-100 rounded-full p-6 flex items-center justify-center">
                            <i class="ri-cash-fill text-5xl text-green-700"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Cash</h3>
                    <p class="text-sm text-gray-600">Bayar dengan uang tunai</p>
                </div>

                <!-- QRIS Method -->
                <div
                    class="payment-method-card rounded-xl bg-white border-2 border-gray-200 p-8 cursor-pointer hover:border-green-500 hover:shadow-lg transition-all text-center"
                    onclick="selectPayment('qris')"
                >
                    <div class="flex justify-center mb-6">
                        <div class="bg-green-100 rounded-full p-6 flex items-center justify-center">
                            <i class="ri-qr-code-fill text-5xl text-green-700"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">QRIS</h3>
                    <p class="text-sm text-gray-600">Bayar dengan Qr Code</p>
                </div>
            </div>
        </div>
    </div>

</div>
