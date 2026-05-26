<div class="flex-1 min-w-0 flex flex-col gap-2 md:gap-3">

    <div class="relative">
        <span class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
            <i class="ri-search-line text-sm md:text-base"></i>
        </span>

        <input
            type="text"
            id="searchInput"
            placeholder="Cari produk..."
            oninput="filterMenu()"
            class="w-full pl-9 pr-4 py-1.5 md:py-2 text-xs md:text-sm border border-gray-200 rounded-lg bg-white focus:outline-none focus:ring-2 focus:ring-[#355b34] transition duration-150"
        >
    </div>

    <div class="category-scroll overflow-x-auto pb-2" id="categoryTabs">

        <button
            onclick="filterCategory(this, '')"
            class="active whitespace-nowrap">
            Semua
        </button>

        @foreach($categories as $cat)
            <button
                onclick="filterCategory(this, '{{ $cat->id }}')" class="whitespace-nowrap">
                {{ $cat->name }}
            </button>
        @endforeach

    </div>

    <div
        id="menuGrid"
        class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-2 md:gap-2 lg:gap-3"
        style="grid-template-rows:max-content;align-content:start;"
    >

        @forelse ($menus as $menu)
            @php
                $isHabis = !$menu->is_available || $menu->stock <= 0;
            @endphp

            <div
                class="menu-card bg-white rounded-lg md:rounded-xl border border-gray-200 p-2 md:p-3 flex flex-col items-center {{ $isHabis ? 'opacity-60 cursor-not-allowed select-none' : 'cursor-pointer' }}"
                data-id="{{ $menu->id }}"
                data-name="{{ strtolower($menu->name) }}"
                data-category="{{ $menu->category_id }}"
                data-stock="{{ $menu->stock }}"
                @if(!$isHabis)
                onclick="addToCart(
                    {{ $menu->id }},
                    '{{ addslashes($menu->name) }}',
                    {{ $menu->price }},
                    '{{ $menu->image ?? '' }}'
                )"
                @endif
            >

                <div class="w-16 h-16 md:w-20 md:h-20 rounded-lg mb-1 md:mb-2 overflow-hidden bg-gray-300 flex items-center justify-center">
                    @if($menu->image)
                        <img src="{{ Str::startsWith($menu->image, ['http://', 'https://']) ? $menu->image : asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="w-full h-full object-cover">
                    @else
                        <i class="ri-image-line text-2xl md:text-3xl text-gray-400"></i>
                    @endif
                </div>

                @if($isHabis)
                    <span class="sold-out-badge">Habis</span>
                @endif

                <h4 class="font-medium text-gray-900 text-center text-xs md:text-xs leading-tight line-clamp-2 w-full">
                    {{ $menu->name }}
                </h4>

                <div class="flex items-center justify-between w-full mt-1 md:mt-2">

                    <p class="text-green-700 font-bold text-xs md:text-xs">
                        Rp {{ number_format($menu->price, 0, ',', '.') }}
                    </p>

                    <button
                        type="button"
                        @if(!$isHabis)
                        onclick="event.stopPropagation(); addToCart(
                            {{ $menu->id }},
                            '{{ addslashes($menu->name) }}',
                            {{ $menu->price }},
                            '{{ $menu->image ?? '' }}'
                        )"
                        @endif
                        @if($isHabis) disabled @endif
                        class="rounded-full w-5 h-5 md:w-6 md:h-6 flex items-center justify-center flex-shrink-0 transition {{ $isHabis ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-green-700 hover:bg-green-800 text-white' }}"
                    >
                        <i class="ri-add-line text-xs md:text-sm"></i>
                    </button>

                </div>

            </div>

        @empty

            <div class="col-span-2 md:col-span-3 lg:col-span-4 text-center py-8 md:py-12 text-gray-400">
                <i class="ri-store-2-line text-4xl mb-2 block"></i>
                <p class="text-sm">Belum ada menu tersedia.</p>
            </div>

        @endforelse

    </div>

</div>
