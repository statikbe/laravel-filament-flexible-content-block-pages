{{-- Default menu item template --}}
@php
    $classes = collect(['menu-item'])
        ->when($item['has_children'], fn($collection) => $collection->push('has-children'))
        ->when($item['is_current'], fn($collection) => $collection->push('current'))
        ->when($item['is_active'], fn($collection) => $collection->push('active'))
        ->when(!empty($item['css_classes']), fn($collection) => $collection->push($item['css_classes']))
        ->filter()
        ->implode(' ');

    $linkClasses = collect(['menu-link'])
        ->when($item['is_current'], fn($collection) => $collection->push('current'))
        ->when($item['is_active'], fn($collection) => $collection->push('active'))
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
        <ul class="menu submenu">
            @foreach($item['children'] as $child)
                <x-flexible-pages-menu-item :item="$child" :style="$style" />
            @endforeach
        </ul>
    @endif
</li>