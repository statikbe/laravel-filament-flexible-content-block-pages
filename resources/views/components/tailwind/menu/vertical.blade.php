{{-- Vertical menu template with collapsible submenus --}}
@if($items && $menu)
<nav class="menu-navigation menu-vertical" 
     role="navigation" 
     aria-label="{{ $menu->name }}"
     x-data="verticalMenu()"
     x-init="init()">
    <ul class="space-y-1" role="menubar" aria-orientation="vertical">
        @foreach($items as $item)
            <li role="none">
                <x-flexible-pages-menu-item :item="$item" :style="$style" />
            </li>
        @endforeach
    </ul>
</nav>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('verticalMenu', () => ({
        expandedItems: [],
        
        init() {
            // Auto-expand parent items that contain current page
            this.autoExpandCurrentPath();
        },
        
        toggleSubmenu(itemId) {
            const index = this.expandedItems.indexOf(itemId);
            if (index > -1) {
                this.expandedItems.splice(index, 1);
            } else {
                this.expandedItems.push(itemId);
            }
        },
        
        isExpanded(itemId) {
            return this.expandedItems.includes(itemId);
        },
        
        autoExpandCurrentPath() {
            // Find all menu items with current or active state
            const currentItems = this.$el.querySelectorAll('[aria-current="page"], .is-active');
            currentItems.forEach(item => {
                // Find parent menu items and expand them
                let parent = item.closest('li[data-has-children="true"]');
                while (parent) {
                    const itemId = parent.dataset.itemId;
                    if (itemId && !this.expandedItems.includes(itemId)) {
                        this.expandedItems.push(itemId);
                    }
                    parent = parent.parentElement.closest('li[data-has-children="true"]');
                }
            });
        }
    }));
});
</script>
@endif