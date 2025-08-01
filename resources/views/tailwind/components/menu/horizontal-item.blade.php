{{-- Horizontal menu item template --}}
@php
    $classes = collect(['menu-item'])
        ->when($item['has_children'], fn($collection) => $collection->push('relative', 'group'))
        ->when($item['is_current'], fn($collection) => $collection->push('current'))
        ->when($item['is_active'], fn($collection) => $collection->push('active'))
        ->when(!empty($item['css_classes']), fn($collection) => $collection->push($item['css_classes']))
        ->filter()
        ->implode(' ');

    $linkClasses = collect(['menu-link', 'px-3', 'py-2', 'rounded-md', 'text-sm', 'font-medium'])
        ->when($item['is_current'], fn($collection) => $collection->push('bg-gray-900', 'text-white'))
        ->when(!$item['is_current'], fn($collection) => $collection->push('text-gray-900', 'hover:bg-gray-50'))
        ->filter()
        ->implode(' ');
@endphp

<li class="{{ $classes }}" {!! $getDataAttributes() !!}>
    <a href="{{ $item['url'] }}" 
       class="{{ $linkClasses }}"
       @if($item['target'] !== '_self') target="{{ $item['target'] }}" @endif
       @if($item['is_current']) aria-current="page" @endif>
        {{ $item['label'] }}
    </a>
    
    @if($item['has_children'])
        <ul class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-10">
            @foreach($item['children'] as $child)
                <li>
                    <a href="{{ $child['url'] }}" 
                       class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 {{ $child['is_current'] ? 'bg-gray-50 font-medium' : '' }}"
                       @if($child['target'] !== '_self') target="{{ $child['target'] }}" @endif
                       @if($child['is_current']) aria-current="page" @endif>
                        {{ $child['label'] }}
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</li>