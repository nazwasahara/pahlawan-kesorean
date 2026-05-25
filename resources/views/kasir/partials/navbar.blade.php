<nav class="bg-[#FBF9F8] shadow-sm">
    <div class="flex justify-between items-center h-14 md:h-16 px-3 md:px-6">
        <div class="flex items-center gap-2 md:gap-3">
            <img src="/images/logo.png" alt="Logo" class="h-8 md:h-20">

        </div>
        <div class="flex items-center gap-2 md:gap-3">
            @auth
            <span class="text-xs md:text-sm lg:text-base font-semibold text-green-900 truncate">
                {{ auth()->user()->name }}
                </span>
            @endauth
        </div>
    </div>
</nav>
<div class="pattern-green w-full h-8 md:h-10"></div>
