{{-- Horizontal menu item template --}}
@php
    $hasChildren = $item->children && $item->children->isNotEmpty();
    $isCurrent = $item->isCurrentMenuItem();
    $isActive = $isCurrent || ($hasChildren && $item->hasActiveChildren());
    
    $classes = collect(['menu-item'])
        ->when($hasChildren, fn($collection) => $collection->push('relative', 'group'))
        ->when($isCurrent, fn($collection) => $collection->push('current'))
        ->when($isActive, fn($collection) => $collection->push('active'))
        ->filter()
        ->implode(' ');

    $linkClasses = collect(['menu-link', 'px-3', 'py-2', 'rounded-md', 'text-sm', 'font-medium'])
        ->when($isCurrent, fn($collection) => $collection->push('bg-gray-900', 'text-white'))
        ->when(!$isCurrent, fn($collection) => $collection->push('text-gray-900', 'hover:bg-gray-50'))
        ->filter()
        ->implode(' ');
@endphp

<li class="{{ $classes }}">
    <a href="{{ $item->getCompleteUrl($locale) }}" 
       class="{{ $linkClasses }}"
       @if($item->getTarget() !== '_self') target="{{ $item->getTarget() }}" @endif
       @if($isCurrent) aria-current="page" @endif>
        {{ $item->getDisplayLabel($locale) }}
    </a>
    
    @if($hasChildren)
        <ul class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-10">
            @foreach($item->children as $child)
                <li>
                    <a href="{{ $child->getCompleteUrl($locale) }}" 
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ $child->isCurrentMenuItem() ? 'bg-gray-50 font-medium' : '' }}"
                       @if($child->getTarget() !== '_self') target="{{ $child->getTarget() }}" @endif
                       @if($child->isCurrentMenuItem()) aria-current="page" @endif>
                        {{ $child->getDisplayLabel($locale) }}
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</li>