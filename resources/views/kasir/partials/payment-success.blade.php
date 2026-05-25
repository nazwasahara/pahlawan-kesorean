<!-- Payment Success View -->
<div id="paymentSuccessView" style="display:none;" class="flex flex-col h-full w-full items-center justify-center p-6">

    <style>
        @media print {
            /* Set page size and margins for thermal receipt printing */
            @page {
                size: 80mm auto;
                margin: 0;
            }

            /* Hide all document elements for print */
            body * {
                visibility: hidden;
            }

            /* Make only the printReceiptArea visible */
            #printReceiptArea, 
            #printReceiptArea * {
                visibility: visible;
            }

            /* Reset layouts & containers for thermal print */
            body, 
            html {
                background: white !important;
                color: #000 !important;
                width: 80mm !important;
                margin: 0 auto !important;
                padding: 0 !important;
            }

            #printReceiptArea {
                position: absolute;
                left: 0;
                top: 0;
                width: 80mm !important;
                max-width: 80mm !important;
                background: white !important;
                padding: 4mm !important;
                margin: 0 !important;
                border: none !important;
                box-shadow: none !important;
                font-size: 11px !important;
                font-family: 'Courier New', Courier, monospace !important;
                display: block !important;
            }

            /* Force table columns layout for narrow print */
            table {
                width: 100% !important;
                border-collapse: collapse !important;
                font-size: 10px !important;
            }

            table thead tr {
                border-top: 1px solid #000 !important;
                border-bottom: 1px solid #000 !important;
            }

            th {
                padding: 8px 0 !important;
                border: none !important;
            }

            td {
                padding: 8px 0 !important;
                border-bottom: 1px dashed #ccc !important;
            }

            /* Summary borders for print */
            .border-t.border-solid.border-gray-300.pt-3 {
                border-top: 1px solid #000 !important;
                padding-top: 12px !important;
                margin-top: 12px !important;
            }

            .border-t.border-solid.border-gray-300.pt-2\.5 {
                border-top: 1px solid #000 !important;
                padding-top: 8px !important;
            }
        }
    </style>

    <div class="w-full max-w-xl print:hidden">
        <div class="bg-white rounded-2xl border border-gray-100 p-6 md:p-10 text-center">
            <div class="mb-6">
                <div class="w-16 h-16 rounded-full bg-green-50 mx-auto flex items-center justify-center">
                    <i class="ri-check-line text-3xl text-green-700"></i>
                </div>
            </div>

            <h2 class="text-xl font-bold text-green-700 mb-3">Pembayaran Berhasil</h2>

            <div class="text-left my-6">
                <div class="flex justify-between py-2 border-b border-gray-100">
                    <div class="text-xs text-gray-600">ID Order</div>
                    <div id="successOrderId" class="font-medium text-sm text-gray-800">-</div>
                </div>

                <div class="flex justify-between py-2 border-b border-gray-100">
                    <div class="text-xs text-gray-600">Tanggal</div>
                    <div id="successDate" class="font-medium text-sm text-gray-800">-</div>
                </div>

                <div class="flex justify-between py-2 border-b border-gray-100">
                    <div class="text-xs text-gray-600">Metode Pembayaran</div>
                    <div id="successMethod" class="font-medium text-sm text-gray-800">-</div>
                </div>

                <div class="flex justify-between py-2 border-b border-gray-100">
                    <div class="text-xs text-gray-600">Total Pembayaran</div>
                    <div id="successTotal" class="font-bold text-sm text-green-700">Rp 0</div>
                </div>

                <div class="flex justify-between py-2">
                    <div class="text-xs text-gray-600">Kembalian</div>
                    <div id="successChange" class="font-bold text-sm text-green-700">Rp 0</div>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-2 gap-4">
                <button onclick="printReceipt()" class="px-4 py-3 bg-green-800 text-white rounded-lg">Cetak Struk</button>
                <button onclick="finishAndReset()" class="px-4 py-3 border rounded-lg">Selesai</button>
            </div>
        </div>
    </div>

    <!-- Print-only receipt layout (hidden on screen, shows on print) -->
    <div id="printReceiptArea" class="hidden print:block bg-white text-black p-0 w-full text-sm">
        
        <!-- Info Block -->
        <div class="space-y-0.5 text-xs">
            <div class="flex justify-between py-1.5 border-b border-dashed border-gray-300">
                <span class="font-bold uppercase">NO. ORDER</span>
                <span class="font-bold" id="printOrderId">#</span>
            </div>
            <div class="flex justify-between py-1.5 border-b border-dashed border-gray-300">
                <span class="font-bold uppercase">PELANGGAN</span>
                <span class="font-bold" id="printCustomerName">-</span>
            </div>
            <div class="flex justify-between py-1.5 border-b border-dashed border-gray-300">
                <span class="font-bold uppercase">TIPE</span>
                <span class="font-bold" id="printOrderType">-</span>
            </div>
            <div class="flex justify-between py-1.5 border-b border-dashed border-gray-300">
                <span class="font-bold uppercase">MEJA</span>
                <span class="font-bold" id="printTableNumber">-</span>
            </div>
            <div class="flex justify-between py-1.5 border-b border-dashed border-gray-300">
                <span class="font-bold uppercase">WAKTU</span>
                <span class="font-bold" id="printDate">-</span>
            </div>
        </div>

        <!-- Products List Table -->
        <table class="w-full text-left border-collapse mt-6 text-xs">
            <thead>
                <tr class="border-t border-b border-solid border-gray-300">
                    <th class="py-2.5 font-bold text-gray-900">Produk</th>
                    <th class="py-2.5 font-bold text-gray-900 text-center w-12">Qty</th>
                    <th class="py-2.5 font-bold text-gray-900 text-right w-24">Harga</th>
                    <th class="py-2.5 font-bold text-gray-900 text-right w-24">Subtotal</th>
                </tr>
            </thead>
            <tbody id="printProductsList" class="divide-y divide-dashed divide-gray-300">
                <!-- Dynamically populated via JS -->
            </tbody>
        </table>

        <!-- Totals & Summary -->
        <div class="border-t border-solid border-gray-300 pt-3 mt-4 text-xs">
            <div class="flex flex-col gap-2.5 max-w-xs ml-auto">
                <div class="flex justify-between font-bold text-gray-800">
                    <span>Subtotal</span>
                    <span id="printSubtotal">Rp. 0</span>
                </div>
                <div class="flex justify-between font-bold text-gray-900 border-t border-solid border-gray-300 pt-2.5 text-sm">
                    <span>Total</span>
                    <span id="printTotal" class="font-bold text-base">Rp. 0</span>
                </div>
                <div class="flex justify-between font-bold text-gray-800 mt-0.5">
                    <span>Metode Pembayaran</span>
                    <span id="printMethod" class="uppercase">-</span>
                </div>
            </div>
        </div>
    </div>

</div>
