{{-- Horizontal menu template --}}
@if($items && $menu)
<nav class="menu-navigation menu-horizontal" role="navigation" aria-label="{{ $menu->name }}">
    <ul class="flex space-x-6">
        @foreach($items as $item)
            <x-flexible-pages-menu-item :item="$item" :style="$style" />
        @endforeach
    </ul>
</nav>
@endif