<!-- Sidebar overlay for mobile -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black/50 md:hidden hidden z-40" onclick="closeSidebar()"></div>

<aside id="sidebar" class="fixed md:relative w-64 lg:w-64 md:w-20 h-screen md:h-full bg-[#125E34] text-white flex flex-col p-4 justify-between overflow-y-auto transition-all z-50 md:z-25 left-0 top-0 hidden md:flex shadow-lg shrink-0">
    <!-- Close button for mobile -->
    <button id="sidebarClose" class="md:hidden absolute top-4 right-4 text-2xl text-white hover:text-gray-200 transition" onclick="closeSidebar()">
        <i class="ri-close-line"></i>
    </button>

    <div class="space-y-1 mt-8 md:mt-0">
        <!-- Dashboard Link -->
        <a href="{{ route('admin-owner.dashboard') }}" 
           class="flex items-center gap-3 md:justify-center lg:justify-start px-4 py-3 rounded-xl transition-all duration-200 {{ Route::is('admin-owner.dashboard') ? 'bg-[#F4F3EB] text-[#125E34] font-extrabold shadow' : 'text-white/90 hover:bg-white/10 hover:text-white' }}"
           title="Dashboard">
            <i class="ri-home-4-fill text-lg"></i>
            <span class="text-xs font-bold tracking-wide block md:hidden lg:block">Dashboard</span>
        </a>

        <!-- Manajemen Pengguna Link -->
        <a href="{{ route('admin-owner.users.index') }}" 
           class="flex items-center gap-3 md:justify-center lg:justify-start px-4 py-3 rounded-xl transition-all duration-200 {{ Route::is('admin-owner.users.*') ? 'bg-[#F4F3EB] text-[#125E34] font-extrabold shadow' : 'text-white/90 hover:bg-white/10 hover:text-white' }}"
           title="Manajemen Pengguna">
            <i class="ri-user-fill text-lg"></i>
            <span class="text-xs font-bold tracking-wide block md:hidden lg:block">Manajemen Pengguna</span>
        </a>

        <!-- Manajemen Menu Dropdown -->
        <div class="space-y-1">
            <button onclick="toggleMenuDropdown()" 
                    class="w-full flex items-center justify-between gap-3 md:justify-center lg:justify-between px-4 py-3 rounded-xl transition-all duration-200 {{ (Route::is('admin-owner.menus.*') || Route::is('admin-owner.categories.*') || Route::is('admin-owner.promos.*')) ? 'bg-white/10 text-white font-extrabold' : 'text-white/90 hover:bg-white/10 hover:text-white' }}"
                    title="Manajemen Menu">
                <span class="flex items-center gap-3">
                    <i class="ri-restaurant-2-fill text-lg"></i>
                    <span class="text-xs font-bold tracking-wide block md:hidden lg:block">Manajemen Menu</span>
                </span>
                <i id="menuDropdownArrow" class="ri-arrow-down-s-line text-sm transition-transform duration-200 block md:hidden lg:block {{ (Route::is('admin-owner.menus.*') || Route::is('admin-owner.categories.*') || Route::is('admin-owner.promos.*')) ? 'rotate-180' : '' }}"></i>
            </button>
            
            <div id="menuDropdownContainer" 
                 class="pl-4 lg:pl-6 space-y-1 md:hidden {{ (Route::is('admin-owner.menus.*') || Route::is('admin-owner.categories.*') || Route::is('admin-owner.promos.*')) ? 'block lg:block' : 'hidden' }}">
                <!-- Data Menu Link -->
                <a href="{{ route('admin-owner.menus.index') }}" 
                   class="flex items-center gap-2 px-3 py-2 rounded-lg text-[11px] font-bold transition-all duration-150 {{ Route::is('admin-owner.menus.index') ? 'bg-[#F4F3EB] text-[#125E34]' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <i class="ri-menu-line"></i>
                    <span>Data Menu</span>
                </a>
                <!-- Data Kategori Menu Link -->
                <a href="{{ route('admin-owner.categories.index') }}" 
                   class="flex items-center gap-2 px-3 py-2 rounded-lg text-[11px] font-bold transition-all duration-150 {{ Route::is('admin-owner.categories.index') ? 'bg-[#F4F3EB] text-[#125E34]' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <i class="ri-menu-line"></i>
                    <span>Data Kategori Menu</span>
                </a>
                <!-- Data Promo Link -->
                <a href="{{ route('admin-owner.promos.index') }}" 
                   class="flex items-center gap-2 px-3 py-2 rounded-lg text-[11px] font-bold transition-all duration-150 {{ Route::is('admin-owner.promos.*') ? 'bg-[#F4F3EB] text-[#125E34]' : 'text-white/80 hover:bg-white/10 hover:text-white' }}">
                    <i class="ri-coupon-line"></i>
                    <span>Data Promo</span>
                </a>
            </div>
        </div>

        <!-- Manajemen Transaksi Link -->
        <a href="{{ route('admin-owner.transactions.index') }}" 
           class="flex items-center gap-3 md:justify-center lg:justify-start px-4 py-3 rounded-xl transition-all duration-200 {{ Route::is('admin-owner.transactions.*') ? 'bg-[#F4F3EB] text-[#125E34] font-extrabold shadow' : 'text-white/90 hover:bg-white/10 hover:text-white' }}"
           title="Manajemen Transaksi">
            <i class="ri-file-list-3-fill text-lg"></i>
            <span class="text-xs font-bold tracking-wide block md:hidden lg:block">Manajemen Transaksi</span>
        </a>

        <!-- Manajemen Pengeluaran Link -->
        <a href="{{ route('admin-owner.expenses.index') }}" 
           class="flex items-center gap-3 md:justify-center lg:justify-start px-4 py-3 rounded-xl transition-all duration-200 {{ Route::is('admin-owner.expenses.*') ? 'bg-[#F4F3EB] text-[#125E34] font-extrabold shadow' : 'text-white/90 hover:bg-white/10 hover:text-white' }}"
           title="Manajemen Pengeluaran">
            <i class="ri-wallet-3-fill text-lg"></i>
            <span class="text-xs font-bold tracking-wide block md:hidden lg:block">Manajemen Pengeluaran</span>
        </a>

        <!-- Pusat Laporan Link -->
        @if(auth()->user()->role === 'owner')
            <a href="{{ route('admin-owner.reports.index') }}" 
               class="flex items-center gap-3 md:justify-center lg:justify-start px-4 py-3 rounded-xl transition-all duration-200 {{ Route::is('admin-owner.reports.*') ? 'bg-[#F4F3EB] text-[#125E34] font-extrabold shadow' : 'text-white/90 hover:bg-white/10 hover:text-white' }}"
               title="Pusat Laporan">
                <i class="ri-file-chart-fill text-lg"></i>
                <span class="text-xs font-bold tracking-wide block md:hidden lg:block">Pusat Laporan</span>
            </a>
        @endif

        <!-- Log Aktivitas Link -->
        @if(auth()->user()->role === 'owner')
            <a href="{{ route('admin-owner.logs.index') }}" 
               class="flex items-center gap-3 md:justify-center lg:justify-start px-4 py-3 rounded-xl transition-all duration-200 {{ Route::is('admin-owner.logs.*') ? 'bg-[#F4F3EB] text-[#125E34] font-extrabold shadow' : 'text-white/90 hover:bg-white/10 hover:text-white' }}"
               title="Log Aktivitas">
                <i class="ri-history-fill text-lg"></i>
                <span class="text-xs font-bold tracking-wide block md:hidden lg:block">Log Aktivitas</span>
            </a>
        @endif
    </div>

    <!-- Bottom Logout Button -->
    <div class="pt-4 border-t border-white/10">
        <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" 
           class="flex items-center gap-3 md:justify-center lg:justify-start px-4 py-3 rounded-xl bg-red-100/90 text-red-700 hover:bg-red-200 transition-all duration-150 font-extrabold shadow-sm"
           title="Logout">
            <i class="ri-logout-box-r-fill text-lg"></i>
            <span class="text-xs font-bold tracking-wide block md:hidden lg:block">Logout</span>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
            @csrf
        </form>
    </div>
</aside>

<script>
    function openSidebar() {
        document.getElementById('sidebar').classList.remove('hidden');
        document.getElementById('sidebarOverlay').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        document.getElementById('sidebar').classList.add('hidden');
        document.getElementById('sidebarOverlay').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function toggleMenuDropdown() {
        // If sidebar is collapsed on md screens (icon only), direct click navigates to menus index
        if (window.innerWidth >= 768 && window.innerWidth < 1024) {
            window.location.href = "{{ route('admin-owner.menus.index') }}";
            return;
        }

        const container = document.getElementById('menuDropdownContainer');
        const arrow = document.getElementById('menuDropdownArrow');

        if (container.classList.contains('hidden')) {
            container.classList.remove('hidden');
            container.classList.add('block', 'lg:block');
            arrow.classList.add('rotate-180');
        } else {
            container.classList.add('hidden');
            container.classList.remove('block', 'lg:block');
            arrow.classList.remove('rotate-180');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('sidebarToggle')?.addEventListener('click', openSidebar);
    });
</script>
