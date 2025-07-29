<x-filament-panels::page>
    <div 
        x-data="menuItemsManager({
            maxDepth: {{ $this->getMaxDepth() }},
            menuId: {{ $this->record->id }},
            items: @js($this->getMenuItems())
        })"
        x-init="init()"
        class="space-y-6"
    >
        <!-- Tree Container -->
        <x-filament::section>
            <div class="min-h-[400px]">
                <!-- Empty State -->
                <div x-show="items.length === 0" class="text-center py-12">
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

                <!-- Tree Items -->
                <div x-show="items.length > 0" class="space-y-2" id="menu-items-container">
                    <template x-for="(item, index) in items" :key="item.id">
                        <div class="menu-tree-item" x-bind:data-item-id="item.id">
                            <div x-html="renderTreeItem(item, 0)"></div>
                        </div>
                    </template>
                </div>
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
                items: config.items || [],
                maxDepth: config.maxDepth || 2,
                menuId: config.menuId,
                loading: false,

                init() {
                    this.initSortable();
                },

                initSortable() {
                    if (typeof Sortable !== 'undefined') {
                        const container = document.getElementById('menu-items-container');
                        if (container) {
                            Sortable.create(container, {
                                group: 'menu-items',
                                animation: 150,
                                handle: '.drag-handle',
                                onEnd: (evt) => {
                                    if (evt.oldIndex !== evt.newIndex) {
                                        this.reorderItems(evt.oldIndex, evt.newIndex);
                                    }
                                }
                            });
                        }
                    }
                },

                renderTreeItem(item, depth) {
                    const canHaveChildren = depth < this.maxDepth;
                    const hasChildren = item.children && item.children.length > 0;
                    const indentClass = depth > 0 ? `ml-${depth * 8}` : '';
                    
                    return `
                        <div class="flex items-center p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 group hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors ${indentClass}">
                            <!-- Drag Handle -->
                            <div class="drag-handle cursor-move text-gray-400 hover:text-gray-600 mr-4 opacity-0 group-hover:opacity-100 transition-opacity">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                                </svg>
                            </div>

                            <!-- Item Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        ${item.icon ? `<span class="text-gray-500 text-lg">${item.icon}</span>` : ''}
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                ${this.getItemDisplayLabel(item)}
                                            </p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                                ${this.getItemTypeLabel(item)}
                                            </p>
                                        </div>
                                        ${!item.is_visible ? '<span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">{{ flexiblePagesTrans('menu_items.status.hidden') }}</span>' : ''}
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        ${canHaveChildren ? `
                                            <button onclick="$wire.addMenuItem(${item.id})" 
                                                    class="inline-flex items-center px-2 py-1 border border-gray-300 rounded text-xs font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                                {{ flexiblePagesTrans('menu_items.tree.add_child') }}
                                            </button>
                                        ` : ''}
                                        <button onclick="$wire.editMenuItem(${item.id})" 
                                                class="inline-flex items-center px-2 py-1 border border-primary-300 rounded text-xs font-medium text-primary-700 bg-primary-50 hover:bg-primary-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500">
                                            {{ flexiblePagesTrans('menu_items.tree.edit') }}
                                        </button>
                                        <button onclick="$wire.deleteMenuItem(${item.id})" 
                                                class="inline-flex items-center px-2 py-1 border border-red-300 rounded text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                            {{ flexiblePagesTrans('menu_items.tree.delete') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        ${hasChildren ? `
                            <div class="space-y-2 mt-2">
                                ${item.children.map(child => this.renderTreeItem(child, depth + 1)).join('')}
                            </div>
                        ` : ''}
                    `;
                },

                getItemDisplayLabel(item) {
                    if (item.use_model_title && item.linkable && item.linkable.title) {
                        return item.linkable.title;
                    }
                    return item.label || '{{ flexiblePagesTrans('menu_items.status.no_label') }}';
                },

                getItemTypeLabel(item) {
                    if (item.linkable_type && item.linkable) {
                        return `{{ flexiblePagesTrans('menu_items.tree.linked_to') }} ${item.linkable_type}`;
                    }
                    if (item.url) {
                        return `{{ flexiblePagesTrans('menu_items.tree.external_url') }}: ${item.url}`;
                    }
                    return '{{ flexiblePagesTrans('menu_items.tree.no_link') }}';
                },

                reorderItems(oldIndex, newIndex) {
                    // Move item in array
                    const item = this.items.splice(oldIndex, 1)[0];
                    this.items.splice(newIndex, 0, item);
                    
                    // Send new order to server
                    const orderedIds = this.items.map(item => item.id);
                    this.loading = true;
                    this.$wire.call('reorderMenuItems', orderedIds).then(() => {
                        this.loading = false;
                        // Refresh items to get updated structure
                        location.reload();
                    });
                }
            }
        }
    </script>
    @endpush
</x-filament-panels::page>