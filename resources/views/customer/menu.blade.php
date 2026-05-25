@extends('customer.layouts.app')

@section('title', 'Menu Favorit - Pahlawan Kesorean')

@section('customer-content')
@php
    $iconMap = [
        'makanan-utama' => 'ri-restaurant-line',
        'minuman' => 'ri-cup-line',
        'snack' => 'ri-cookie-line',
        'dessert' => 'ri-cake-3-line'
    ];
@endphp

<div class="py-10">
    <!-- Search Bar -->
    <div class="relative w-full mb-5">
        <input 
            type="text" 
            placeholder="Cari menu favoritmu..." 
            oninput="filterSearch(this.value)"
            class="w-full bg-white text-neutral-800 placeholder-neutral-400 text-sm pl-5 pr-12 py-3 rounded-full border border-neutral-200 shadow-sm focus:outline-none focus:border-[#133122] focus:ring-1 focus:ring-[#133122]"
        >
        <div class="absolute right-4 top-1/2 -translate-y-1/2 text-neutral-400">
            <i class="ri-search-line text-lg"></i>
        </div>
    </div>

    <!-- Category Horizontal Scroll List -->
    <div class="flex flex-row flex-nowrap gap-2 overflow-x-auto pb-3 mb-4 custom-scroll select-none shrink-0 -mx-4 px-4">
        <!-- Semua Pill -->
        <button 
            data-slug="all" 
            onclick="filterCategory('all', this)" 
            class="category-pill shrink-0 bg-[#133122] text-[#F4F3EB] border border-[#133122] rounded-full py-2 px-4 flex items-center gap-1.5 whitespace-nowrap text-xs font-semibold shadow-sm transition duration-200 cursor-pointer"
        >
            <i class="ri-list-check-3 text-sm"></i>
            <span>Semua</span>
        </button>

        @foreach($categories as $cat)
            @php
                $icon = $iconMap[$cat->slug] ?? 'ri-restaurant-line';
            @endphp
            <button 
                data-slug="{{ $cat->slug }}" 
                onclick="filterCategory('{{ $cat->slug }}', this)" 
                class="category-pill shrink-0 bg-white text-[#133122] border border-neutral-200 rounded-full py-2 px-4 flex items-center gap-1.5 whitespace-nowrap text-xs font-semibold shadow-sm transition duration-200 hover:bg-neutral-50 cursor-pointer"
            >
                <i class="{{ $icon }} text-sm"></i>
                <span>{{ $cat->name }}</span>
            </button>
        @endforeach
    </div>

    <!-- Menu Items Grouped By Category -->
    <div id="menu-container" class="space-y-6">
        @foreach($categories as $cat)
            @if($cat->menus->count() > 0)
                <div class="category-section" data-category="{{ $cat->slug }}">
                    <!-- Section Header -->
                    <div class="flex items-center justify-between mb-3 px-1">
                        <h3 class="font-extrabold text-[#133122] text-xs tracking-wider uppercase">{{ $cat->name }}</h3>
                        <button onclick="filterCategory('{{ $cat->slug }}', null)" class="text-[10px] text-emerald-800 font-bold flex items-center gap-0.5 hover:text-emerald-950">
                            Lihat Semua <i class="ri-arrow-right-s-line text-xs"></i>
                        </button>
                    </div>

                    <!-- Product Grid -->
                    <div class="grid grid-cols-2 gap-2.5">
                        @foreach($cat->menus as $menu)
                            <div 
                                class="menu-card relative bg-[#F7EED5]/70 rounded-2xl flex flex-row items-center border border-neutral-200/20 shadow-sm p-1.5 min-h-[96px] cursor-pointer hover:shadow transition duration-200" 
                                data-name="{{ strtolower($menu->name) }}"
                            >
                                <!-- Left: Rounded White Container for Image Placeholder -->
                                <div class="w-16 h-16 bg-white rounded-xl flex items-center justify-center shrink-0 border border-neutral-100/50 shadow-inner overflow-hidden">
                                    @if($menu->image)
                                        <img src="{{ Str::startsWith($menu->image, ['http://', 'https://']) ? $menu->image : asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="w-full h-full object-cover">
                                    @else
                                        <i class="ri-image-line text-xl text-neutral-300"></i>
                                    @endif
                                </div>

                                <!-- Right: Details -->
                                <div class="flex-grow pl-2 pr-6 flex flex-col justify-center min-w-0">
                                    <h4 class="font-extrabold text-[#133122] text-[10px] sm:text-xs leading-tight whitespace-normal mb-0.5">
                                        {{ $menu->name }}
                                    </h4>
                                    <p class="text-[#133122]/90 font-bold text-[10px] sm:text-xs">
                                        Rp {{ number_format($menu->price, 0, ',', '.') }}
                                    </p>
                                </div>

                                <!-- Add Button -->
                                <button onclick="event.stopPropagation(); addToCart({{ $menu->id }})" class="absolute bottom-1.5 right-1.5 w-6 h-6 bg-[#133122] hover:bg-[#1c4430] text-white rounded-full flex items-center justify-center shadow transition-all duration-200 hover:scale-105 active:scale-95 cursor-pointer">
                                    <i class="ri-add-line text-base font-bold"></i>
                                </button>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <!-- Empty Search State -->
    <div id="empty-search" class="hidden bg-white rounded-3xl p-8 shadow-sm border border-neutral-200/50 text-center flex flex-col items-center justify-center my-6">
        <div class="bg-neutral-100 p-4 rounded-full text-neutral-400 mb-3">
            <i class="ri-search-line text-3xl"></i>
        </div>
        <h3 class="font-extrabold text-neutral-800 text-sm">Menu Tidak Ditemukan</h3>
        <p class="text-[11px] text-neutral-500 mt-1 max-w-[200px] leading-relaxed">
            Maaf, kami tidak menemukan menu yang sesuai dengan kata kunci pencarianmu.
        </p>
    </div>
</div>

<script>
    // Live Search Filter
    function filterSearch(query) {
        query = query.toLowerCase().trim();
        const cards = document.querySelectorAll('.menu-card');
        const sections = document.querySelectorAll('.category-section');
        const emptySearch = document.getElementById('empty-search');
        
        let totalVisibleCards = 0;
 
        cards.forEach(card => {
            const name = card.getAttribute('data-name') || '';
            if (name.includes(query)) {
                card.setAttribute('data-search-visible', 'true');
                totalVisibleCards++;
            } else {
                card.setAttribute('data-search-visible', 'false');
            }
        });

        // Respect the active category filter when displaying matching sections
        const activePill = document.querySelector('.category-pill[data-slug]');
        const activeCategory = activePill ? activePill.getAttribute('data-slug') : 'all';

        sections.forEach(section => {
            const sectionCategory = section.getAttribute('data-category');
            const sectionCards = section.querySelectorAll('.menu-card');
            let visibleInThisSection = 0;

            sectionCards.forEach(card => {
                const searchVisible = card.getAttribute('data-search-visible') === 'true';
                const categoryVisible = (activeCategory === 'all' || sectionCategory === activeCategory);

                if (searchVisible && categoryVisible) {
                    card.style.display = 'flex';
                    visibleInThisSection++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (visibleInThisSection > 0) {
                section.style.display = 'block';
            } else {
                section.style.display = 'none';
            }
        });

        // Toggle Empty Search State
        if (totalVisibleCards === 0 && query !== '') {
            emptySearch.style.display = 'flex';
        } else {
            emptySearch.style.display = 'none';
        }
    }

    // Category Filter
    function filterCategory(slug, buttonElement) {
        // If button is clicked directly, update styling
        if (buttonElement) {
            document.querySelectorAll('.category-pill').forEach(btn => {
                btn.className = 'category-pill shrink-0 bg-white text-[#133122] border border-neutral-200 rounded-full py-2 px-4 flex items-center gap-1.5 whitespace-nowrap text-xs font-semibold shadow-sm transition duration-200 hover:bg-neutral-50 cursor-pointer';
                const icon = btn.querySelector('i');
                if (icon) {
                    icon.className = icon.className.replace('-fill', '-line');
                }
            });

            buttonElement.className = 'category-pill shrink-0 bg-[#133122] text-[#F4F3EB] border border-[#133122] rounded-full py-2 px-4 flex items-center gap-1.5 whitespace-nowrap text-xs font-semibold shadow-sm transition duration-200 cursor-pointer';
            const activeIcon = buttonElement.querySelector('i');
            if (activeIcon) {
                activeIcon.className = activeIcon.className.replace('-line', '-fill');
            }
        } else {
            // Find pill by slug and select it
            const matchedPill = document.querySelector(`.category-pill[data-slug="${slug}"]`);
            if (matchedPill) {
                filterCategory(slug, matchedPill);
            }
            return;
        }

        const sections = document.querySelectorAll('.category-section');
        const searchQuery = document.querySelector('input[type="text"]').value.toLowerCase().trim();
        let totalVisible = 0;

        sections.forEach(section => {
            const sectionCategory = section.getAttribute('data-category');
            const categoryMatch = (slug === 'all' || sectionCategory === slug);
            
            const cards = section.querySelectorAll('.menu-card');
            let visibleInThisSection = 0;

            cards.forEach(card => {
                const name = card.getAttribute('data-name') || '';
                const searchMatch = searchQuery === '' || name.includes(searchQuery);

                if (categoryMatch && searchMatch) {
                    card.style.display = 'flex';
                    visibleInThisSection++;
                    totalVisible++;
                } else {
                    card.style.display = 'none';
                }
            });

            if (visibleInThisSection > 0) {
                section.style.display = 'block';
            } else {
                section.style.display = 'none';
            }
        });

        // Hide Empty state if visible cards exist
        const emptySearch = document.getElementById('empty-search');
        if (totalVisible === 0 && searchQuery !== '') {
            emptySearch.style.display = 'flex';
        } else {
            emptySearch.style.display = 'none';
        }
    }

    function addToCart(menuId) {
        fetch('{{ route("customer.cart.add") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                menu_id: menuId,
                quantity: 1
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update cart count badge in topbar if it exists
                const badge = document.querySelector('.topbar-cart-badge');
                if (badge) {
                    badge.textContent = data.cart_count;
                    badge.style.display = 'flex';
                }
                
                // Show custom added success modal
                document.getElementById('add-success-modal').classList.remove('hidden');
            } else {
                alert(data.message || 'Gagal menambahkan ke keranjang');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan saat menambahkan ke keranjang');
        });
    }

    function closeSuccessModal() {
        document.getElementById('add-success-modal').classList.add('hidden');
    }
</script>
@endsection
