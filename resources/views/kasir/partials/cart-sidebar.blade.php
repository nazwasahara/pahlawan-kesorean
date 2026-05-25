<div
    class="w-full md:w-72 lg:w-80 bg-white rounded-lg md:rounded-xl border border-gray-200 flex flex-col flex-shrink-0"
    style="height:auto;max-height:100%;"
>

    <div class="p-2 md:p-4 border-b border-gray-100 flex flex-col gap-2">

        <div>
            <label class="text-xs font-medium block mb-0.5 md:mb-1">
                Nama Pelanggan
            </label>

            <input
                type="text"
                id="customerName"
                placeholder="Nama pelanggan"
                class="w-full px-2.5 py-1 md:py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#355b34]"
            >
        </div>

        <div class="grid grid-cols-2 gap-2">
            <div>
                <label class="text-xs font-medium block mb-0.5 md:mb-1">
                    Tipe
                </label>
                <select
                    id="orderType"
                    onchange="toggleTableSelection()"
                    class="w-full px-2 py-1 md:py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#355b34] bg-white"
                >
                    <option value="dine_in">Dine In</option>
                    <option value="take_away">Take Away</option>
                </select>
            </div>

            <div>
                <label class="text-xs font-medium block mb-0.5 md:mb-1">
                    Meja
                </label>

                <select
                    id="tableNumber"
                    class="w-full px-2 py-1 md:py-1.5 text-xs border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-[#355b34] bg-white"
                >
                    <option value="">Pilih Meja</option>
                    @for ($i = 1; $i <= 30; $i++)
                        @php
                            $isOccupied = in_array($i, $occupiedTables ?? []);
                        @endphp
                        <option value="{{ $i }}" {{ $isOccupied ? 'disabled' : '' }} class="{{ $isOccupied ? 'text-red-500 bg-red-50' : 'text-gray-900' }}">
                            Meja {{ $i }} {{ $isOccupied ? '(Terpakai)' : '' }}
                        </option>
                    @endfor
                </select>
            </div>
        </div>

    </div>

    <div
        id="cartItems"
        class="flex-1 overflow-y-auto px-2 md:px-4 max-h-64 md:max-h-96"
    >

        <div
            id="cartEmpty"
            class="text-center py-10 text-gray-500"
        >
            <i class="ri-shopping-cart-line text-3xl mb-2 block"></i>

            <p class="text-xs">
                Keranjang kosong
            </p>

            <p class="text-xs mt-1 text-gray-400">
                Pilih menu untuk memulai
            </p>
        </div>

    </div>

    <!-- Checkout Summary View -->
    @include('kasir.partials.checkout-summary')

    <div
        id="cartFooter"
        style="display:none;"
        class="p-2 md:p-4 border-t border-gray-100"
    >

        <div class="flex justify-between text-xs md:text-sm font-semibold text-gray-800 mb-2 md:mb-3">
            <span>Total</span>
            <span id="cartTotal">Rp 0</span>
        </div>

        <button
            onclick="checkout()"
            class="w-full py-2 md:py-2.5 bg-green-800 hover:bg-green-900 text-white font-semibold text-xs md:text-sm rounded-lg transition"
        >
            Selesai
        </button>

    </div>

    <!-- Checkout Footer -->
    <div
        id="checkoutFooter"
        style="display:none;"
        class="p-2 md:p-4 border-t border-gray-100 flex gap-2"
    >

        <button
            onclick="backToCart()"
            class="flex-1 py-2 md:py-2.5 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold text-xs md:text-sm rounded-lg transition"
        >
            Kembali
        </button>

        <button
            onclick="selectPaymentMethod()"
            class="flex-1 py-2 md:py-2.5 bg-green-800 hover:bg-green-900 text-white font-semibold text-xs md:text-sm rounded-lg transition"
        >
            Pembayaran
        </button>

    </div>

</div>
