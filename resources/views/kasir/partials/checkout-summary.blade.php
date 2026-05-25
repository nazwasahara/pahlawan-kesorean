<div
    id="checkoutSummary"
    class="flex-1 overflow-y-auto px-2 md:px-4 max-h-64 md:max-h-96"
    style="display:none;"
>
    <div class="checkout-summary">
        <!-- Customer Info -->
        <div class="summary-section">
            <span class="summary-title">Detail Pelanggan</span>
            <div class="summary-info">
                <div class="summary-row">
                    <span class="summary-label">Nama</span>
                    <span class="summary-value" id="summaryCustomerName">-</span>
                </div>
                <div class="summary-row">
                    <span class="summary-label">Tipe</span>
                    <span class="summary-value" id="summaryOrderType">-</span>
                </div>
                <div class="summary-row" id="summaryTableNumberRow">
                    <span class="summary-label">Meja</span>
                    <span class="summary-value" id="summaryTableNumber">-</span>
                </div>
            </div>
        </div>

        <!-- Items List -->
        <div class="summary-section">
            <span class="summary-title">Pesanan</span>
            <div class="summary-items" id="summaryItemsList">
                <!-- Items will be rendered here -->
            </div>
        </div>

        <!-- Totals -->
        <div class="summary-section">
            <span class="summary-title">Total</span>
            <div class="summary-totals">
                <div class="summary-total-row">
                    <span class="summary-label">Subtotal</span>
                    <span class="summary-value" id="summarySubtotal">Rp 0</span>
                </div>
                <div class="summary-total-row">
                    <span class="summary-label">Diskon</span>
                    <span class="summary-value" id="summaryDiscount">Rp 0</span>
                </div>
                <div class="summary-total-row grand-total">
                    <span class="summary-label">Total</span>
                    <span class="summary-value" id="summaryTotal">Rp 0</span>
                </div>
            </div>
        </div>

        <!-- Notes -->
        <div class="summary-section">
            <span class="summary-title">Catatan</span>
            <div class="summary-notes">
                <div class="summary-notes-label">Catatan Khusus (Opsional)</div>
                <textarea
                    id="orderNotes"
                    placeholder="Tambah catatan khusus untuk pesanan..."
                    class="w-full px-2 py-2 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400"
                    style="resize: vertical; min-height: 60px; font-family: inherit;"
                ></textarea>
            </div>
        </div>
    </div>
</div>
