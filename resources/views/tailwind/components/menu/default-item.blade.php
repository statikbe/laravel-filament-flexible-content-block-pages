{{-- Default menu item template --}}

@props([
    'item',
    'style',
    'locale',

    'itemClass' => 'menu-item',
    'itemHasChildrenClass' => 'menu-item--has-children',
    'currentItemClass' => 'menu-item--current',
    'activeItemClass' => 'menu-item--active',

    'itemLinkClass' => 'menu-item-link',
    'currentItemLinkClass' => 'menu-item-link--current',
    'activeItemLinkClass' => 'menu-item-link--active',

    'subMenuClass' => 'navigation-sub-menu',
    'level' => 1,
])

@php
    /** @var \Statikbe\FilamentFlexibleContentBlockPages\Components\Data\MenuItemData $item */

    $isCurrent = $item->isCurrentMenuItem();
    $isActive = $isCurrent || ($item->hasChildren() && $item->hasActiveChildren());

    $wrapperTag = $isCurrent
        ? 'span'
        : 'a';
@endphp

<li @class([
    $itemClass,
    "menu-item--level-{$level}",
    $itemHasChildrenClass => $item->hasChildren(),
    $currentItemClass => $isCurrent,
    $activeItemClass => $isActive,
])>
    <{{ $wrapperTag }}
        @if (!$isCurrent) href="{{ $item->url }}" @endif
        @class([
            $itemLinkClass,
            "menu-item-link--level-{$level}",
            $currentItemLinkClass => $isCurrent,
            $activeItemLinkClass => $isActive,
        ])
        @if (!$isCurrent && $item->target !== '_self') target="{{ $item->target }}" @endif
        @if ($isCurrent) aria-current="page" @endif
    >
        @if ($item->icon)
            <x-filament::icon :icon="$item->icon" class="menu-item-icon h-5 w-5" />
        @endif
        {{ $item->label }}
    </{{ $wrapperTag }}>

    @if ($item->hasChildren())
        <ul @class([
                $subMenuClass,
                "navigation-sub-menu--level-{$level}",
            ])
        >
            @foreach ($item->children as $child)
                <x-flexible-pages-menu-item :item="$child"
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
                                            :level="$level + 1"
                />
          @endforeach
        </ul>
    @endif
</li>
