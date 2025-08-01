{{-- Mobile menu item template --}}
@php
    $linkClasses = collect(['block', 'px-3', 'py-2', 'rounded-md', 'text-base', 'font-medium'])
        ->when($item['is_current'], fn($collection) => $collection->push('text-indigo-700', 'bg-indigo-50'))
        ->when(!$item['is_current'], fn($collection) => $collection->push('text-gray-700', 'hover:text-gray-900', 'hover:bg-gray-50'))
        ->filter()
        ->implode(' ');
@endphp

<a href="{{ $item['url'] }}" 
   class="{{ $linkClasses }}" 
   role="menuitem"
   @if($item['target'] !== '_self') target="{{ $item['target'] }}" @endif
   @if($item['is_current']) aria-current="page" @endif
   {!! $getDataAttributes() !!}>
    {{ $item['label'] }}
    @if($item['has_children'])
        <span class="ml-2 text-gray-400">▼</span>
    @endif
</a>

@if($item['has_children'])
    <div class="pl-4">
        @foreach($item['children'] as $child)
            <a href="{{ $child['url'] }}" 
               class="block px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-50" 
               role="menuitem"
               @if($child['target'] !== '_self') target="{{ $child['target'] }}" @endif
               @if($child['is_current']) aria-current="page" @endif>
                {{ $child['label'] }}
            </a>
        @endforeach
    </div>
@endif