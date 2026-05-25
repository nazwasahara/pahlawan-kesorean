let cart = [];
let activeCategory = '';
let currentOrderTotal = 0;
let currentOrderName = '';
let currentOrderTable = '';
let currentOrderType = 'dine_in';
let qrisTimerInterval = null;
let qrisTimeRemaining = 0;
let qrisPollingInterval = null;

async function persistOrder(paymentMethod, extra = {}) {
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const note = document.getElementById('orderNotes')?.value?.trim() || '';
    const tblNum = currentOrderTable || document.getElementById('tableNumber')?.value || '';
    const orderType = currentOrderType || document.getElementById('orderType')?.value || 'dine_in';

    const payload = {
        customer_name: currentOrderName || document.getElementById('customerName')?.value?.trim() || '',
        table_number: tblNum,
        payment_method: paymentMethod,
        order_type: orderType,
        discount: 0,
        note,
        items: cart.map(item => ({
            id: Number(item.id),
            qty: Number(item.qty),
        })),
        ...extra,
    };

    const res = await fetch('/kasir/orders/checkout', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf || '',
        },
        body: JSON.stringify(payload),
    });

    const json = await res.json();
    if (!res.ok) {
        const message = json?.message || 'Gagal menyimpan order.';
        throw new Error(message);
    }

    return json?.data || null;
}

function filterCategory(btn, category) {
    document
        .querySelectorAll('#categoryTabs button')
        .forEach(b => b.classList.remove('active'));

    btn.classList.add('active');

    activeCategory = category;

    applyFilter();
}

function filterMenu() {
    applyFilter();
}

function applyFilter() {
    const q = document
        .getElementById('searchInput')
        .value
        .toLowerCase()
        .trim();

    document
        .querySelectorAll('#menuGrid .menu-card')
        .forEach(card => {

            const nameMatch = card.dataset.name.includes(q);

            const catMatch =
                !activeCategory ||
                card.dataset.category === activeCategory;

            card.style.display =
                (nameMatch && catMatch)
                    ? ''
                    : 'none';
        });
}

function addToCart(id, name, price, image) {
    // normalize id to number for consistent comparisons
    id = Number(id);

    // determine stock from DOM if available
    let stock = Infinity;
    const card = document.querySelector(`[data-id="${id}"]`);
    if (card && card.dataset && card.dataset.stock !== undefined) {
        stock = Number(card.dataset.stock);
    }

    const existing = cart.find(i => Number(i.id) === id);

    if (existing) {
        if (existing.qty + 1 > stock) {
            alert('Stok tidak cukup untuk menambah item ini.');
            return;
        }
        existing.qty++;
    } else {
        if (stock <= 0) {
            alert('Produk habis stok.');
            return;
        }
        cart.push({
            id,
            name,
            price: Number(price),
            image,
            qty: 1,
            stock: stock
        });
    }

    renderCart();
}

function changeQty(id, delta) {

    id = Number(id);

    const item = cart.find(i => Number(i.id) === id);

    if (!item) return;

    // enforce stock limit when increasing
    if (delta > 0) {
        const maxStock = item.stock !== undefined ? Number(item.stock) : Infinity;
        if (item.qty + delta > maxStock) {
            alert('Stok tidak cukup.');
            return;
        }
    }

    item.qty += delta;

    if (item.qty <= 0) {
        cart = cart.filter(i => Number(i.id) !== id);
    }

    renderCart();
}

function renderCart() {

    const container = document.getElementById('cartItems');
    const footer    = document.getElementById('cartFooter');
    // defensive checks: ensure required elements exist
    if (!container) {
        console.error('renderCart: container #cartItems not found');
        return;
    }

    if (!footer) {
        console.warn('renderCart: footer #cartFooter not found');
    }

    let emptyEl = container.querySelector('#cartEmpty') || document.getElementById('cartEmpty');

    // create a fallback empty element if it's missing (prevents null errors)
    if (!emptyEl) {
        emptyEl = document.createElement('div');
        emptyEl.id = 'cartEmpty';
        emptyEl.className = 'text-center py-10 text-gray-400';
        emptyEl.innerHTML = `
            <i class="ri-shopping-cart-line text-3xl mb-2 block"></i>
            <p class="text-xs">Keranjang kosong</p>
            <p class="text-xs mt-1 text-gray-300">Pilih menu untuk memulai</p>
        `;
    }

    if (cart.length === 0) {
        try {
            emptyEl.style.display = '';
        } catch (e) {
            console.warn('renderCart: could not set emptyEl.style', e);
        }

        if (footer) footer.style.display  = 'none';

        container.innerHTML = '';

        container.appendChild(emptyEl);

        return;
    }

    emptyEl.style.display = 'none';
    footer.style.display  = '';

    let html  = '';
    let total = 0;

    cart.forEach(item => {

        total += item.price * item.qty;

        const isHttp = item.image && (item.image.startsWith('http://') || item.image.startsWith('https://'));
        const imgSrc = item.image ? (isHttp ? item.image : `/storage/${item.image}`) : null;
        const imgHtml = imgSrc 
            ? `<div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0 overflow-hidden border border-gray-200 shadow-inner">
                    <img src="${imgSrc}" class="w-full h-full object-cover" alt="${item.name}">
               </div>`
            : `<div class="w-10 h-10 rounded-lg bg-gray-300 flex items-center justify-center flex-shrink-0">
                    <i class="ri-image-line text-gray-400"></i>
               </div>`;

        // determine if plus button should be disabled (stock reached)
        const atMax = item.stock !== undefined && Number(item.qty) >= Number(item.stock);

        html += `
            <div class="flex items-center gap-2 mb-3">

                ${imgHtml}

                <div class="flex-1 min-w-0">
                    <p class="text-xs font-medium text-gray-800 truncate">
                        ${item.name}
                    </p>

                    <p class="text-xs text-green-700 font-bold">
                        Rp ${item.price.toLocaleString('id-ID')}
                    </p>
                </div>

                <div class="flex items-center gap-1 flex-shrink-0">

                    <button
                        onclick="changeQty(${item.id}, -1)"
                        class="w-6 h-6 rounded-full border border-gray-300 flex items-center justify-center text-gray-600 hover:bg-gray-100 text-xs"
                    >
                        −
                    </button>

                    <span class="text-xs w-4 text-center font-medium">
                        ${item.qty}
                    </span>

                    <button
                        onclick="changeQty(${item.id}, 1)"
                        class="w-6 h-6 rounded-full ${atMax ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-green-700 text-white hover:bg-green-800'} flex items-center justify-center text-xs"
                        ${atMax ? 'disabled' : ''}
                    >
                        +
                    </button>

                </div>

            </div>
        `;
    });

    container.innerHTML = html;

    document.getElementById('cartTotal').textContent =
        'Rp ' + total.toLocaleString('id-ID');
}

function checkout() {

    const name = document
        .getElementById('customerName')
        .value
        .trim();

    const orderType = document
        .getElementById('orderType')
        .value;

    const table = document
        .getElementById('tableNumber')
        .value;

    if (!name) {
        alert('Masukkan nama pelanggan terlebih dahulu.');
        return;
    }

    if (orderType === 'dine_in' && !table) {
        alert('Masukkan nomor meja terlebih dahulu.');
        return;
    }

    if (cart.length === 0) {
        alert('Keranjang masih kosong.');
        return;
    }

    // Show checkout summary
    showCheckoutSummary(name, table, orderType);
}

function showCheckoutSummary(name, table, orderType) {
    // Update customer info
    document.getElementById('summaryCustomerName').textContent = name;
    
    const summaryOrderTypeEl = document.getElementById('summaryOrderType');
    if (summaryOrderTypeEl) {
        summaryOrderTypeEl.textContent = orderType === 'dine_in' ? 'Dine In' : 'Take Away';
    }

    const tableRow = document.getElementById('summaryTableNumberRow');
    if (tableRow) {
        if (orderType === 'take_away') {
            tableRow.style.display = 'none';
        } else {
            tableRow.style.display = 'flex';
            document.getElementById('summaryTableNumber').textContent = `Meja ${table}`;
        }
    }

    // Render items
    let itemsHtml = '';
    let total = 0;

    cart.forEach(item => {
        const itemTotal = item.price * item.qty;
        total += itemTotal;

        itemsHtml += `
            <div class="summary-item">
                <div class="summary-item-detail">
                    <div class="summary-item-name">${item.name}</div>
                    <div class="summary-item-qty">${item.qty}x Rp ${item.price.toLocaleString('id-ID')}</div>
                </div>
                <div class="summary-item-price">Rp ${itemTotal.toLocaleString('id-ID')}</div>
            </div>
        `;
    });

    document.getElementById('summaryItemsList').innerHTML = itemsHtml;

    // Update totals
    document.getElementById('summarySubtotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('summaryDiscount').textContent = 'Rp 0';
    document.getElementById('summaryTotal').textContent = 'Rp ' + total.toLocaleString('id-ID');

    // Clear notes
    document.getElementById('orderNotes').value = '';

    // Hide cart, show summary
    document.getElementById('cartItems').style.display = 'none';
    document.getElementById('cartFooter').style.display = 'none';
    document.getElementById('checkoutSummary').style.display = '';
    document.getElementById('checkoutFooter').style.display = 'flex';
}

function backToCart() {
    // Show cart, hide summary
    document.getElementById('cartItems').style.display = '';
    document.getElementById('cartFooter').style.display = '';
    document.getElementById('checkoutSummary').style.display = 'none';
    document.getElementById('checkoutFooter').style.display = 'none';
    document.getElementById('orderNotes').value = '';
}

function selectPaymentMethod() {
    // Hide POS view, show payment full page
    document.getElementById('posView').style.display = 'none';
    document.getElementById('paymentFullView').style.display = 'flex';
}

function backToCheckout() {
    // Show POS view, hide payment full page
    document.getElementById('posView').style.display = 'flex';
    document.getElementById('paymentFullView').style.display = 'none';
}

function selectPayment(method) {
    const notes = document.getElementById('orderNotes').value.trim();
    const name = document.getElementById('customerName').value.trim();
    const table = document.getElementById('tableNumber').value;
    const orderType = document.getElementById('orderType').value;

    console.log('Payment Confirmed:', {
        method: method,
        customer: name,
        table: table,
        orderType: orderType,
        items: cart,
        notes: notes
    });

    // Store current order info
    currentOrderName = name;
    currentOrderTable = table;
    currentOrderType = orderType;

    // Map method to display name
    const methodNames = {
        'cash': 'Cash',
        'qris': 'QRIS',
        'debit': 'Debit / Kartu'
    };

    if (method === 'cash') {
        showCashPayment(name, table);
    } else if (method === 'qris') {
        showQRISPayment(name, table);
    } else {
        alert('Pembayaran dengan ' + methodNames[method] + ' dipilih.\n\nData yang akan dikirim:\nNama: ' + name + '\nMeja: ' + table + '\nCatatan: ' + (notes || 'Tidak ada') + '\n\nMetode: ' + methodNames[method]);
    }
}

function showCashPayment(name, table) {
    // Calculate total
    let total = 0;
    cart.forEach(item => {
        total += item.price * item.qty;
    });
    currentOrderTotal = total;

    // Update customer info
    document.getElementById('cashCustomerName').textContent = name;
    document.getElementById('cashTableNumber').textContent = currentOrderType === 'take_away' ? 'Take Away' : `Meja ${table}`;

    // Render items
    let itemsHtml = '';
    cart.forEach(item => {
        const itemTotal = item.price * item.qty;
        const isHttp = item.image && (item.image.startsWith('http://') || item.image.startsWith('https://'));
        const imgSrc = item.image ? (isHttp ? item.image : `/storage/${item.image}`) : null;
        const imgHtml = imgSrc 
            ? `<div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0 overflow-hidden border border-gray-200 shadow-inner">
                    <img src="${imgSrc}" class="w-full h-full object-cover" alt="${item.name}">
               </div>`
            : `<div class="w-12 h-12 rounded-lg bg-gray-300 flex items-center justify-center flex-shrink-0">
                    <i class="ri-image-line text-gray-400 text-lg"></i>
               </div>`;

        itemsHtml += `
            <div class="p-3 border-b border-gray-100 flex gap-3 justify-between items-start">
                ${imgHtml}
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-900 truncate">${item.name}</p>
                    <p class="text-xs text-gray-600">${item.qty}x Rp ${item.price.toLocaleString('id-ID')}</p>
                </div>
                <div class="flex items-start gap-3 flex-shrink-0">
                    <p class="text-sm font-bold text-green-700 text-right">Rp ${itemTotal.toLocaleString('id-ID')}</p>
                </div>
            </div>
        `;
    });

    document.getElementById('cashItemsList').innerHTML = itemsHtml;

    // Update totals
    document.getElementById('cashSubtotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('cashTotal').textContent = 'Rp ' + total.toLocaleString('id-ID');
    document.getElementById('changeAmount').textContent = 'Rp 0';
    document.getElementById('customMoneyInput').value = '';

    // Hide payment method view, show cash payment view
    document.getElementById('paymentFullView').style.display = 'none';
    document.getElementById('cashPaymentView').style.display = 'flex';
}

function setMoneyReceived(amount) {
    document.getElementById('customMoneyInput').value = amount;
    calculateChange();
}

function calculateChange() {
    const customMoneyInput = document.getElementById('customMoneyInput');
    const moneyReceived = customMoneyInput.value ? Number(customMoneyInput.value) : 0;
    const change = moneyReceived - currentOrderTotal;

    const changeAmountEl = document.getElementById('changeAmount');
    if (change < 0) {
        changeAmountEl.textContent = 'Rp ' + Math.abs(change).toLocaleString('id-ID');
        changeAmountEl.classList.add('text-red-600');
        changeAmountEl.classList.remove('text-green-700');
    } else {
        changeAmountEl.textContent = 'Rp ' + change.toLocaleString('id-ID');
        changeAmountEl.classList.add('text-green-700');
        changeAmountEl.classList.remove('text-red-600');
    }
}

function backToPaymentMethod() {
    document.getElementById('cashPaymentView').style.display = 'none';
    document.getElementById('paymentFullView').style.display = 'flex';
}

async function confirmCashPayment() {
    const moneyReceived = document.getElementById('customMoneyInput').value ? Number(document.getElementById('customMoneyInput').value) : 0;

    if (moneyReceived < currentOrderTotal) {
        alert('Uang yang diterima tidak cukup!\nTotal: Rp ' + currentOrderTotal.toLocaleString('id-ID') + '\nUang diterima: Rp ' + moneyReceived.toLocaleString('id-ID'));
        return;
    }

    const change = moneyReceived - currentOrderTotal;

    console.log('Cash Payment Confirmed:', {
        customer: currentOrderName,
        table: currentOrderTable,
        total: currentOrderTotal,
        moneyReceived: moneyReceived,
        change: change,
        items: cart
    });

    try {
        const saved = await persistOrder('cash', { money_received: moneyReceived });
        showPaymentSuccess('Cash', {
            total: saved?.total ?? currentOrderTotal,
            change: saved?.change ?? change,
            orderId: saved?.order_number,
            date: saved?.paid_at,
        });
    } catch (err) {
        alert(err.message || 'Gagal menyimpan pembayaran cash.');
    }
}

// --- QRIS payment helpers ---
let currentQRISOrderId = '';

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

function renderQRISCode() {
    const qrisPayload = generateQRISPayload(currentOrderTotal, currentQRISOrderId);
    
    const rawInput = document.getElementById('qrisRawPayload');
    if (rawInput) rawInput.value = qrisPayload;

    const qrCanvas = document.getElementById('qrisCanvas');
    if (qrCanvas) {
        try {
            new QRious({
                element: qrCanvas,
                value: qrisPayload,
                size: 200,
                level: 'M'
            });
        } catch (e) {
            console.error('Error generating QR code with QRious:', e);
        }
    }
}

function downloadQRIS() {
    const canvas = document.getElementById('qrisCanvas');
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
    ctx.fillText(currentQRISOrderId, dlCanvas.width / 2, 420);
    
    ctx.fillStyle = '#15803D';
    ctx.font = 'bold 24px sans-serif';
    ctx.fillText('Rp ' + currentOrderTotal.toLocaleString('id-ID'), dlCanvas.width / 2, 460);
    
    ctx.fillStyle = '#64748B';
    ctx.font = 'italic 11px sans-serif';
    ctx.fillText('Dicetak via Pahlawan Kesorean POS', dlCanvas.width / 2, 525);
    
    const link = document.createElement('a');
    link.download = `QRIS-${currentQRISOrderId}.png`;
    link.href = dlCanvas.toDataURL('image/png');
    link.click();
}

function copyQRISPayload() {
    const rawInput = document.getElementById('qrisRawPayload');
    if (!rawInput) return;
    
    rawInput.select();
    rawInput.setSelectionRange(0, 99999);
    navigator.clipboard.writeText(rawInput.value);
    
    alert('Payload QRIS berhasil disalin ke clipboard!');
}

async function showQRISPayment(name, table) {
    const qrisBtn = document.querySelector('button[onclick*="selectPaymentMethod"][onclick*="qris"]');
    let originalText = '';
    if (qrisBtn) {
        originalText = qrisBtn.innerHTML;
        qrisBtn.disabled = true;
        qrisBtn.innerHTML = `<i class="ri-loader-4-line animate-spin text-lg"></i> Membuat QRIS...`;
    }

    try {
        let total = 0;
        cart.forEach(item => { total += item.price * item.qty; });
        currentOrderTotal = total;
        currentOrderName = name;
        currentOrderTable = table;

        // Persist order with status pending to get real order number
        const saved = await persistOrder('qris', { status: 'pending' });
        if (!saved || !saved.order_number) {
            throw new Error("Gagal membuat pesanan QRIS di server.");
        }
        currentQRISOrderId = saved.order_number;

        const qrisCustNameEl = document.getElementById('qrisCustomerName');
        if (qrisCustNameEl) qrisCustNameEl.textContent = name;
        
        const qrisTableNumberEl = document.getElementById('qrisTableNumber');
        if (qrisTableNumberEl) qrisTableNumberEl.textContent = currentOrderType === 'take_away' ? 'Take Away' : `Meja ${table}`;

        const qrisAmountValEl = document.getElementById('qrisAmountVal');
        if (qrisAmountValEl) qrisAmountValEl.textContent = 'Rp ' + total.toLocaleString('id-ID');

        let itemsHtml = '';
        cart.forEach(item => {
            const itemTotal = item.price * item.qty;
            const isHttp = item.image && (item.image.startsWith('http://') || item.image.startsWith('https://'));
            const imgSrc = item.image ? (isHttp ? item.image : `/storage/${item.image}`) : null;
            const imgHtml = imgSrc 
                ? `<div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center flex-shrink-0 overflow-hidden border border-gray-200 shadow-inner">
                        <img src="${imgSrc}" class="w-full h-full object-cover" alt="${item.name}">
                   </div>`
                : `<div class="w-12 h-12 rounded-lg bg-gray-300 flex items-center justify-center flex-shrink-0">
                        <i class="ri-image-line text-gray-400 text-lg"></i>
                   </div>`;

            itemsHtml += `
                <div class="p-3 border-b border-gray-100 flex gap-3 justify-between items-start">
                    ${imgHtml}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-900 truncate">${item.name}</p>
                        <p class="text-xs text-gray-600">${item.qty}x Rp ${item.price.toLocaleString('id-ID')}</p>
                    </div>
                    <div class="flex items-start gap-3 flex-shrink-0">
                        <p class="text-sm font-bold text-green-700 text-right">Rp ${itemTotal.toLocaleString('id-ID')}</p>
                    </div>
                </div>
            `;
        });
        
        const qrisItemsListEl = document.getElementById('qrisItemsList');
        if (qrisItemsListEl) qrisItemsListEl.innerHTML = itemsHtml;

        const qrisSubtotalEl = document.getElementById('qrisSubtotal');
        if (qrisSubtotalEl) qrisSubtotalEl.textContent = 'Rp ' + total.toLocaleString('id-ID');
        
        const qrisTotalEl = document.getElementById('qrisTotal');
        if (qrisTotalEl) qrisTotalEl.textContent = 'Rp ' + total.toLocaleString('id-ID');

        const progressEl = document.getElementById('qrisProgress');
        if (progressEl) {
            progressEl.style.width = '100%';
            progressEl.style.backgroundColor = '#15803d';
        }

        const timerEl = document.getElementById('qrisTimer');
        if (timerEl) timerEl.textContent = formatTime(300);

        const paymentFull = document.getElementById('paymentFullView');
        const qrisView = document.getElementById('qrisPaymentView');
        if (paymentFull) paymentFull.style.display = 'none';
        if (qrisView) qrisView.style.display = 'flex';

        setTimeout(() => {
            renderQRISCode();
        }, 100);

        startQRTimer(300);
        startQRISStatusPolling();

    } catch (err) {
        alert(err.message || 'Gagal menyiapkan pembayaran QRIS.');
    } finally {
        if (qrisBtn) {
            qrisBtn.disabled = false;
            qrisBtn.innerHTML = originalText;
        }
    }
}

function startQRTimer(durationSeconds) {
    if (qrisTimerInterval) clearInterval(qrisTimerInterval);
    
    const storageKey = `qris_expiry_${currentQRISOrderId}`;
    let expiryTime = localStorage.getItem(storageKey);
    
    if (!expiryTime) {
        expiryTime = Date.now() + (durationSeconds * 1000);
        localStorage.setItem(storageKey, expiryTime);
    } else {
        expiryTime = parseInt(expiryTime);
    }

    const totalDuration = durationSeconds;
    const timerEl = document.getElementById('qrisTimer');
    const progressEl = document.getElementById('qrisProgress');
    if (!timerEl) return;

    const updateTimer = () => {
        const now = Date.now();
        qrisTimeRemaining = Math.max(0, Math.floor((expiryTime - now) / 1000));
        
        if (qrisTimeRemaining <= 0) {
            clearInterval(qrisTimerInterval);
            qrisTimerInterval = null;
            timerEl.textContent = 'Expired';
            if (progressEl) {
                progressEl.style.width = '0%';
                progressEl.style.backgroundColor = '#DC2626';
            }
            const expiredOverlay = document.getElementById('qrisExpiredOverlay');
            if (expiredOverlay) expiredOverlay.style.display = 'flex';
            
            // Auto cancel order on expiry
            cancelQRISOrder();
            return;
        }
        
        timerEl.textContent = formatTime(qrisTimeRemaining);
        
        if (progressEl) {
            const pct = (qrisTimeRemaining / totalDuration) * 100;
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
    qrisTimerInterval = setInterval(updateTimer, 1000);
    
    const expiredOverlay = document.getElementById('qrisExpiredOverlay');
    if (expiredOverlay) expiredOverlay.style.display = 'none';
}

function regenerateQRIS() {
    currentQRISOrderId = generateOrderId();
    startQRTimer(300);
    renderQRISCode();
}

async function cancelQRISOrder() {
    try {
        const saved = await persistOrder('qris', { status: 'cancelled' });
        alert(`Transaksi QRIS Dibatalkan!\nOrder: ${saved?.order_number || currentQRISOrderId}\nStatus: ${saved?.status || 'cancelled'}`);
        
        if (qrisTimerInterval) clearInterval(qrisTimerInterval);
        qrisTimerInterval = null;
        qrisTimeRemaining = 0;
        
        const qrisView = document.getElementById('qrisPaymentView');
        if (qrisView) qrisView.style.display = 'none';
        
        finishAndReset();
    } catch (err) {
        alert(err.message || 'Gagal memproses pembatalan order.');
    }
}

function startQRISStatusPolling() {
    if (qrisPollingInterval) clearInterval(qrisPollingInterval);

    qrisPollingInterval = setInterval(async () => {
        if (!currentQRISOrderId) {
            clearInterval(qrisPollingInterval);
            qrisPollingInterval = null;
            return;
        }

        try {
            const res = await fetch(`/checkout/status/${currentQRISOrderId}`);
            if (!res.ok) return;

            const data = await res.json();
            if (data.status === 'paid' || data.status === 'completed' || data.status === 'processing') {
                clearInterval(qrisPollingInterval);
                qrisPollingInterval = null;

                if (qrisTimerInterval) clearInterval(qrisTimerInterval);
                qrisTimerInterval = null;
                qrisTimeRemaining = 0;

                const qrisView = document.getElementById('qrisPaymentView');
                if (qrisView) qrisView.style.display = 'none';

                const paidDate = new Date().toLocaleString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) + ' WIB';

                showPaymentSuccess('QRIS', {
                    total: currentOrderTotal,
                    change: 0,
                    orderId: currentQRISOrderId,
                    date: paidDate,
                });
            } else if (data.status === 'cancelled') {
                clearInterval(qrisPollingInterval);
                qrisPollingInterval = null;

                if (qrisTimerInterval) clearInterval(qrisTimerInterval);
                qrisTimerInterval = null;
                qrisTimeRemaining = 0;

                const qrisView = document.getElementById('qrisPaymentView');
                if (qrisView) qrisView.style.display = 'none';

                alert('Transaksi QRIS Kedaluwarsa atau Dibatalkan oleh Sistem.');
                finishAndReset();
            }
        } catch (err) {
            console.error('Polling error:', err);
        }
    }, 2000);
}

function backToPaymentMethodFromQRIS() {
    if (qrisTimerInterval) clearInterval(qrisTimerInterval);
    qrisTimerInterval = null;
    qrisTimeRemaining = 0;

    if (qrisPollingInterval) clearInterval(qrisPollingInterval);
    qrisPollingInterval = null;

    const qrisView = document.getElementById('qrisPaymentView');
    const paymentFull = document.getElementById('paymentFullView');

    if (qrisView) qrisView.style.display = 'none';
    if (paymentFull) paymentFull.style.display = 'flex';
}

function formatTime(sec) {
    const m = Math.floor(sec / 60).toString().padStart(2, '0');
    const s = Math.floor(sec % 60).toString().padStart(2, '0');
    return `${m}:${s}`;
}

// --- Payment success helpers ---
function generateOrderId() {
    const ts = Date.now().toString();
    return 'POS' + ts.slice(-6);
}

function showPaymentSuccess(method, opts = {}) {
    // opts: { total, change }
    const orderId = opts.orderId || generateOrderId();
    const date = opts.date || new Date();

    const orderEl = document.getElementById('successOrderId');
    const dateEl = document.getElementById('successDate');
    const methodEl = document.getElementById('successMethod');
    const totalEl = document.getElementById('successTotal');
    const changeEl = document.getElementById('successChange');

    if (orderEl) orderEl.textContent = orderId;
    if (dateEl) dateEl.textContent = typeof date === 'string' ? date : date.toLocaleString('id-ID');
    if (methodEl) methodEl.textContent = method;
    if (totalEl) totalEl.textContent = 'Rp ' + (opts.total || 0).toLocaleString('id-ID');
    if (changeEl) changeEl.textContent = 'Rp ' + (opts.change || 0).toLocaleString('id-ID');

    // Populate Print Receipt details
    const printOrderEl = document.getElementById('printOrderId');
    const printCustomerEl = document.getElementById('printCustomerName');
    const printOrderTypeEl = document.getElementById('printOrderType');
    const printTableEl = document.getElementById('printTableNumber');
    const printDateEl = document.getElementById('printDate');
    const printSubtotalEl = document.getElementById('printSubtotal');
    const printTotalEl = document.getElementById('printTotal');
    const printMethodEl = document.getElementById('printMethod');
    const printCashSection = document.getElementById('printCashSection');
    const printCashAmountEl = document.getElementById('printCashAmount');
    const printChangeSection = document.getElementById('printChangeSection');
    const printChangeAmountEl = document.getElementById('printChangeAmount');
    const printProductsList = document.getElementById('printProductsList');

    if (printOrderEl) printOrderEl.textContent = '#' + orderId;
    if (printCustomerEl) printCustomerEl.textContent = typeof currentOrderName !== 'undefined' ? (currentOrderName || 'Pelanggan POS') : 'Pelanggan POS';
    
    const tableNum = typeof currentOrderTable !== 'undefined' ? currentOrderTable : '';
    const orderType = typeof currentOrderType !== 'undefined' ? currentOrderType : (tableNum ? 'dine_in' : 'take_away');
    if (printOrderTypeEl) printOrderTypeEl.textContent = orderType === 'take_away' ? 'Take Away' : 'Dine In';
    if (printTableEl) printTableEl.textContent = orderType === 'take_away' ? '-' : (tableNum ? tableNum : '-');
    
    if (printDateEl) printDateEl.textContent = typeof date === 'string' ? date : date.toLocaleString('id-ID');

    let subtotal = 0;
    if (typeof cart !== 'undefined' && Array.isArray(cart)) {
        subtotal = cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
    }
    
    if (printSubtotalEl) printSubtotalEl.textContent = 'Rp. ' + subtotal.toLocaleString('id-ID');
    if (printTotalEl) printTotalEl.textContent = 'Rp. ' + (opts.total || subtotal).toLocaleString('id-ID');
    if (printMethodEl) printMethodEl.textContent = method;

    const isCash = method.toLowerCase() === 'cash' || method.toLowerCase() === 'tunai';
    if (isCash) {
        if (printCashSection) printCashSection.style.display = 'flex';
        if (printChangeSection) printChangeSection.style.display = 'flex';
        const totalVal = opts.total || subtotal;
        const changeVal = opts.change || 0;
        const moneyRec = totalVal + changeVal;
        if (printCashAmountEl) printCashAmountEl.textContent = 'Rp. ' + moneyRec.toLocaleString('id-ID');
        if (printChangeAmountEl) printChangeAmountEl.textContent = 'Rp. ' + changeVal.toLocaleString('id-ID');
    } else {
        if (printCashSection) printCashSection.style.display = 'none';
        if (printChangeSection) printChangeSection.style.display = 'none';
    }

    if (printProductsList) {
        printProductsList.innerHTML = '';
        if (typeof cart !== 'undefined' && Array.isArray(cart)) {
            cart.forEach(item => {
                const tr = document.createElement('tr');
                const noteHtml = item.note ? `<div class="text-[10px] text-gray-500 italic">Note: ${item.note}</div>` : '';
                tr.innerHTML = `
                    <td class="py-2.5 font-semibold text-gray-800">
                        <div>${item.name}</div>
                        ${noteHtml}
                    </td>
                    <td class="py-2.5 text-center font-semibold text-gray-800">${item.qty}</td>
                    <td class="py-2.5 text-right font-semibold text-gray-800">Rp. ${item.price.toLocaleString('id-ID')}</td>
                    <td class="py-2.5 text-right font-semibold text-gray-800">Rp. ${(item.price * item.qty).toLocaleString('id-ID')}</td>
                `;
                printProductsList.appendChild(tr);
            });
        }
    }

    // hide other views
    const views = ['posView','paymentFullView','cashPaymentView','debitPaymentView','qrisPaymentView'];
    views.forEach(id => {
        const el = document.getElementById(id);
        if (el) el.style.display = 'none';
    });

    const successView = document.getElementById('paymentSuccessView');
    if (successView) successView.style.display = 'flex';
}

function printReceipt() {
    // simple print
    window.print();
}

function finishAndReset() {
    window.location.reload();
}

// --- Debit/Card payment helpers ---
function selectDebitPayment() {
    const name = document.getElementById('customerName').value.trim();
    const table = document.getElementById('tableNumber').value;
    showDebitPayment(name, table);
}

function showDebitPayment(name, table) {
    let total = 0;
    cart.forEach(item => { total += item.price * item.qty; });
    currentOrderTotal = total;
    currentOrderName = name;
    currentOrderTable = table;

    const totalEl = document.getElementById('debitTotal');
    if (totalEl) totalEl.textContent = 'Rp ' + total.toLocaleString('id-ID');

    const paymentFull = document.getElementById('paymentFullView');
    const debitView = document.getElementById('debitPaymentView');
    if (paymentFull) paymentFull.style.display = 'none';
    if (debitView) debitView.style.display = 'flex';
}

function backToPaymentMethodFromDebit() {
    const debitView = document.getElementById('debitPaymentView');
    const paymentFull = document.getElementById('paymentFullView');
    if (debitView) debitView.style.display = 'none';
    if (paymentFull) paymentFull.style.display = 'flex';
}

async function confirmDebitPayment() {
    const cardNumber = (document.getElementById('debitCardNumber') || {}).value || '';
    const rawCardNumber = cardNumber.replace(/\s+/g, '');
    const cardName = (document.getElementById('debitCardName') || {}).value || '';
    const expiry = (document.getElementById('debitExpiry') || {}).value || '';
    const cvv = (document.getElementById('debitCVV') || {}).value || '';

    // sanitize
    const rawExpiry = expiry.replace(/\D/g, ''); // MMYY
    const rawCvv = cvv.replace(/\D/g, '');

    if (!rawCardNumber || !cardName || !rawExpiry || !rawCvv) {
        alert('Mohon lengkapi semua detail kartu.');
        return;
    }

    if (rawCvv.length !== 3) {
        alert('CVV harus 3 digit.');
        return;
    }

    if (rawExpiry.length !== 4) {
        alert('Masa berlaku harus 4 digit (MMYY).');
        return;
    }

    const month = parseInt(rawExpiry.slice(0,2), 10);
    if (!(month >=1 && month <= 12)) {
        alert('Bulan pada masa berlaku tidak valid.');
        return;
    }

    const masked = rawCardNumber.replace(/.(?=.{4})/g, '*');
    console.log('Debit Payment:', { customer: currentOrderName, table: currentOrderTable, total: currentOrderTotal, cardNumber: masked, cardName, expiry });

    try {
        const saved = await persistOrder('debit');

        // Close debit view and show success
        const debitView = document.getElementById('debitPaymentView');
        if (debitView) debitView.style.display = 'none';

        showPaymentSuccess('Debit / Kartu', {
            total: saved?.total ?? currentOrderTotal,
            change: saved?.change ?? 0,
            orderId: saved?.order_number,
            date: saved?.paid_at,
        });
    } catch (err) {
        alert(err.message || 'Gagal menyimpan pembayaran debit.');
    }
}

// Category scroll drag handler removed to allow native scrolling

// Format debit card input: digits only, space every 4 digits
(function attachDebitFormatting(){
    const input = document.getElementById('debitCardNumber');
    if (!input) return;

    input.addEventListener('input', (e) => {
        const el = e.target;
        // keep only digits and limit to 16 digits
        const digits = el.value.replace(/\D/g, '').slice(0, 16);
        // group every 4 digits
        const parts = digits.match(/.{1,4}/g);
        const formatted = parts ? parts.join(' ') : digits;
        el.value = formatted;
    });

    // on paste, normalize
    input.addEventListener('paste', (e) => {
        e.preventDefault();
        const paste = (e.clipboardData || window.clipboardData).getData('text');
        const digits = paste.replace(/\D/g, '').slice(0, 16);
        const parts = digits.match(/.{1,4}/g);
        input.value = parts ? parts.join(' ') : digits;
    });
})();

// Attach CVV and Expiry formatting/validation
(function attachExpiryAndCvv(){
    const expiry = document.getElementById('debitExpiry');
    const cvv = document.getElementById('debitCVV');

    if (expiry) {
        expiry.addEventListener('input', (e) => {
            const el = e.target;
            // keep only digits, max 4 (MMYY)
            const digits = el.value.replace(/\D/g, '').slice(0, 4);
            const m = digits.slice(0,2);
            const y = digits.slice(2,4);
            el.value = y ? (m + '/' + y) : m;
        });

        expiry.addEventListener('paste', (e) => {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            const digits = paste.replace(/\D/g, '').slice(0,4);
            const m = digits.slice(0,2);
            const y = digits.slice(2,4);
            expiry.value = y ? (m + '/' + y) : m;
        });
    }

    if (cvv) {
        cvv.addEventListener('input', (e) => {
            const el = e.target;
            el.value = el.value.replace(/\D/g, '').slice(0,3);
        });

        cvv.addEventListener('paste', (e) => {
            e.preventDefault();
            const paste = (e.clipboardData || window.clipboardData).getData('text');
            cvv.value = paste.replace(/\D/g, '').slice(0,3);
        });
    }
})();

function toggleTableSelection() {
    const orderType = document.getElementById('orderType').value;
    const tableNumberSelect = document.getElementById('tableNumber');
    if (tableNumberSelect) {
        if (orderType === 'take_away') {
            tableNumberSelect.value = '';
            tableNumberSelect.disabled = true;
            tableNumberSelect.classList.add('bg-gray-100', 'cursor-not-allowed');
        } else {
            tableNumberSelect.disabled = false;
            tableNumberSelect.classList.remove('bg-gray-100', 'cursor-not-allowed');
        }
    }
}
