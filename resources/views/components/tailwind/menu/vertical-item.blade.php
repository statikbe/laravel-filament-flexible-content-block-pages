{{-- Vertical menu item template --}}
@php
    $classes = collect(['menu-item'])
        ->when($item['has_children'], fn($collection) => $collection->push('has-children'))
        ->when($item['is_current'], fn($collection) => $collection->push('current'))
        ->when($item['is_active'], fn($collection) => $collection->push('active'))
        ->when(!empty($item['css_classes']), fn($collection) => $collection->push($item['css_classes']))
        ->filter()
        ->implode(' ');

    $linkClasses = collect(['menu-link', 'flex', 'items-center', 'px-4', 'py-3', 'text-sm', 'font-medium', 'rounded-lg'])
        ->when($item['is_current'], fn($collection) => $collection->push('bg-blue-100', 'text-blue-700'))
        ->when(!$item['is_current'], fn($collection) => $collection->push('text-gray-700', 'hover:bg-gray-100'))
        ->filter()
        ->implode(' ');
@endphp

<li class="{{ $classes }}" {!! $getDataAttributes() !!}>
    <a href="{{ $item['url'] }}" 
       class="{{ $linkClasses }}"
       @if($item['target'] !== '_self') target="{{ $item['target'] }}" @endif
       @if($item['is_current']) aria-current="page" @endif>
        <span class="flex-1">{{ $item['label'] }}</span>
        @if($item['has_children'])
            <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
            </svg>
        @endif
    </a>
    
    @if($item['has_children'])
        <ul class="ml-6 mt-2 space-y-1">
            @foreach($item['children'] as $child)
                <li>
                    <a href="{{ $child['url'] }}" 
                       class="flex items-center px-3 py-2 text-sm rounded-lg {{ $child['is_current'] ? 'bg-blue-50 text-blue-600 font-medium' : 'text-gray-600 hover:bg-gray-50' }}"
                       @if($child['target'] !== '_self') target="{{ $child['target'] }}" @endif
                       @if($child['is_current']) aria-current="page" @endif>
                        {{ $child['label'] }}
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</li>