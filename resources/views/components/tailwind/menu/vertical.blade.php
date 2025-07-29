{{-- Vertical menu template --}}
@if($items && $menu)
<nav class="menu-navigation menu-vertical" role="navigation" aria-label="{{ $menu->name }}">
    <ul class="space-y-1">
        @foreach($items as $item)
            <x-flexible-pages-menu-item :item="$item" :style="$style" />
        @endforeach
    </ul>
</nav>
@endif