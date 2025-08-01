{{-- Dropdown menu item template --}}
@php
    $isCurrent = $item->isCurrentMenuItem();
    $hasChildren = $item->children && $item->children->isNotEmpty();
    
    $linkClasses = collect(['block', 'px-4', 'py-2', 'text-sm', 'text-gray-700'])
        ->when($isCurrent, fn($collection) => $collection->push('bg-gray-100', 'text-gray-900'))
        ->when(!$isCurrent, fn($collection) => $collection->push('hover:bg-gray-100', 'hover:text-gray-900'))
        ->filter()
        ->implode(' ');
@endphp

<a href="{{ $item->getCompleteUrl($locale) }}" 
   class="{{ $linkClasses }}" 
   role="menuitem" 
   tabindex="-1"
   @if($item->getTarget() !== '_self') target="{{ $item->getTarget() }}" @endif
   @if($isCurrent) aria-current="page" @endif>
    {{ $item->getDisplayLabel($locale) }}
    @if($hasChildren)
        <span class="ml-2 text-gray-400">→</span>
    @endif
</a>

@if($hasChildren)
    @foreach($item->children as $child)
        <a href="{{ $child->getCompleteUrl($locale) }}" 
           class="block px-8 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-900 {{ $child->isCurrentMenuItem() ? 'bg-gray-100 text-gray-900' : '' }}" 
           role="menuitem" 
           tabindex="-1"
           @if($child->getTarget() !== '_self') target="{{ $child->getTarget() }}" @endif
           @if($child->isCurrentMenuItem()) aria-current="page" @endif>
            {{ $child->getDisplayLabel($locale) }}
        </a>
    @endforeach
@endif