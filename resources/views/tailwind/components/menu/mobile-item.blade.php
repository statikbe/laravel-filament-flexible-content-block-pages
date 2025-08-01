{{-- Mobile menu item template --}}
@php
    $isCurrent = $item->isCurrentMenuItem();
    $hasChildren = $item->children && $item->children->isNotEmpty();
    
    $linkClasses = collect(['block', 'px-3', 'py-2', 'rounded-md', 'text-base', 'font-medium'])
        ->when($isCurrent, fn($collection) => $collection->push('text-indigo-700', 'bg-indigo-50'))
        ->when(!$isCurrent, fn($collection) => $collection->push('text-gray-700', 'hover:text-gray-900', 'hover:bg-gray-50'))
        ->filter()
        ->implode(' ');
@endphp

<a href="{{ $item->getCompleteUrl($locale) }}" 
   class="{{ $linkClasses }}" 
   role="menuitem"
   @if($item->getTarget() !== '_self') target="{{ $item->getTarget() }}" @endif
   @if($isCurrent) aria-current="page" @endif>
    {{ $item->getDisplayLabel($locale) }}
    @if($hasChildren)
        <span class="ml-2 text-gray-400">▼</span>
    @endif
</a>

@if($hasChildren)
    <div class="pl-4">
        @foreach($item->children as $child)
            <a href="{{ $child->getCompleteUrl($locale) }}" 
               class="block px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50 {{ $child->isCurrentMenuItem() ? 'text-indigo-700 bg-indigo-50' : '' }}" 
               role="menuitem"
               @if($child->getTarget() !== '_self') target="{{ $child->getTarget() }}" @endif
               @if($child->isCurrentMenuItem()) aria-current="page" @endif>
                {{ $child->getDisplayLabel($locale) }}
            </a>
        @endforeach
    </div>
@endif