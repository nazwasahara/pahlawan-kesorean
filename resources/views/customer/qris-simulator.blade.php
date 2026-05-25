@extends('layouts.app')

@section('title', 'QRIS Payment Simulator - Pahlawan Kesorean')

@section('content')
<div class="min-h-screen bg-gray-50 flex items-center justify-center p-4 sm:p-6 md:p-8 font-sans">
    <div class="w-full max-w-md bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100 flex flex-col relative transition-all duration-300">
        <!-- Header -->
        <div class="bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 text-white p-6 relative">
            <div class="absolute bottom-0 left-0 right-0 h-1 flex">
                <div class="flex-1 bg-[#005CA9]"></div>
                <div class="flex-1 bg-[#EE2E24]"></div>
            </div>
            <div class="flex items-center gap-2 mb-2">
                <span class="text-2xl font-black italic tracking-tighter">
                    <span class="text-[#005CA9]">QR</span><span class="text-[#EE2E24]">IS</span>
                </span>
                <span class="text-[9px] text-gray-400 font-medium leading-none">
                    Gateway<br>Simulator
                </span>
            </div>
            <h1 class="text-lg font-bold">Simulasi Pembayaran QRIS</h1>
            <p class="text-xs text-gray-400 mt-1">Uji coba alur transaksi non-tunai Pahlawan Kesorean</p>
        </div>

        <!-- Form & Simulator Screen -->
        <div class="p-6 flex-grow flex flex-col gap-6" id="simulatorScreen">
            <!-- Step 1: Input Order -->
            <div class="flex flex-col gap-4">
                <div class="border-b border-gray-100 pb-2">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-wider">Langkah 1: Identifikasi Transaksi</h3>
                </div>

                <!-- Option B: Paste QRIS payload/Order Number -->
                <div class="flex flex-col gap-1.5">
                    <label for="qrisPayloadInput" class="text-xs font-bold text-gray-700">Tempel Teks QRIS / Nomor Order</label>
                    <input type="text" id="qrisPayloadInput" placeholder="Tempel payload QRIS atau CS-xxxxxxxx-xx..." class="w-full bg-gray-50 text-gray-800 text-sm px-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:border-green-600 focus:ring-1 focus:ring-green-600 focus:bg-white transition-all">
                </div>

                <div class="relative flex py-2 items-center">
                    <div class="flex-grow border-t border-gray-200"></div>
                    <span class="flex-shrink mx-4 text-gray-400 text-xs font-bold uppercase">atau</span>
                    <div class="flex-grow border-t border-gray-200"></div>
                </div>

                <!-- Option C: Upload image file -->
                <div class="flex flex-col gap-1.5">
                    <label class="text-xs font-bold text-gray-700">Unggah Gambar QR Code</label>
                    <div class="relative border-2 border-dashed border-gray-200 rounded-xl p-4 flex flex-col items-center justify-center bg-gray-50 hover:bg-gray-100/50 cursor-pointer transition-colors" id="dropzone">
                        <input type="file" id="qrImageInput" accept="image/*" class="absolute inset-0 opacity-0 cursor-pointer">
                        <i class="ri-qr-code-line text-3xl text-gray-400 mb-1"></i>
                        <span class="text-xs text-gray-600 font-bold" id="uploadStatus">Pilih atau Seret Foto Struk QR</span>
                        <span class="text-[10px] text-gray-400 mt-0.5">Mendukung format JPG, PNG</span>
                    </div>
                </div>
            </div>

            <!-- Step 2: Payment Details (Hidden until selected/parsed) -->
            <div id="paymentDetailsSection" class="hidden flex flex-col gap-4 bg-slate-50 border border-slate-100 rounded-2xl p-4 animate-fade-in">
                <div class="border-b border-slate-200/60 pb-2">
                    <h3 class="text-xs font-black text-slate-400 uppercase tracking-wider">Langkah 2: Rincian Pembayaran</h3>
                </div>

                <div class="flex flex-col gap-3">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500 font-semibold">Toko Merchant:</span>
                        <span class="font-extrabold text-slate-800">PAHLAWAN KESOREAN</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500 font-semibold">Nomor Pesanan:</span>
                        <span class="font-extrabold text-slate-800" id="detailOrderNumber">CS-xxxx</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500 font-semibold">Nama Pelanggan:</span>
                        <span class="font-extrabold text-slate-800" id="detailCustomerName">-</span>
                    </div>
                    <div class="flex justify-between items-center text-sm border-t border-slate-200/60 pt-3">
                        <span class="text-gray-800 font-extrabold">Total Tagihan:</span>
                        <span class="text-lg font-black text-green-700" id="detailAmount">Rp 0</span>
                    </div>
                </div>

                <!-- Select Simulated E-wallet -->
                <div class="flex flex-col gap-2 mt-2">
                    <span class="text-xs font-bold text-gray-700">Pilih E-Wallet Simulasi:</span>
                    <div class="grid grid-cols-3 gap-2">
                        <label class="border border-gray-200 rounded-xl p-2 flex flex-col items-center gap-1 cursor-pointer hover:bg-white hover:border-green-600 transition-all text-center relative" id="gopayLabel">
                            <input type="radio" name="ewallet" value="gopay" checked class="sr-only" onchange="updateWalletSelection('gopay')">
                            <span class="text-[11px] font-black text-[#00AED6]">GoPay</span>
                        </label>
                        <label class="border border-gray-200 rounded-xl p-2 flex flex-col items-center gap-1 cursor-pointer hover:bg-white hover:border-green-600 transition-all text-center relative" id="danaLabel">
                            <input type="radio" name="ewallet" value="dana" class="sr-only" onchange="updateWalletSelection('dana')">
                            <span class="text-[11px] font-black text-[#118EEA]">DANA</span>
                        </label>
                        <label class="border border-gray-200 rounded-xl p-2 flex flex-col items-center gap-1 cursor-pointer hover:bg-white hover:border-green-600 transition-all text-center relative" id="ovoLabel">
                            <input type="radio" name="ewallet" value="ovo" class="sr-only" onchange="updateWalletSelection('ovo')">
                            <span class="text-[11px] font-black text-[#4C2A86]">OVO</span>
                        </label>
                    </div>
                </div>

                <button type="button" id="payButton" onclick="submitSimulationPayment()" class="w-full bg-[#125E34] hover:bg-[#0e4b29] text-white py-3.5 px-4 rounded-xl text-sm font-bold transition-all duration-200 active:scale-98 flex items-center justify-center gap-2 cursor-pointer shadow-md mt-2">
                    <i class="ri-shield-check-line text-lg"></i> BAYAR SEKARANG
                </button>
            </div>
        </div>

        <!-- Success Receipt Screen (Hidden by default) -->
        <div id="receiptScreen" class="hidden p-6 flex-col items-center text-center gap-6 animate-scale-up">
            <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-600">
                <i class="ri-checkbox-circle-fill text-6xl"></i>
            </div>

            <div class="flex flex-col gap-1">
                <h2 class="text-xl font-black text-slate-800">Pembayaran Berhasil!</h2>
                <p class="text-xs text-gray-500">Bukti pembayaran digital simulator</p>
            </div>

            <div class="w-full bg-slate-50 border border-slate-100 rounded-2xl p-4 flex flex-col gap-3 text-left">
                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-500 font-semibold">Merchant:</span>
                    <span class="font-bold text-slate-800">PAHLAWAN KESOREAN</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-500 font-semibold">Nomor Order:</span>
                    <span class="font-bold text-slate-800" id="receiptOrderNumber">CS-xxxx</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-500 font-semibold">Metode:</span>
                    <span class="font-bold text-slate-800 uppercase" id="receiptMethod">QRIS (GoPay)</span>
                </div>
                <div class="flex justify-between items-center text-xs">
                    <span class="text-gray-500 font-semibold">Tanggal:</span>
                    <span class="font-bold text-slate-800" id="receiptDate">24 May 2026, 19:50</span>
                </div>
                <div class="flex justify-between items-center text-xs border-t border-slate-200 pt-2.5 mt-1">
                    <span class="text-slate-800 font-bold">Total Pembayaran:</span>
                    <span class="text-sm font-black text-green-700" id="receiptAmount">Rp 0</span>
                </div>
            </div>

            <button type="button" onclick="resetSimulator()" class="w-full bg-slate-900 hover:bg-slate-800 text-white py-3.5 px-4 rounded-xl text-sm font-bold transition-all cursor-pointer">
                Simulasikan Transaksi Lain
            </button>
        </div>
    </div>
</div>

@push('scripts')
<!-- QR Code Scanner/Decoder Library (Local with CDN fallback) -->
<script src="{{ asset('js/jsQR.js') }}"></script>
<script>
    if (typeof jsQR === 'undefined') {
        console.warn("Local jsQR.js not found, falling back to CDN synchronously...");
        document.write('<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"><\/script>');
    }
</script>
<script>
    let selectedOrderNumber = null;
    let selectedOrderTotal = 0;
    let selectedCustomerName = '';

    // Handle Dropdown Selection
    const selectEl = document.getElementById('orderSelect');
    if (selectEl) {
        selectEl.addEventListener('change', function(e) {
            const option = e.target.options[e.target.selectedIndex];
            if (option && option.value) {
                selectedOrderNumber = option.value;
                selectedOrderTotal = parseFloat(option.getAttribute('data-total'));
                selectedCustomerName = option.getAttribute('data-customer');
                showPaymentDetails();
            } else {
                hidePaymentDetails();
            }
        });
    }

    // Handle QRIS text or order ID paste
    document.getElementById('qrisPayloadInput').addEventListener('input', function(e) {
        const val = e.target.value.trim();
        if (val) {
            parseTextPayload(val);
        } else {
            const selectEl = document.getElementById('orderSelect');
            if (!selectEl || !selectEl.value) {
                hidePaymentDetails();
            }
        }
    });

    // Handle Image Upload
    document.getElementById('qrImageInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            document.getElementById('uploadStatus').textContent = "Membaca QR...";
            const reader = new FileReader();
            reader.onload = function(event) {
                const img = new Image();
                img.onload = function() {
                    try {
                        if (typeof jsQR === 'undefined') {
                            throw new Error("Pustaka jsQR tidak terdeteksi. Periksa koneksi internet Anda atau muat ulang halaman.");
                        }
                        const canvas = document.createElement('canvas');
                        const ctx = canvas.getContext('2d');
                        canvas.width = img.width;
                        canvas.height = img.height;
                        ctx.drawImage(img, 0, 0);
                        const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                        const code = jsQR(imageData.data, imageData.width, imageData.height);
                        
                        if (code) {
                            document.getElementById('uploadStatus').textContent = "QR Terdeteksi!";
                            parseTextPayload(code.data);
                        } else {
                            document.getElementById('uploadStatus').textContent = "Gagal membaca QR code";
                            alert('Gambar QR tidak terdeteksi. Pastikan gambar jelas, tidak terpotong, dan berfokus pada sandi QR.');
                        }
                    } catch (err) {
                        console.error("QR Image Reading Error:", err);
                        document.getElementById('uploadStatus').textContent = "Error: " + err.message;
                        alert("Gagal memproses gambar: " + err.message);
                    }
                };
                img.src = event.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    // QRIS TLV Parser helper
    function parseQRIS(payload) {
        let index = 0;
        const tags = {};
        while (index < payload.length) {
            if (index + 4 > payload.length) break;
            const tag = payload.substring(index, index + 2);
            const length = parseInt(payload.substring(index + 2, index + 4), 10);
            if (isNaN(length)) break;
            const value = payload.substring(index + 4, index + 4 + length);
            tags[tag] = value;
            index += 4 + length;
        }
        return tags;
    }

    // Parser Helper
    function parseTextPayload(text) {
        let orderNum = null;
        let amount = 0;
        let merchantName = "PAHLAWAN KESOREAN";
        
        // 1. Try to parse as TLV (QRIS format)
        try {
            const tags = parseQRIS(text);
            if (tags['54']) {
                amount = parseFloat(tags['54']);
            }
            if (tags['59']) {
                merchantName = tags['59'];
            }
            if (tags['62']) {
                const subTags = parseQRIS(tags['62']);
                if (subTags['01']) {
                    orderNum = subTags['01'];
                }
            }
        } catch (e) {
            console.warn("Failed to parse as TLV QRIS, falling back to regex...");
        }

        // 2. Fallback to regex if not found via TLV or is direct order number
        if (!orderNum) {
            const orderMatch = text.match(/(CS|POS)-\d+-\d+|POS\d+/i);
            if (orderMatch) {
                orderNum = orderMatch[0];
            }
        }

        if (orderNum) {
            orderNum = orderNum.toUpperCase();
            
            // Check if this order exists in our dropdown
            const selectEl = document.getElementById('orderSelect');
            let foundInDropdown = false;
            if (selectEl) {
                for (let i = 0; i < selectEl.options.length; i++) {
                    if (selectEl.options[i].value === orderNum) {
                        selectEl.selectedIndex = i;
                        selectedOrderNumber = orderNum;
                        selectedOrderTotal = parseFloat(selectEl.options[i].getAttribute('data-total'));
                        selectedCustomerName = selectEl.options[i].getAttribute('data-customer');
                        showPaymentDetails();
                        foundInDropdown = true;
                        break;
                    }
                }
            }

            if (!foundInDropdown) {
                // Fetch dynamically from server
                document.getElementById('uploadStatus').textContent = "Mengambil rincian...";
                fetch(`/checkout/details/${orderNum}`)
                    .then(res => {
                        if (!res.ok) {
                            throw new Error('Order not found on server');
                        }
                        return res.json();
                    })
                    .then(resData => {
                        if (resData.success) {
                            selectedOrderNumber = resData.order_number;
                            selectedOrderTotal = resData.total;
                            selectedCustomerName = resData.customer_name;
                            showPaymentDetails();
                            document.getElementById('uploadStatus').textContent = "Rincian dimuat!";
                        } else {
                            throw new Error(resData.message || 'Pesanan tidak ditemukan.');
                        }
                    })
                    .catch(err => {
                        console.warn('Order details not found on server, fallback to decoded QRIS data:', err);
                        // Fallback to data parsed from QRIS code directly
                        selectedOrderNumber = orderNum;
                        selectedOrderTotal = amount > 0 ? amount : 0;
                        selectedCustomerName = "Simulasi QRIS Offline";
                        showPaymentDetails();
                        document.getElementById('uploadStatus').textContent = "Dimuat dari QRIS (Offline)";
                    });
            }
        } else {
            alert('Teks atau format QRIS/Nomor Order tidak dikenali.');
            hidePaymentDetails();
        }
    }

    function showPaymentDetails() {
        document.getElementById('detailOrderNumber').textContent = '#' + selectedOrderNumber;
        document.getElementById('detailCustomerName').textContent = selectedCustomerName;
        document.getElementById('detailAmount').textContent = 'Rp ' + selectedOrderTotal.toLocaleString('id-ID');
        document.getElementById('paymentDetailsSection').classList.remove('hidden');
    }

    function hidePaymentDetails() {
        document.getElementById('paymentDetailsSection').classList.add('hidden');
    }

    // Toggle border styling for radio buttons
    function updateWalletSelection(wallet) {
        ['gopay', 'dana', 'ovo'].forEach(w => {
            const el = document.getElementById(w + 'Label');
            if (el) {
                if (w === wallet) {
                    el.classList.add('border-green-600', 'bg-green-50/50');
                    el.classList.remove('border-gray-200');
                } else {
                    el.classList.remove('border-green-600', 'bg-green-50/50');
                    el.classList.add('border-gray-200');
                }
            }
        });
    }

    // Initialize styling
    updateWalletSelection('gopay');

    // Submit Simulation
    function submitSimulationPayment() {
        if (!selectedOrderNumber) return;

        const payButton = document.getElementById('payButton');
        payButton.disabled = true;
        payButton.innerHTML = `<i class="ri-loader-4-line animate-spin text-lg"></i> Memproses Pembayaran...`;

        const wallet = document.querySelector('input[name="ewallet"]:checked').value;

        fetch('{{ route("customer.qris-simulator.pay") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                order_number: selectedOrderNumber
            })
        })
        .then(res => {
            if (!res.ok && selectedCustomerName === "Simulasi QRIS Offline") {
                // Return a mock success response so we can complete simulation
                return { success: true, is_mock: true };
            }
            return res.json();
        })
        .then(data => {
            if (data.success) {
                // Transition to receipt screen
                document.getElementById('simulatorScreen').classList.add('hidden');
                document.getElementById('receiptScreen').classList.remove('hidden');
                document.getElementById('receiptScreen').classList.add('flex');

                document.getElementById('receiptOrderNumber').textContent = '#' + selectedOrderNumber;
                document.getElementById('receiptAmount').textContent = 'Rp ' + selectedOrderTotal.toLocaleString('id-ID');
                document.getElementById('receiptMethod').textContent = 'QRIS (' + wallet.toUpperCase() + ')';
                
                // Show info badge if it was parsed offline
                if (data.is_mock) {
                    const warning = document.createElement('div');
                    warning.className = "mt-4 p-3 bg-amber-50 border border-amber-200 text-amber-800 text-[11px] font-bold rounded-xl text-center leading-relaxed";
                    warning.innerHTML = `<i class="ri-information-line text-sm"></i> Pembayaran disimulasikan offline karena pesanan tidak terdaftar di database.`;
                    document.getElementById('receiptScreen').insertBefore(warning, document.getElementById('receiptScreen').lastElementChild);
                }

                const now = new Date();
                const dateStr = now.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) + ', ' + 
                                now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
                document.getElementById('receiptDate').textContent = dateStr;
            } else {
                alert('Gagal: ' + data.message);
                payButton.disabled = false;
                payButton.innerHTML = `<i class="ri-shield-check-line text-lg"></i> BAYAR SEKARANG`;
            }
        })
        .catch(err => {
            console.error('Error:', err);
            alert('Terjadi kesalahan jaringan.');
            payButton.disabled = false;
            payButton.innerHTML = `<i class="ri-shield-check-line text-lg"></i> BAYAR SEKARANG`;
        });
    }

    function resetSimulator() {
        // Reload pending options or reload page
        window.location.reload();
    }

    // Read order_number from query parameter on page load
    window.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const orderNum = urlParams.get('order_number');
        if (orderNum) {
            const selectEl = document.getElementById('orderSelect');
            let foundInDropdown = false;
            if (selectEl) {
                for (let i = 0; i < selectEl.options.length; i++) {
                    if (selectEl.options[i].value === orderNum) {
                        selectEl.selectedIndex = i;
                        selectedOrderNumber = orderNum;
                        selectedOrderTotal = parseFloat(selectEl.options[i].getAttribute('data-total'));
                        selectedCustomerName = selectEl.options[i].getAttribute('data-customer');
                        showPaymentDetails();
                        foundInDropdown = true;
                        break;
                    }
                }
            }

            if (!foundInDropdown) {
                document.getElementById('qrisPayloadInput').value = orderNum;
                parseTextPayload(orderNum);
            }
        }
    });
</script>

<style>
    .animate-fade-in {
        animation: fadeIn 0.3s ease-out forwards;
    }
    .animate-scale-up {
        animation: scaleUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes scaleUp {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
    }
</style>
@endpush
