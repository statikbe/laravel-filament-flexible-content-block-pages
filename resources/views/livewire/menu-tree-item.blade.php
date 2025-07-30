<div class="menu-tree-item" data-item-id="{{ $item->id }}">
    <div class="flex items-center p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 group hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ $depth > 0 ? 'ml-' . ($depth * 8) : '' }}">
        
        @if($showActions)
        <!-- Drag Handle -->
        <div class="drag-handle cursor-move text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 mr-4 opacity-0 group-hover:opacity-100 transition-opacity">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
            </svg>
        </div>
        @endif

        <!-- Expand/Collapse Button for items with children -->
        @if($this->hasChildren())
            <button 
                type="button"
                wire:click="toggleExpanded"
                class="mr-2 p-1 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors"
            >
                <svg class="w-4 h-4 transform transition-transform {{ $isExpanded ? 'rotate-90' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        @else
            <div class="w-6 mr-2"></div>
        @endif

        <!-- Item Content -->
        <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    @if($item->icon)
                        <span class="text-gray-500 text-lg">{!! $item->icon !!}</span>
                    @endif
                    
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                            {{ $this->getItemDisplayLabel() }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                            {{ $this->getItemTypeLabel() }}
                        </p>
                    </div>
                    
                    @if(!$item->is_visible)
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                            {{ flexiblePagesTrans('menu_items.status.hidden') }}
                        </span>
                    @endif
                </div>

                @if($showActions)
                <!-- Actions -->
                <div class="flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    @if($this->canHaveChildren())
                        <x-filament::button
                            color="gray"
                            size="xs"
                            wire:click="mountAction('addChild')"
                        >
                            <x-slot name="icon">
                                heroicon-o-plus
                            </x-slot>
                            {{ flexiblePagesTrans('menu_items.tree.add_child') }}
                        </x-filament::button>
                    @endif
                    
                    <x-filament::button
                        color="primary"
                        size="xs"
                        wire:click="mountAction('edit')"
                    >
                        <x-slot name="icon">
                            heroicon-o-pencil
                        </x-slot>
                        {{ flexiblePagesTrans('menu_items.tree.edit') }}
                    </x-filament::button>
                    
                    <x-filament::button
                        color="danger"
                        size="xs"
                        wire:click="mountAction('delete')"
                    >
                        <x-slot name="icon">
                            heroicon-o-trash
                        </x-slot>
                        {{ flexiblePagesTrans('menu_items.tree.delete') }}
                    </x-filament::button>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Children -->
    @if($this->hasChildren() && $isExpanded)
        <div class="space-y-2 mt-2" wire:key="children-{{ $item->id }}">
            @foreach($item->children as $child)
                @livewire('filament-flexible-content-block-pages::menu-tree-item', [
                    'item' => $child,
                    'depth' => $depth + 1,
                    'maxDepth' => $maxDepth,
                    'showActions' => $showActions
                ], key($child->id))
            @endforeach
        </div>
    @endif
</div>