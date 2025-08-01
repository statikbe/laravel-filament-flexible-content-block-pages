{{-- Default menu item template --}}
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

    $linkClasses = collect(['menu-link'])
        ->when($isCurrent, fn($collection) => $collection->push('current'))
        ->when($isActive, fn($collection) => $collection->push('active'))
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
        <ul class="menu submenu">
            @foreach($item->children as $child)
                <x-flexible-pages-menu-item :item="$child" :style="$style" :locale="$locale" />
            @endforeach
        </ul>
    @endif
</li>