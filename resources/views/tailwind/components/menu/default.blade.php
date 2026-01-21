{{-- Default menu template --}}
@php
    /** @var \Statikbe\FilamentFlexibleContentBlockPages\Components\Data\MenuData $menu */
@endphp

@props([
    /* --- provided by the component --- */
    'menu',
    'items',
    'style',
    'locale',

    /* --- to tweak the display of the menu --- */
    'wrapWithNav' => true,
    'navClass' => 'navigation-menu',
    'ulClass' => 'navigation-menu-list',
    /* both 'titleClass' and 'titleTag' are only used when the title of the menu itself is shown (so not when a 'title' slot has been provided) */
    'titleClass' => 'navigation-menu-title',
    'titleTag' => 'h3',

    /* --- to tweak the display of the individual menu items --- */
    'itemClass' => null,
    'itemHasChildrenClass' => null,
    'currentItemClass' => null,
    'activeItemClass' => null,
    'itemLinkClass' => null,
    'currentItemLinkClass' => null,
    'activeItemLinkClass' => null,
    'subMenuClass' => null,

    /* --- optional slots --- */
    'title', /* this 'title' slot will override the title field of the menu itself */
])

@if($items && $items->isNotEmpty() && $menu)
    @if ($wrapWithNav)
        <nav class="{{ $navClass }}" aria-label="{{ $menu->title ?? $menu->name }}">
    @endif
    @if (isset($title))
        {{ $title }}
    @elseif (isset($menu->title))
        <{{ $titleTag }} class="{{ $titleClass }}" @if($wrapWithNav) aria-hidden="true" @endif>
            {{ $menu->title }}
        </{{ $titleTag }}>
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
