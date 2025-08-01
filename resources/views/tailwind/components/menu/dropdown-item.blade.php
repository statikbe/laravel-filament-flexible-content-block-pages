{{-- Dropdown menu item template --}}
@php
    $linkClasses = collect(['block', 'px-4', 'py-2', 'text-sm', 'text-gray-700'])
        ->when($item['is_current'], fn($collection) => $collection->push('bg-gray-100', 'text-gray-900'))
        ->when(!$item['is_current'], fn($collection) => $collection->push('hover:bg-gray-100', 'hover:text-gray-900'))
        ->filter()
        ->implode(' ');
@endphp

<a href="{{ $item['url'] }}" 
   class="{{ $linkClasses }}" 
   role="menuitem" 
   tabindex="-1"
   @if($item['target'] !== '_self') target="{{ $item['target'] }}" @endif
   @if($item['is_current']) aria-current="page" @endif
   {!! $getDataAttributes() !!}>
    {{ $item['label'] }}
    @if($item['has_children'])
        <span class="ml-2 text-gray-400">→</span>
    @endif
</a>

@if($item['has_children'])
    @foreach($item['children'] as $child)
        <a href="{{ $child['url'] }}" 
           class="block px-8 py-2 text-sm text-gray-600 hover:bg-gray-50 hover:text-gray-900" 
           role="menuitem" 
           tabindex="-1"
           @if($child['target'] !== '_self') target="{{ $child['target'] }}" @endif
           @if($child['is_current']) aria-current="page" @endif>
            {{ $child['label'] }}
        </a>
    @endforeach
@endif