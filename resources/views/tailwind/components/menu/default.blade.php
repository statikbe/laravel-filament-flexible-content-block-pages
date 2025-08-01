{{-- Default menu template --}}
@if($items && $items->isNotEmpty() && $menu)
<nav class="menu-navigation" role="navigation" aria-label="{{ $menu->name }}">
    <ul class="menu">
        @foreach($items as $item)
            <x-flexible-pages-menu-item :item="$item" :style="$style" :locale="$locale" />
        @endforeach
    </ul>
</nav>
@endif