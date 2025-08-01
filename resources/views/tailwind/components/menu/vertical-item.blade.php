{{-- Vertical menu item template with collapsible submenus --}}
@php
    $hasChildren = $item->children && $item->children->isNotEmpty();
    $isCurrent = $item->isCurrentMenuItem();
    $isActive = $isCurrent || ($hasChildren && $item->hasActiveChildren());
    
    $classes = collect(['menu-item'])
        ->when($hasChildren, fn($collection) => $collection->push('has-children'))
        ->when($isCurrent, fn($collection) => $collection->push('current'))
        ->when($isActive, fn($collection) => $collection->push('active'))
        ->filter()
        ->implode(' ');

    $linkClasses = collect(['menu-link', 'flex', 'items-center', 'px-4', 'py-3', 'text-sm', 'font-medium', 'rounded-lg'])
        ->when($isCurrent, fn($collection) => $collection->push('bg-blue-100', 'text-blue-700'))
        ->when(!$isCurrent, fn($collection) => $collection->push('text-gray-700', 'hover:bg-gray-100'))
        ->filter()
        ->implode(' ');
        
    $itemId = 'menu-item-' . $item->id;
    $displayLabel = $item->getDisplayLabel($locale);
@endphp

<li class="{{ $classes }}"
    @if($hasChildren) 
        data-has-children="true" 
        data-item-id="{{ $itemId }}"
    @endif>
    
    @if($hasChildren)
        {{-- Parent item with submenu toggle --}}
        <div class="flex items-center">
            <a href="{{ $item->getCompleteUrl($locale) }}" 
               class="{{ $linkClasses }} flex-1"
               role="menuitem"
               @if($item->getTarget() !== '_self') target="{{ $item->getTarget() }}" @endif
               @if($isCurrent) aria-current="page" @endif>
                <span class="flex-1">{{ $displayLabel }}</span>
            </a>
            <button type="button"
                    class="p-1 ml-2 rounded hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    @click="$parent.toggleSubmenu('{{ $itemId }}')"
                    :aria-expanded="$parent.isExpanded('{{ $itemId }}')"
                    :aria-controls="'submenu-{{ $itemId }}'"
                    aria-label="{{ flexiblePagesTrans('menu.toggle_submenu', ['label' => $displayLabel]) }}">
                <svg class="w-4 h-4 transition-transform" 
                     :class="{ 'rotate-90': $parent.isExpanded('{{ $itemId }}') }"
                     fill="currentColor" 
                     viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
        
        {{-- Collapsible submenu --}}
        <ul x-show="$parent.isExpanded('{{ $itemId }}')"
            x-transition:enter="transition ease-out duration-100"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-75"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="ml-6 mt-2 space-y-1"
            id="submenu-{{ $itemId }}"
            role="menu">
            @foreach($item->children as $child)
                <li role="none">
                    <a href="{{ $child->getCompleteUrl($locale) }}" 
                       class="flex items-center px-3 py-2 text-sm rounded-lg {{ $child->isCurrentMenuItem() ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' }}"
                       role="menuitem"
                       @if($child->getTarget() !== '_self') target="{{ $child->getTarget() }}" @endif
                       @if($child->isCurrentMenuItem()) aria-current="page" @endif>
                        {{ $child->getDisplayLabel($locale) }}
                    </a>
                </li>
            @endforeach
        </ul>
    @else
        {{-- Regular menu item without children --}}
        <a href="{{ $item->getCompleteUrl($locale) }}" 
           class="{{ $linkClasses }}"
           role="menuitem"
           @if($item->getTarget() !== '_self') target="{{ $item->getTarget() }}" @endif
           @if($isCurrent) aria-current="page" @endif>
            <span class="flex-1">{{ $displayLabel }}</span>
        </a>
    @endif
</li>