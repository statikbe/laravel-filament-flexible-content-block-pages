{{-- Dropdown menu template with Alpine.js --}}
@if($items && $items->isNotEmpty() && $menu)
<div class="menu-navigation menu-dropdown relative inline-block text-left" 
     x-data="dropdownMenu()" 
     x-init="init()"
     @click.away="close()"
     @keydown.escape="close()">
    <div>
        <button type="button" 
                class="inline-flex w-full justify-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2" 
                :id="buttonId"
                :aria-expanded="isOpen"
                :aria-controls="menuId"
                aria-haspopup="true"
                @click="toggle()"
                @keydown.enter="toggle()"
                @keydown.space.prevent="toggle()"
                @keydown.arrow-down.prevent="openAndFocusFirst()"
                @keydown.arrow-up.prevent="openAndFocusLast()">
            {{ $menu->name }}
            <svg class="-mr-1 h-5 w-5 text-gray-400 transition-transform" 
                 :class="{ 'rotate-180': isOpen }"
                 viewBox="0 0 20 20" 
                 fill="currentColor" 
                 aria-hidden="true">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
            </svg>
        </button>
    </div>

    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         class="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" 
         role="menu" 
         aria-orientation="vertical" 
         :aria-labelledby="buttonId"
         :id="menuId"
         @keydown.arrow-down.prevent="focusNext()"
         @keydown.arrow-up.prevent="focusPrevious()"
         @keydown.escape="closeAndFocusButton()"
         @keydown.tab="close()">
        <div class="py-1" role="none">
            @foreach($items as $item)
                <x-flexible-pages-menu-item :item="$item" :style="$style" :locale="$locale" />
            @endforeach
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('dropdownMenu', () => ({
        isOpen: false,
        buttonId: 'menu-button-' + Math.random().toString(36).substr(2, 9),
        menuId: 'menu-' + Math.random().toString(36).substr(2, 9),
        
        init() {
            // Initialize any needed setup
        },
        
        toggle() {
            this.isOpen ? this.close() : this.open();
        },
        
        open() {
            this.isOpen = true;
        },
        
        close() {
            this.isOpen = false;
        },
        
        openAndFocusFirst() {
            this.open();
            this.$nextTick(() => {
                this.focusFirstItem();
            });
        },
        
        openAndFocusLast() {
            this.open();
            this.$nextTick(() => {
                this.focusLastItem();
            });
        },
        
        closeAndFocusButton() {
            this.close();
            this.$nextTick(() => {
                this.$refs.button?.focus();
            });
        },
        
        focusFirstItem() {
            const items = this.getMenuItems();
            if (items.length > 0) {
                items[0].focus();
            }
        },
        
        focusLastItem() {
            const items = this.getMenuItems();
            if (items.length > 0) {
                items[items.length - 1].focus();
            }
        },
        
        focusNext() {
            const items = this.getMenuItems();
            const currentIndex = items.indexOf(document.activeElement);
            const nextIndex = currentIndex < items.length - 1 ? currentIndex + 1 : 0;
            items[nextIndex].focus();
        },
        
        focusPrevious() {
            const items = this.getMenuItems();
            const currentIndex = items.indexOf(document.activeElement);
            const previousIndex = currentIndex > 0 ? currentIndex - 1 : items.length - 1;
            items[previousIndex].focus();
        },
        
        getMenuItems() {
            return Array.from(this.$el.querySelectorAll('[role="menuitem"]'));
        }
    }));
});
</script>
@endif