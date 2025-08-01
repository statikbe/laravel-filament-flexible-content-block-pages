{{-- Default menu template --}}
@if($items && $menu)
<nav class="menu-navigation" role="navigation" aria-label="{{ $menu->name }}">
    <ul class="menu">
        @foreach($items as $item)
            <x-flexible-pages-menu-item :item="$item" :style="$style" />
        @endforeach
    </ul>
</nav>
@endif