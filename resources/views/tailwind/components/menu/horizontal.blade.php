{{-- Horizontal menu template with mobile toggle --}}
@if($items && $menu)
<nav class="menu-navigation menu-horizontal" 
     role="navigation" 
     aria-label="{{ $menu->name }}"
     x-data="horizontalMenu()"
     x-init="init()">
    
    {{-- Mobile menu button --}}
    <div class="md:hidden">
        <button type="button" 
                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500" 
                :aria-expanded="mobileOpen"
                :aria-controls="mobileMenuId"
                @click="toggleMobile()"
                @keydown.enter="toggleMobile()"
                @keydown.space.prevent="toggleMobile()">
            <span class="sr-only">{{ flexiblePagesTrans('menu.mobile_toggle_label') }}</span>
            {{-- Hamburger icon --}}
            <svg x-show="!mobileOpen" class="block h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
            {{-- Close icon --}}
            <svg x-show="mobileOpen" class="block h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- Desktop menu --}}
    <ul class="hidden md:flex md:space-x-6" role="menubar">
        @foreach($items as $item)
            <li role="none">
                <x-flexible-pages-menu-item :item="$item" :style="$style" />
            </li>
        @endforeach
    </ul>

    {{-- Mobile menu --}}
    <div x-show="mobileOpen"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="md:hidden absolute top-full left-0 right-0 z-50 bg-white shadow-lg border-t"
         :id="mobileMenuId"
         @click.away="closeMobile()"
         @keydown.escape="closeMobile()">
        <ul class="px-2 pt-2 pb-3 space-y-1" role="menu">
            @foreach($items as $item)
                <li role="none">
                    <x-flexible-pages-menu-item :item="$item" :style="'mobile'" />
                </li>
            @endforeach
        </ul>
    </div>
</nav>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('horizontalMenu', () => ({
        mobileOpen: false,
        mobileMenuId: 'mobile-menu-' + Math.random().toString(36).substr(2, 9),
        
        init() {
            // Close mobile menu on window resize to desktop
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 768) { // md breakpoint
                    this.mobileOpen = false;
                }
            });
        },
        
        toggleMobile() {
            this.mobileOpen = !this.mobileOpen;
        },
        
        closeMobile() {
            this.mobileOpen = false;
        }
    }));
});
</script>
@endif