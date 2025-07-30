<x-filament-panels::page>
    <div
        x-data="menuItemsManager({
            maxDepth: {{ $this->getMaxDepth() }},
            menuId: {{ $this->record->id }}
        })"
        x-init="init()"
        class="space-y-6"
    >
        <!-- Tree Container -->
        <x-filament::section>
            <div class="min-h-[400px]">
                @if($this->record->menuItems()->count() === 0)
                    <!-- Empty State -->
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">
                            {{ flexiblePagesTrans('menu_items.tree.empty_state') }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            {{ flexiblePagesTrans('menu_items.manage.empty_description') }}
                        </p>
                    </div>
                @else
                    <!-- Tree Items -->
                    <div class="space-y-2" id="menu-items-container" wire:key="tree-{{ $refreshKey }}">
                        @foreach($this->record->menuItems()->with(['children', 'linkable'])->whereNull('parent_id')->orderBy('_lft')->get() as $item)
                            @livewire('filament-flexible-content-block-pages::menu-tree-item', [
                                'item' => $item,
                                'maxDepth' => $this->getMaxDepth()
                            ], key("item-{$item->id}-{$refreshKey}"))
                        @endforeach
                    </div>
                @endif
            </div>
        </x-filament::section>

        <!-- Loading Overlay -->
        <div x-show="loading" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white dark:bg-gray-800 rounded-lg p-6 flex items-center space-x-4">
                <svg class="animate-spin h-6 w-6 text-primary-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-sm text-gray-700 dark:text-gray-300">
                    {{ flexiblePagesTrans('menu_items.manage.loading') }}
                </span>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        function menuItemsManager(config) {
            return {
                maxDepth: config.maxDepth || 2,
                menuId: config.menuId,
                loading: false,

                init() {
                    this.initSortable();
                    this.setupEventListeners();
                },

                setupEventListeners() {
                    // Listen for Livewire events to refresh menu items
                    this.$wire.on('menu-items-updated', () => {
                        this.refreshMenuItems();
                    });
                },

                refreshMenuItems() {
                    this.loading = true;
                    // Refresh the page component - Livewire will handle the re-render
                    this.$wire.$refresh().finally(() => {
                        this.loading = false;
                        // Re-initialize sortable after items are updated
                        this.$nextTick(() => {
                            this.initSortable();
                        });
                    });
                },

                initSortable() {
                    if (typeof Sortable !== 'undefined') {
                        const container = document.getElementById('menu-items-container');
                        if (container) {
                            Sortable.create(container, {
                                group: 'menu-items',
                                animation: 150,
                                handle: '.drag-handle',
                                onStart: (evt) => {
                                    // Store the original order and DOM state before drag
                                    this.originalOrder = Array.from(container.children).map(el => this.extractItemId(el));
                                    this.originalElements = Array.from(container.children);
                                },
                                onEnd: (evt) => {
                                    if (evt.oldIndex !== evt.newIndex) {
                                        // Calculate the new order based on the drag operation
                                        const newOrder = [...this.originalOrder];
                                        const movedItem = newOrder.splice(evt.oldIndex, 1)[0];
                                        newOrder.splice(evt.newIndex, 0, movedItem);
                                        
                                        // Immediately revert DOM to original state to preserve Livewire
                                        container.innerHTML = '';
                                        this.originalElements.forEach(el => container.appendChild(el));
                                        
                                        this.saveReorderFromOrder(newOrder);
                                    }
                                }
                            });
                        }
                    }
                },

                saveReorderFromOrder(newOrder) {
                    this.loading = true;
                    
                    // Build items array from the calculated new order
                    const items = newOrder.map((itemId, index) => {
                        return {
                            id: itemId,
                            position: index,
                            parent_id: null // Root level items for now
                        };
                    });

                    // Call the Livewire method to save the new order
                    this.$wire.reorderMenuItems(items);
                },

                extractItemId(element) {
                    const itemId = element.dataset.itemId;
                    return itemId ? parseInt(itemId) : null;
                },
            }
        }
    </script>
    @endpush
</x-filament-panels::page>
