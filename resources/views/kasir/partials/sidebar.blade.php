<!-- Hamburger Button for Mobile -->
<button id="sidebarToggle" class="md:hidden fixed top-20 left-4 z-40 p-2 rounded-lg bg-green-700 text-white hover:bg-green-800 transition print:hidden">
    <i class="ri-menu-line text-xl"></i>
</button>

<!-- Sidebar -->
<aside id="sidebar" class="fixed md:relative md:flex w-56 lg:w-56 md:w-20 min-h-full bg-[#f8f6f2] flex-col justify-between py-4 md:py-3 px-3 md:px-2 border-r border-gray-200 z-50 md:z-auto left-0 top-0 md:top-auto hidden md:flex h-screen md:h-auto overflow-y-auto md:overflow-y-visible transition-all">
    <!-- Close button for mobile -->
    <button id="sidebarClose" class="md:hidden absolute top-4 right-4 text-2xl text-gray-600 hover:text-black">
        <i class="ri-close-line"></i>
    </button>

    <div class="mt-8 md:mt-0">
        <nav class="flex flex-col gap-2 md:gap-1">
            <a href="{{ route('kasir.dashboard') }}" class="flex items-center gap-3 md:justify-center lg:justify-start lg:px-4 md:px-2 py-2 md:py-2 rounded-xl font-semibold {{ request()->routeIs('kasir.dashboard') ? 'text-white bg-[#355b34] hover:bg-[#2d4a2a]' : 'text-black hover:bg-[#e6e6e6]' }} transition group relative">
                <i class="ri-computer-line text-lg md:text-xl"></i> <span class="block md:hidden lg:inline text-sm">POS</span>
            </a>
            <a href="{{ route('kasir.orders') }}" class="flex items-center gap-3 md:justify-center lg:justify-start lg:px-4 md:px-2 py-2 md:py-2 rounded-xl font-semibold {{ request()->routeIs('kasir.orders*') ? 'text-white bg-[#355b34] hover:bg-[#2d4a2a]' : 'text-black hover:bg-[#e6e6e6]' }} transition group relative">
                <i class="ri-clipboard-line text-lg md:text-xl"></i> <span class="block md:hidden lg:inline text-sm">Order</span>
            </a>
            <a href="{{ route('kasir.shift') }}" class="flex items-center gap-3 md:justify-center lg:justify-start lg:px-4 md:px-2 py-2 md:py-2 rounded-xl font-semibold {{ request()->routeIs('kasir.shift') ? 'text-white bg-[#355b34] hover:bg-[#2d4a2a]' : 'text-black hover:bg-[#e6e6e6]' }} transition group relative">
                <i class="ri-time-line text-lg md:text-xl"></i> <span class="block md:hidden lg:inline text-sm">Shift</span>
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 md:justify-center lg:justify-start lg:px-4 md:px-2 py-2 md:py-2 rounded-xl font-semibold text-black hover:bg-[#e6e6e6] text-left transition group relative">
                    <i class="ri-logout-box-r-line text-lg md:text-xl"></i> <span class="block md:hidden lg:inline text-sm">Logout</span>
                </button>
            </form>
        </nav>
    </div>
</aside>

<!-- Sidebar overlay for mobile -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 md:hidden hidden z-40" onclick="closeSidebar()"></div>

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

    document.getElementById('sidebarToggle')?.addEventListener('click', openSidebar);
    document.getElementById('sidebarClose')?.addEventListener('click', closeSidebar);
</script>
