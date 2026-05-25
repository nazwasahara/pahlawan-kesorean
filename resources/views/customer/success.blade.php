@extends('customer.layouts.app')

@section('title', 'Pesanan Sukses - Pahlawan Kesorean')

@section('customer-content')
<!-- Full-screen absolute overlay inside the phone container -->
<div class="absolute inset-0 z-[60] bg-white p-6 overflow-y-auto custom-scroll flex flex-col justify-between">
    <!-- Close Button (Top Right) -->
    <a href="{{ route('customer.history') }}" class="absolute top-4 right-4 text-neutral-400 hover:text-neutral-600 transition">
        <i class="ri-close-line text-2xl"></i>
    </a>

    <!-- Top Section / Details -->
    <div class="flex flex-col items-center pt-8 pb-4">
        <!-- Smiling face SVG or Checkmark -->
        <div class="mb-4">
            @if($order->status === 'paid' || $order->status === 'processing' || $order->status === 'completed')
                <div class="w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center text-emerald-600">
                    <i class="ri-checkbox-circle-fill text-6xl"></i>
                </div>
            @else
                <svg width="80" height="80" viewBox="0 0 80 80" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <!-- Outer pale green circle -->
                    <circle cx="40" cy="40" r="40" fill="#E2EFE4"/>
                    <!-- Inner green face circle -->
                    <circle cx="40" cy="40" r="28" fill="#125E34"/>
                    <!-- Eyes (arc) -->
                    <path d="M29 36C30.5 33.5 33.5 33.5 35 36" stroke="white" stroke-width="3" stroke-linecap="round"/>
                    <path d="M45 36C46.5 33.5 49.5 33.5 51 36" stroke="white" stroke-width="3" stroke-linecap="round"/>
                    <!-- Smile (arc) -->
                    <path d="M30 46C33 50.5 47 50.5 50 46" stroke="white" stroke-width="3" stroke-linecap="round"/>
                    <!-- Checkmark badge (bottom right) -->
                    <circle cx="54" cy="54" r="10" fill="white"/>
                    <circle cx="54" cy="54" r="8" fill="#125E34"/>
                    <path d="M50 54L53 57L58 51" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            @endif
        </div>

        @if($order->status === 'paid' || $order->status === 'processing' || $order->status === 'completed')
            <h2 class="text-xl font-extrabold text-emerald-800 text-center tracking-tight">Pembayaran Berhasil !!</h2>
            <h1 class="text-2xl font-black text-gray-950 text-center mt-1 leading-tight tracking-tight">
                Pesanan Sedang<br>Diproses
            </h1>

            <p class="text-[12px] font-semibold text-gray-600 text-center mt-4 px-3 leading-relaxed">
                Terima kasih, pembayaran QRIS Anda telah kami terima dan pesanan Anda sedang disiapkan di dapur.
            </p>
        @else
            <h2 class="text-xl font-extrabold text-gray-950 text-center tracking-tight">Terima kasih !!</h2>
            <h1 class="text-2xl font-black text-gray-950 text-center mt-1 leading-tight tracking-tight">
                Pesanan Anda Sudah<br>Kami Terima
            </h1>

            <p class="text-[12px] font-semibold text-gray-600 text-center mt-4 px-3 leading-relaxed">
                @if($order->payment_method == 'qris')
                    Silakan scan atau unduh kode QRIS di bawah untuk menyelesaikan pembayaran secara instan.
                @else
                    Simpan nomor pesanan Anda dan tunjukan ke kasir saat pembayaran dengan metode Cash dan Debit
                @endif
            </p>
        @endif

        <!-- Order Number Box -->
        <div class="border border-[#125E34] rounded-3xl p-4 bg-white w-full max-w-sm mt-5 flex flex-col items-center justify-center relative">
            <span class="text-[11px] font-bold text-[#125E34] tracking-wide mb-1">Nomor pesanan Anda</span>
            <div class="flex items-center gap-2">
                <span class="text-2xl font-black text-[#125E34] tracking-wide" id="successOrderNumber">#{{ $order->order_number }}</span>
                <button onclick="copyOrderNumber()" class="text-gray-800 hover:text-gray-600 transition flex items-center" title="Salin Nomor Pesanan">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                </button>
            </div>
        </div>

        <!-- QRIS Section if QRIS selected and not paid yet -->
        @if($order->payment_method == 'qris' && in_array($order->status, ['pending', 'cancelled']))
            <div class="w-full max-w-sm bg-white rounded-3xl border border-gray-200 shadow-sm flex flex-col overflow-hidden mt-5">
                <div class="bg-slate-900 text-white py-3 px-5 text-center relative">
                    <div class="absolute bottom-0 left-0 right-0 h-1 flex">
                        <div class="flex-1 bg-[#005CA9]"></div>
                        <div class="flex-1 bg-[#EE2E24]"></div>
                    </div>
                    <div class="flex items-center justify-center gap-1.5 mb-0.5">
                        <span class="text-xl font-black italic tracking-tighter">
                            <span class="text-[#005CA9]">QR</span><span class="text-[#EE2E24]">IS</span>
                        </span>
                        <span class="text-[8px] text-gray-400 font-medium leading-none text-left">
                            Quick Response Code<br>Indonesian Standard
                        </span>
                    </div>
                    <h3 class="text-xs font-bold tracking-wide uppercase text-gray-200">PAHLAWAN KESOREAN</h3>
                    <p class="text-[8px] text-gray-400">NMID: ID1020021570535</p>
                </div>

                <div class="p-5 flex flex-col items-center bg-white text-center">
                    <div class="relative bg-white rounded-xl p-2.5 border border-gray-100 shadow-inner">
                        <div class="w-[180px] h-[180px] relative overflow-hidden flex items-center justify-center bg-white">
                            <canvas id="successQrisCanvas" class="hidden" width="180" height="180"></canvas>
                            <img id="successQrisImage" class="w-[180px] h-[180px] object-contain cursor-pointer" alt="QRIS Code" title="Sentuh lama atau klik kanan untuk menyalin/menyimpan">
                            <!-- Expired Overlay -->
                            <div id="customerQrisExpiredOverlay" class="absolute inset-0 bg-white/95 flex flex-col items-center justify-center p-3 {{ $order->status === 'cancelled' ? '' : 'hidden' }}">
                                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-600 mb-2">
                                    <i class="ri-error-warning-line text-xl"></i>
                                </div>
                                <p class="text-xs font-bold text-gray-900">Pembayaran Kedaluwarsa</p>
                                <p class="text-[10px] text-gray-550 text-center mt-1">Sandi QR tidak lagi berlaku dan pesanan telah dibatalkan.</p>
                            </div>
                        </div>
                    </div>

                    @if($order->status !== 'cancelled')
                        <div class="mt-3.5 w-full flex flex-col gap-2">
                            <button 
                                type="button" 
                                onclick="downloadQrisImage()" 
                                class="w-full bg-[#125E34] hover:bg-[#0e4b29] text-[#F4F3EB] font-bold text-xs py-2 px-4 rounded-xl shadow-sm flex items-center justify-center gap-1.5 transition-all duration-200 active:scale-98 cursor-pointer"
                            >
                                <i class="ri-download-2-line text-sm"></i> Unduh QR Code
                            </button>
                            <button 
                                type="button" 
                                onclick="copyQrisText()" 
                                class="w-full bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs py-2 px-4 rounded-xl flex items-center justify-center gap-1.5 transition-all duration-200 active:scale-98 cursor-pointer"
                            >
                                <i class="ri-file-copy-2-line text-sm"></i> Salin Teks QRIS
                            </button>
                        </div>
                    @endif

                    <div class="mt-4 w-full">
                        <div class="flex items-center justify-between text-[10px] text-gray-500 mb-1 px-1">
                            <span class="flex items-center gap-1"><i class="ri-time-line text-gray-400"></i> Masa Berlaku QRIS</span>
                            <span id="successQrisTimer" class="font-bold text-green-750">05:00</span>
                        </div>
                        <div class="w-full h-1 bg-gray-100 rounded-full overflow-hidden">
                            <div id="successQrisProgress" class="h-full bg-green-700 transition-all duration-1000" style="width: 100%;"></div>
                        </div>
                        <p class="text-[10px] text-neutral-500 mt-3 font-semibold leading-relaxed">
                            Silakan scan atau unduh kode QR di atas untuk menyelesaikan pembayaran.
                        </p>

                        <!-- Simulated Trial Link -->
                        <div class="mt-4 pt-3.5 border-t border-gray-100 flex flex-col items-center">
                            <a href="{{ route('customer.qris-simulator') }}?order_number={{ $order->order_number }}" target="_blank" class="w-full inline-flex items-center justify-center gap-1.5 text-[10px] font-extrabold text-amber-800 hover:text-amber-900 bg-amber-50 hover:bg-amber-100/70 border border-amber-255 px-3 py-2 rounded-xl transition-all cursor-pointer">
                                <i class="ri-external-link-line text-xs"></i> Simulasi Bayar QRIS (Uji Coba)
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Total Pembayaran Block -->
                <div class="bg-emerald-50/50 border-t border-gray-100 py-3 px-5 flex items-center justify-between text-left">
                    <div>
                        <span class="text-[9px] text-emerald-800 font-bold uppercase tracking-wider block">Total Pembayaran</span>
                        <span class="text-[10px] text-neutral-500 font-semibold block mt-0.5">Harap bayar sesuai nominal</span>
                    </div>
                    <span class="text-base font-black text-emerald-950">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                </div>

                <div class="bg-gray-50 border-t border-gray-100 py-2.5 px-4 flex items-center justify-between">
                    <span class="text-[9px] text-gray-550 font-semibold">Mendukung semua E-Wallet & Mobile Banking</span>
                    <div class="flex gap-2 text-gray-400 text-xs">
                        <i class="ri-wallet-3-line"></i>
                        <i class="ri-bank-card-line"></i>
                    </div>
                </div>
            </div>
        @endif

        <!-- Penting pill badge -->
        <div class="flex justify-center mt-6">
            <span class="bg-[#FFEBEB] text-[#FF4D4D] border border-[#FFA3A3] text-[10px] font-extrabold px-5 py-1 rounded-full uppercase tracking-wider">
                Penting
            </span>
        </div>

        <!-- Instructions Container -->
        <div class="w-full space-y-3 mt-4">
            <!-- Instruction 1 -->
            <div class="flex items-center gap-3 bg-[#FEF6E5] border border-[#F3D5A2] rounded-2xl p-4 w-full text-left">
                <div class="w-10 h-10 rounded-full bg-[#E5EFE4] flex items-center justify-center flex-shrink-0">
                    <i class="ri-restaurant-line text-lg text-[#125E34]"></i>
                </div>
                <p class="text-[11px] font-bold text-amber-950 leading-relaxed">
                    Jangan lupa ambil nomor meja di kasir untuk pengantaran pesanan.
                </p>
            </div>

            <!-- Instruction 2 -->
            <div class="flex items-center gap-3 bg-[#E6EFE4] border border-[#BCD4BF] rounded-2xl p-4 w-full text-left">
                <div class="w-10 h-10 rounded-full bg-[#E5EFE4] flex items-center justify-center flex-shrink-0">
                    <i class="ri-user-received-2-line text-lg text-[#125E34]"></i>
                </div>
                <p class="text-[11px] font-bold text-[#1b4325] leading-relaxed">
                    Lakukan Pembayaran di kasir sebelum atau sesudah makan.
                </p>
            </div>
        </div>
    </div>

    <!-- Bottom Button Section -->
    <div class="w-full pb-4 mt-6">
        <a href="{{ route('customer.history') }}" class="w-full bg-[#125E34] hover:bg-[#0e4b29] text-white rounded-2xl py-3.5 px-5 flex items-center justify-center font-extrabold text-sm tracking-wide transition-all duration-200">
            Mengerti
        </a>
    </div>
</div>

<script>
    function copyOrderNumber() {
        const orderNum = "{{ $order->order_number }}";
        navigator.clipboard.writeText(orderNum).then(() => {
            alert("Nomor pesanan berhasil disalin: " + orderNum);
        }).catch(err => {
            console.error("Gagal menyalin nomor pesanan: ", err);
        });
    }

    // Save to LocalStorage history
    try {
        const orderNum = "{{ $order->order_number }}";
        let history = JSON.parse(localStorage.getItem('customer_order_history') || '[]');
        if (!history.includes(orderNum)) {
            history.push(orderNum);
            localStorage.setItem('customer_order_history', JSON.stringify(history));
        }
    } catch (e) {
        console.error('Failed to save order to history:', e);
    }
</script>

@if($order->payment_method == 'qris')
<!-- QR Code Generator Library -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrious/4.0.2/qrious.min.js"></script>
<script>
    // QRIS CRC16 calculation helper
    function getCRC16(str) {
        let crc = 0xFFFF;
        for (let c = 0; c < str.length; c++) {
            crc ^= (str.charCodeAt(c) << 8);
            for (let i = 0; i < 8; i++) {
                if (crc & 0x8000) {
                    crc = (crc << 1) ^ 0x1021;
                } else {
                    crc = crc << 1;
                }
            }
        }
        return (crc & 0xFFFF).toString(16).toUpperCase().padStart(4, '0');
    }

    // QRIS Payload Generator
    function generateQRISPayload(amount, orderId) {
        let qris = "000201";
        qris += "010212"; // Dynamic QRIS
        
        const merchantInfo = "0014ID.CO.QRIS.WWW01189360091234567890120215ID10200215705350303U00";
        qris += "26" + merchantInfo.length.toString().padStart(2, '0') + merchantInfo;
        
        qris += "52045812"; // Restaurant category
        qris += "5303360";  // IDR Currency
        
        if (amount > 0) {
            const amtStr = Math.round(amount).toString();
            qris += "54" + amtStr.length.toString().padStart(2, '0') + amtStr;
        }
        
        qris += "5802ID";
        
        const name = "PAHLAWAN KESOREAN";
        qris += "59" + name.length.toString().padStart(2, '0') + name;
        
        const city = "YOGYAKARTA";
        qris += "60" + city.length.toString().padStart(2, '0') + city;
        
        const postal = "55182";
        qris += "61" + postal.length.toString().padStart(2, '0') + postal;
        
        const billNum = orderId || "POS000000";
        const addData = "01" + billNum.length.toString().padStart(2, '0') + billNum;
        qris += "62" + addData.length.toString().padStart(2, '0') + addData;
        
        qris += "6304";
        const crc = getCRC16(qris);
        qris += crc;
        
        return qris;
    }

    // Render Canvas
    window.addEventListener('DOMContentLoaded', (event) => {
        const amount = {{ $order->total }};
        const orderId = "{{ $order->order_number }}";
        const payload = generateQRISPayload(amount, orderId);
        
        const canvas = document.getElementById('successQrisCanvas');
        if (canvas) {
            new QRious({
                element: canvas,
                value: payload,
                size: 180,
                level: 'M'
            });
            
            // Set image tag source so users can natively copy/save it
            const imgEl = document.getElementById('successQrisImage');
            if (imgEl) {
                imgEl.src = canvas.toDataURL('image/png');
            }
        }

        // Start Countdown Timer
        const isCancelled = @json($order->status === 'cancelled');
        const timerEl = document.getElementById('successQrisTimer');
        const progressEl = document.getElementById('successQrisProgress');
        const expiredOverlay = document.getElementById('customerQrisExpiredOverlay');

        if (isCancelled) {
            if (timerEl) timerEl.textContent = 'Expired';
            if (progressEl) {
                progressEl.style.width = '0%';
                progressEl.style.backgroundColor = '#DC2626';
            }
            if (expiredOverlay) {
                expiredOverlay.classList.remove('hidden');
            }
            return;
        }

        const totalDuration = 300; // 5 minutes
        const timeRemaining = @json(max(0, 300 - (time() - ($order->created_at ? $order->created_at->timestamp : time()))));
        const expiryTime = Date.now() + (timeRemaining * 1000);

        const triggerCancellation = () => {
            fetch('{{ route("customer.checkout.cancel", $order->order_number) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                console.log('Cancellation result:', data);
                if (expiredOverlay) {
                    expiredOverlay.classList.remove('hidden');
                }
            })
            .catch(err => console.error('Error cancelling order:', err));
        };

        const updateTimer = () => {
            const now = Date.now();
            let timeRemaining = Math.max(0, Math.floor((expiryTime - now) / 1000));
            
            if (timeRemaining <= 0) {
                clearInterval(interval);
                if (timerEl) timerEl.textContent = 'Expired';
                if (progressEl) {
                    progressEl.style.width = '0%';
                    progressEl.style.backgroundColor = '#DC2626';
                }
                triggerCancellation();
                return;
            }

            // Format mm:ss
            const m = Math.floor(timeRemaining / 60).toString().padStart(2, '0');
            const s = Math.floor(timeRemaining % 60).toString().padStart(2, '0');
            if (timerEl) timerEl.textContent = `${m}:${s}`;

            if (progressEl) {
                const pct = (timeRemaining / totalDuration) * 100;
                progressEl.style.width = `${pct}%`;
                if (pct < 20) {
                    progressEl.style.backgroundColor = '#DC2626';
                } else if (pct < 50) {
                    progressEl.style.backgroundColor = '#EAB308';
                } else {
                    progressEl.style.backgroundColor = '#15803D';
                }
            }
        };

        updateTimer();
        const interval = setInterval(updateTimer, 1000);

        // Poll order status
        const pollStatus = () => {
            fetch("{{ route('customer.checkout.status', ['order_number' => $order->order_number]) }}")
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'paid' || data.status === 'processing' || data.status === 'completed') {
                        clearInterval(interval);
                        clearInterval(pollInterval);
                        window.location.reload();
                    } else if (data.status === 'cancelled') {
                        clearInterval(interval);
                        clearInterval(pollInterval);
                        window.location.reload();
                    }
                })
                .catch(err => console.error("Error polling status:", err));
        };
        const pollInterval = setInterval(pollStatus, 3000);
    });

    function downloadQrisImage() {
        const canvas = document.getElementById('successQrisCanvas');
        if (!canvas) return;
        
        const dlCanvas = document.createElement('canvas');
        dlCanvas.width = 400;
        dlCanvas.height = 550;
        const ctx = dlCanvas.getContext('2d');
        
        ctx.fillStyle = '#FFFFFF';
        ctx.fillRect(0, 0, dlCanvas.width, dlCanvas.height);
        
        ctx.fillStyle = '#0F172A';
        ctx.fillRect(0, 0, dlCanvas.width, 80);
        
        ctx.fillStyle = '#FFFFFF';
        ctx.font = 'bold 20px sans-serif';
        ctx.textAlign = 'center';
        ctx.fillText('PAHLAWAN KESOREAN', dlCanvas.width / 2, 35);
        
        ctx.fillStyle = '#94A3B8';
        ctx.font = '12px sans-serif';
        ctx.fillText('NMID: ID1020021570535', dlCanvas.width / 2, 55);
        
        ctx.fillStyle = '#005CA9';
        ctx.fillRect(0, 75, dlCanvas.width / 2, 5);
        ctx.fillStyle = '#EE2E24';
        ctx.fillRect(dlCanvas.width / 2, 75, dlCanvas.width / 2, 5);
        
        ctx.drawImage(canvas, (dlCanvas.width - 280) / 2, 110, 280, 280);
        
        ctx.fillStyle = '#1E293B';
        ctx.font = 'bold 16px sans-serif';
        ctx.fillText("{{ $order->order_number }}", dlCanvas.width / 2, 420);
        
        ctx.fillStyle = '#15803D';
        ctx.font = 'bold 24px sans-serif';
        ctx.fillText('Rp ' + ({{ $order->total }}).toLocaleString('id-ID'), dlCanvas.width / 2, 460);
        
        ctx.fillStyle = '#64748B';
        ctx.font = 'italic 11px sans-serif';
        ctx.fillText('Unduh via Pahlawan Kesorean', dlCanvas.width / 2, 525);
        
        const link = document.createElement('a');
        link.download = `QRIS-{{ $order->order_number }}.png`;
        link.href = dlCanvas.toDataURL('image/png');
        link.click();
    }

    function copyQrisText() {
        const amount = {{ $order->total }};
        const orderId = "{{ $order->order_number }}";
        const payload = generateQRISPayload(amount, orderId);
        
        navigator.clipboard.writeText(payload).then(() => {
            alert("Teks payload QRIS berhasil disalin ke clipboard!");
        }).catch(err => {
            console.error("Gagal menyalin teks QRIS:", err);
            // Fallback: prompt to copy
            window.prompt("Salin teks payload QRIS di bawah ini:", payload);
        });
    }
</script>
@endif
@endsection
