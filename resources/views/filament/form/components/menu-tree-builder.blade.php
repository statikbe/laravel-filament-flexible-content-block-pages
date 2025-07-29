<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div 
        x-data="menuTreeBuilder({
            maxDepth: {{ $getMaxDepth() }},
            menuId: {{ $getMenuId() ?? 'null' }},
            items: @js($getMenuItems())
        })"
        x-init="init()"
        class="space-y-4"
        {{ $attributes->merge($getExtraAttributes())->merge($getExtraAlpineAttributes()) }}
    >
        <!-- Tree Container -->
        <div class="bg-white dark:bg-gray-900 rounded-lg border border-gray-300 dark:border-gray-600 min-h-[200px]">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-300 dark:border-gray-600">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                    {{ flexiblePagesTrans('menu_items.tree.title') }}
                </h3>
                <button
                    type="button"
                    x-on:click="showAddItemModal(null)"
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-primary-600 hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500"
                >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    {{ flexiblePagesTrans('menu_items.tree.add_item') }}
                </button>
            </div>

            <!-- Tree Content -->
            <div class="p-4">
                <div x-show="items.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                    {{ flexiblePagesTrans('menu_items.tree.empty_state') }}
                </div>

                <div x-show="items.length > 0" class="space-y-2">
                    <template x-for="(item, index) in items" :key="item.id">
                        <div class="menu-tree-item" x-bind:data-item-id="item.id">
                            <div x-html="renderTreeItem(item, 0)"></div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- Loading State -->
        <div x-show="loading" class="flex items-center justify-center py-8">
            <svg class="animate-spin h-8 w-8 text-primary-600" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        function menuTreeBuilder(config) {
            return {
                items: config.items || [],
                maxDepth: config.maxDepth || 2,
                menuId: config.menuId,
                loading: false,
                draggedItem: null,

                init() {
                    this.initSortable();
                },

                initSortable() {
                    // Initialize SortableJS for drag & drop
                    if (typeof Sortable !== 'undefined') {
                        const container = this.$el.querySelector('.space-y-2');
                        if (container) {
                            Sortable.create(container, {
                                group: 'menu-items',
                                animation: 150,
                                handle: '.drag-handle',
                                onStart: (evt) => {
                                    this.draggedItem = this.items[evt.oldIndex];
                                },
                                onEnd: (evt) => {
                                    if (evt.oldIndex !== evt.newIndex) {
                                        this.moveItem(evt.oldIndex, evt.newIndex);
                                    }
                                    this.draggedItem = null;
                                }
                            });
                        }
                    }
                },

                renderTreeItem(item, depth) {
                    const canHaveChildren = depth < this.maxDepth;
                    const hasChildren = item.children && item.children.length > 0;
                    
                    return `
                        <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 group">
                            <!-- Drag Handle -->
                            <div class="drag-handle cursor-move text-gray-400 hover:text-gray-600 mr-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                                </svg>
                            </div>

                            <!-- Item Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        ${item.icon ? `<span class="text-gray-500">${item.icon}</span>` : ''}
                                        <div>
                                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                ${this.getItemDisplayLabel(item)}
                                            </p>
                                            <p class="text-xs text-gray-500 truncate">
                                                ${this.getItemTypeLabel(item)}
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        ${canHaveChildren ? `
                                            <button type="button" @click="showAddItemModal(item)" class="text-sm text-primary-600 hover:text-primary-800">
                                                {{ flexiblePagesTrans('menu_items.tree.add_child') }}
                                            </button>
                                        ` : ''}
                                        <button type="button" @click="editItem(item)" class="text-sm text-blue-600 hover:text-blue-800">
                                            {{ flexiblePagesTrans('menu_items.tree.edit') }}
                                        </button>
                                        <button type="button" @click="deleteItem(item)" class="text-sm text-red-600 hover:text-red-800">
                                            {{ flexiblePagesTrans('menu_items.tree.delete') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        ${hasChildren ? `
                            <div class="ml-8 mt-2 space-y-2">
                                ${item.children.map(child => this.renderTreeItem(child, depth + 1)).join('')}
                            </div>
                        ` : ''}
                    `;
                },

                getItemDisplayLabel(item) {
                    if (item.use_model_title && item.linkable) {
                        return item.linkable.title || item.label;
                    }
                    return item.label;
                },

                getItemTypeLabel(item) {
                    if (item.linkable_type) {
                        return `{{ flexiblePagesTrans('menu_items.tree.linked_to') }} ${item.linkable_type}`;
                    }
                    if (item.url) {
                        return `{{ flexiblePagesTrans('menu_items.tree.external_url') }}: ${item.url}`;
                    }
                    return '{{ flexiblePagesTrans('menu_items.tree.no_link') }}';
                },

                showAddItemModal(parentItem) {
                    // This will be handled by opening a Filament modal/slide-over
                    // For now, we'll dispatch an Alpine event that can be caught by the parent component
                    this.$dispatch('menu-item-add', { 
                        parentId: parentItem?.id || null,
                        menuId: this.menuId 
                    });
                },

                editItem(item) {
                    this.$dispatch('menu-item-edit', { item });
                },

                deleteItem(item) {
                    if (confirm('{{ flexiblePagesTrans('menu_items.tree.delete_confirm') }}')) {
                        this.$dispatch('menu-item-delete', { item });
                    }
                },

                moveItem(oldIndex, newIndex) {
                    // Move item in array
                    const item = this.items.splice(oldIndex, 1)[0];
                    this.items.splice(newIndex, 0, item);
                    
                    // Dispatch event to handle the reordering
                    this.$dispatch('menu-item-move', {
                        item,
                        oldIndex,
                        newIndex
                    });
                }
            }
        }
    </script>
    @endpush
</x-dynamic-component>