{{-- Default menu template --}}

@props([
    /* provided by the component */
    'menu',
    'items',
    'style',
    'locale',

    /* to tweak the display of the menu */
    'wrapWithNav' => true,
    'navClass' => 'navigation-menu',
    'ulClass' => 'navigation-menu-list',

    /* to tweak the display of the individual menu items */
    'itemClass' => null,
    'itemHasChildrenClass' => null,
    'currentItemClass' => null,
    'activeItemClass' => null,
    'itemLinkClass' => null,
    'currentItemLinkClass' => null,
    'activeItemLinkClass' => null,
    'subMenuClass' => null,

    /* optional slots */
    'title',
])

@if($items && $items->isNotEmpty() && $menu)
    @if ($wrapWithNav)
        <nav class="{{ $navClass }}" aria-label="{{ $menu->name }}">
    @endif
        @if (isset($title))
            {{ $title }}
        @endif

        <ul class="{{ $ulClass }}">
            @foreach ($items as $item)
                <x-flexible-pages-menu-item :item="$item"
                                            :style="$style"
                                            :locale="$locale"
                                            :itemClass="$itemClass"
                                            :itemHasChildrenClass="$itemHasChildrenClass"
                                            :currentItemClass="$currentItemClass"
                                            :activeItemClass="$activeItemClass"
                                            :itemLinkClass="$itemLinkClass"
                                            :currentItemLinkClass="$currentItemLinkClass"
                                            :activeItemLinkClass="$activeItemLinkClass"
                                            :subMenuClass="$subMenuClass"
                />
            @endforeach
        </ul>

    @if ($wrapWithNav)
        </nav>
    @endif
@endif
