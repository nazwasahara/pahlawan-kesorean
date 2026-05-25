@php
    $user = auth()->user();
    $logs = collect();
    if ($user) {
        if ($user->role === 'owner') {
            // Owner sees Admin, Kasir, and System activity
            $logs = \App\Models\ActivityLog::where('user_identity', '!=', 'Owner')
                ->latest()
                ->take(5)
                ->get();
        } else {
            // Admin sees Kasir and System activity
            $logs = \App\Models\ActivityLog::whereNotIn('user_identity', ['Admin', 'Owner'])
                ->latest()
                ->take(5)
                ->get();
        }
    }
@endphp

<nav class="bg-white border-b border-gray-250/60 shadow-sm relative z-30">
    <div class="flex justify-between items-center h-16 px-6 md:px-8">
        <!-- Left Section: Logo & Page Title -->
        <div class="flex items-center gap-4">
            <!-- Mobile sidebar toggle -->
            <button id="sidebarToggle" class="md:hidden p-2 rounded-lg text-gray-700 hover:bg-gray-100 transition shrink-0">
                <i class="ri-menu-line text-xl"></i>
            </button>
            <img src="/images/logo.png" alt="Logo" class="h-10 md:h-12 shrink-0">
            <span class="hidden sm:inline-block text-sm md:text-base font-extrabold text-gray-900 tracking-wider uppercase">
                @yield('navbar-title', 'DASHBOARD ' . ($user ? $user->role : ''))
            </span>
        </div>

        <!-- Right Section: Notification & User Dropdown -->
        <div class="flex items-center gap-4 sm:gap-6 shrink-0">
            <!-- Notification Icon Container (Hover Trigger) -->
            <div class="relative group py-2">
                <button class="relative p-2 text-gray-650 hover:text-gray-900 transition focus:outline-none cursor-pointer">
                    <i class="ri-notification-3-line text-2xl"></i>
                    @if($logs->count() > 0)
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 {{ $user && $user->role === 'owner' ? 'bg-amber-500' : 'bg-emerald-500' }} rounded-full"></span>
                    @endif
                </button>

                <!-- Dropdown Popover (Shows on Hover) -->
                <div class="absolute right-0 mt-1 w-80 bg-white border border-gray-200 rounded-xl shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-350 transform translate-y-2 group-hover:translate-y-0 z-50 pointer-events-none group-hover:pointer-events-auto">
                    <!-- Popover Header -->
                    <div class="px-4 py-3 border-b border-gray-150 {{ $user && $user->role === 'owner' ? 'bg-amber-50/70 border-amber-100' : 'bg-emerald-50/70 border-emerald-100' }} rounded-t-xl">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-extrabold {{ $user && $user->role === 'owner' ? 'text-amber-800' : 'text-emerald-800' }} uppercase tracking-wider">
                                {{ $user && $user->role === 'owner' ? 'Notifikasi Owner' : 'Notifikasi Admin' }}
                            </span>
                            <span class="text-[9px] font-bold text-gray-400">
                                5 Log Terbaru
                            </span>
                        </div>
                        <h4 class="text-[10px] text-gray-500 font-bold mt-0.5">
                            {{ $user && $user->role === 'owner' ? 'Memantau aktivitas Admin & Kasir' : 'Memantau aktivitas Kasir & Sistem' }}
                        </h4>
                    </div>

                    <!-- Popover Content -->
                    <div class="divide-y divide-gray-100 max-h-[320px] overflow-y-auto rounded-b-3xl">
                        @forelse($logs as $log)
                            <div class="px-4 py-3 hover:bg-gray-50/60 transition text-left">
                                <div class="flex justify-between items-start gap-2">
                                    <span class="text-[9px] font-black px-2 py-0.5 rounded-full {{ $log->user_identity === 'Admin' ? 'bg-blue-50 text-blue-700 border border-blue-100' : ($log->user_identity === 'Kasir' ? 'bg-purple-50 text-purple-700 border border-purple-100' : 'bg-gray-55 text-gray-700 border border-gray-250') }}">
                                        {{ $log->user_identity }}
                                    </span>
                                    <span class="text-[9px] font-bold text-gray-400 whitespace-nowrap">
                                        {{ $log->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <p class="text-[11px] font-extrabold text-gray-800 mt-1">
                                    {{ $log->action }}
                                </p>
                                <p class="text-[10px] text-gray-550 font-bold mt-0.5 leading-relaxed">
                                    {{ $log->description }}
                                </p>
                            </div>
                        @empty
                            <div class="px-4 py-8 text-center text-gray-400 text-xs font-bold">
                                <i class="ri-notification-off-line text-lg mb-1 block"></i>
                                Belum ada aktivitas baru.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- User Dropdown -->
            <div class="flex items-center gap-2.5 rounded-xl px-3.5 py-1.5 cursor-pointer transition shrink-0">
                <div class="w-7 h-7 rounded-full bg-[#125E34] text-white flex items-center justify-center font-bold text-xs uppercase shadow-inner">
                    {{ $user ? strtoupper(substr($user->name, 0, 1)) : '' }}
                </div>
                <span class="text-sm font-extrabold text-gray-900 capitalize">
                    {{ $user ? $user->role : '' }}
                </span>
            </div>
        </div>
    </div>
</nav>
<div class="pattern-green w-full h-8"></div>
