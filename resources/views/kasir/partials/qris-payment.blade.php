<!-- QRIS Payment View -->
<div id="qrisPaymentView" style="display:none;" class="flex flex-col h-full w-full">

    <style>
        @keyframes scan-animation {
            0% { top: 0%; }
            50% { top: 100%; }
            100% { top: 0%; }
        }
        .qris-scan-line {
            position: absolute;
            left: 0;
            right: 0;
            height: 3px;
            background: rgba(34, 197, 94, 0.8);
            box-shadow: 0 0 10px 4px rgba(34, 197, 94, 0.6);
            animation: scan-animation 3s linear infinite;
            pointer-events: none;
        }
        /* Custom QRIS logo colors */
        .qris-logo-blue { color: #005CA9; }
        .qris-logo-red { color: #EE2E24; }
    </style>

    <!-- Header -->
    <div class="flex items-center justify-between p-4 md:p-6 border-b border-gray-200 bg-white">
        <div>
            <button onclick="backToPaymentMethodFromQRIS()" class="flex items-center gap-2 text-gray-600 hover:text-gray-900 font-medium text-sm md:text-base transition">
                <i class="ri-arrow-left-s-line text-lg"></i>
                <span>Kembali</span>
            </button>
        </div>
        <div class="text-right">
            <p class="text-xs text-gray-500">Pembayaran Digital</p>
        </div>
    </div>

    <!-- Content Split Layout -->
    <div class="flex-1 flex flex-col md:flex-row gap-4 md:gap-6 p-4 md:p-6 overflow-y-auto bg-gray-50">
        
        <!-- Main Area: QRIS Poster & Controls -->
        <div class="flex-1 min-w-0 flex flex-col items-center justify-start md:py-2">

            <!-- Beautiful QRIS Poster -->
            <div class="w-full max-w-sm bg-white rounded-2xl border border-gray-200 shadow-md flex-shrink-0 flex flex-col overflow-hidden">
                
                <!-- Poster Header (Official Look) -->
                <div class="bg-slate-900 text-white py-4 px-6 text-center relative flex-shrink-0">
                    <!-- Ribbon Strip -->
                    <div class="absolute bottom-0 left-0 right-0 h-1.5 flex">
                        <div class="flex-1 bg-[#005CA9]"></div>
                        <div class="flex-1 bg-[#EE2E24]"></div>
                    </div>
                    
                    <div class="flex items-center justify-center gap-1.5 mb-1">
                        <span class="text-2xl font-black italic tracking-tighter">
                            <span class="qris-logo-blue">QR</span><span class="qris-logo-red">IS</span>
                        </span>
                        <span class="text-[9px] text-gray-400 font-medium leading-none text-left">
                            Quick Response Code<br>Indonesian Standard
                        </span>
                    </div>
                    <h2 class="text-sm font-bold tracking-wide uppercase text-gray-200">PAHLAWAN KESOREAN</h2>
                    <p class="text-[10px] text-gray-400">NMID: ID1020021570535</p>
                </div>

                <!-- Poster Body -->
                <div class="p-6 flex flex-col items-center bg-white text-center flex-shrink-0">
                    
                    <!-- Scan Area -->
                    <div class="relative bg-white rounded-xl p-3 border border-gray-200 shadow-sm inline-block">
                        <div class="w-[220px] h-[220px] relative overflow-hidden flex items-center justify-center bg-white">
                            <!-- Scanning Laser -->
                            <div class="qris-scan-line"></div>
                            
                            <!-- QR Canvas -->
                            <canvas id="qrisCanvas" width="200" height="200"></canvas>
                            
                            <!-- Expired Overlay -->
                            <div id="qrisExpiredOverlay" style="display:none;" class="absolute inset-0 bg-white/95 flex flex-col items-center justify-center p-4">
                                <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center text-red-600 mb-2">
                                    <i class="ri-error-warning-line text-2xl"></i>
                                </div>
                                <p class="text-sm font-bold text-gray-900">QR Code Kedaluwarsa</p>
                                <div class="flex gap-2 mt-3 justify-center w-full">
                                    <button onclick="regenerateQRIS()" class="text-xs bg-green-700 hover:bg-green-800 text-white font-semibold py-1.5 px-3 rounded transition">
                                        Buat QR Baru
                                    </button>
                                    <button onclick="cancelQRISOrder()" class="text-xs bg-red-600 hover:bg-red-700 text-white font-semibold py-1.5 px-3 rounded transition">
                                        Batalkan
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Amount Display inside Poster (Dynamic only) -->
                    <div id="qrisAmountDisplay" class="mt-4">
                        <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Total Tagihan</p>
                        <p id="qrisAmountVal" class="text-2xl font-bold text-gray-900 mt-1">Rp 0</p>
                    </div>

                    <!-- Countdown and Instructions -->
                    <div class="mt-4 w-full">
                        <div class="flex items-center justify-between text-xs text-gray-500 mb-1 px-1">
                            <span class="flex items-center gap-1"><i class="ri-time-line text-gray-400"></i> Masa Berlaku</span>
                            <span id="qrisTimer" class="font-bold text-green-700">05:00</span>
                        </div>
                        <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                            <div id="qrisProgress" class="h-full bg-green-700 transition-all duration-1000" style="width: 100%;"></div>
                        </div>
                        <p id="qrisInstruction" class="text-[11px] text-gray-500 mt-3 text-center leading-relaxed">
                            Scan QR Code menggunakan E-Wallet atau mobile banking. Nominal pembayaran otomatis tertera.
                        </p>
                    </div>

                </div>

                <!-- Poster Footer (GPN and Partners) -->
                <div class="bg-gray-50 border-t border-gray-100 py-3 px-4 flex items-center justify-between">
                    <div class="flex items-center gap-1.5">
                        <!-- GPN-like Badge -->
                        <div class="bg-[#EE2E24] text-white text-[9px] font-black px-1.5 py-0.5 rounded italic leading-none">
                            GPN
                        </div>
                        <span class="text-[10px] text-gray-500 font-semibold">Semua E-Wallet</span>
                    </div>
                    <div class="flex gap-2 text-gray-400 text-sm">
                        <i class="ri-wallet-3-line" title="GoPay, OVO, DANA, dll"></i>
                        <i class="ri-bank-card-line" title="Mobile Banking"></i>
                    </div>
                </div>
            </div>

            <!-- Controls below Poster -->
            <div class="w-full max-w-sm mt-4 flex gap-2">
                <button onclick="downloadQRIS()" class="flex-1 py-2 px-3 bg-white border border-gray-300 hover:border-gray-400 text-gray-700 text-xs font-semibold rounded-lg flex items-center justify-center gap-1.5 transition">
                    <i class="ri-download-2-line"></i> Unduh QR Code
                </button>
                <button onclick="copyQRISPayload()" class="flex-1 py-2 px-3 bg-white border border-gray-300 hover:border-gray-400 text-gray-700 text-xs font-semibold rounded-lg flex items-center justify-center gap-1.5 transition">
                    <i class="ri-file-copy-line"></i> Salin Payload
                </button>
            </div>

            <!-- Hidden raw payload input for utility -->
            <input type="text" id="qrisRawPayload" class="sr-only" readonly value="">

        </div>

        <!-- Right Area: Order Summary Sidebar -->
        <div class="w-full md:w-80 lg:w-96 flex-shrink-0 flex flex-col bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden sticky top-6">
            
            <!-- Customer Info -->
            <div class="p-4 border-b border-gray-200">
                <h3 class="text-sm font-bold text-gray-900 mb-3 flex items-center gap-1.5">
                    <i class="ri-user-line text-gray-500"></i> Detail Pelanggan
                </h3>
                <div class="flex justify-between mb-2">
                    <span class="text-xs text-gray-500">Pelanggan</span>
                    <span class="text-xs font-semibold text-gray-900" id="qrisCustomerName">-</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-xs text-gray-500">Meja</span>
                    <span class="text-xs font-semibold text-gray-900" id="qrisTableNumber">-</span>
                </div>
            </div>

            <!-- Items list -->
            <div class="flex-1 overflow-y-auto border-b border-gray-200">
                <div id="qrisItemsList" class="divide-y divide-gray-50">
                    <!-- Items rendered dynamically -->
                </div>
            </div>

            <!-- Totals and Action -->
            <div class="p-4 space-y-3 bg-gray-50">
                <div class="flex justify-between text-xs">
                    <span class="text-gray-500">Subtotal</span>
                    <span class="font-semibold text-gray-900" id="qrisSubtotal">Rp 0</span>
                </div>
                <div class="flex justify-between text-xs">
                    <span class="text-gray-500">Diskon</span>
                    <span class="font-semibold text-gray-900">Rp 0</span>
                </div>
                <div class="pt-2 border-t border-gray-200 flex justify-between items-baseline">
                    <span class="text-sm font-bold text-gray-900">Total Tagihan</span>
                    <span class="text-xl font-extrabold text-green-700 animate-pulse" id="qrisTotal">Rp 0</span>
                </div>
            </div>

            <!-- Check Status (Automatic Info) -->
            <div class="p-4 border-t border-gray-200 bg-white">
                <div class="flex items-center justify-center gap-2 text-green-700 py-3 bg-green-50 rounded-xl border border-green-100 font-semibold text-sm">
                    <i class="ri-loader-4-line animate-spin text-lg"></i>
                    <span>Menunggu Pembayaran Pelanggan...</span>
                </div>
                <p class="text-[10px] text-gray-500 text-center mt-2">Sistem memverifikasi pembayaran secara otomatis</p>
            </div>
            
        </div>

    </div>

</div>

<!-- QR Code Generator Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
