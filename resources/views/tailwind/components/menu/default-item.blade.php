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
    $hasChildren = $item->children && $item->children->isNotEmpty();
    $isCurrent = $item->isCurrentMenuItem();
    $isActive = $isCurrent || ($hasChildren && $item->hasActiveChildren());

    $wrapperTag = $isCurrent
        ? 'span'
        : 'a';
@endphp

<li @class([
    $itemClass,
    "menu-item--level-{$level}",
    $itemHasChildrenClass => $hasChildren,
    $currentItemClass => $isCurrent,
    $activeItemClass => $isActive,
])>
    <{{ $wrapperTag }}
        @if (!$isCurrent) href="{{ $item->getCompleteUrl($locale) }}" @endif
        @class([
            $itemLinkClass,
            "menu-item-link--level-{$level}",
            $currentItemLinkClass => $isCurrent,
            $activeItemLinkClass => $isActive,
        ])
        @if (!$isCurrent && $item->getTarget() !== '_self') target="{{ $item->getTarget() }}" @endif
        @if ($isCurrent) aria-current="page" @endif
    >
        {{ $item->getDisplayLabel($locale) }}
    </{{ $wrapperTag }}>

    @if ($hasChildren)
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
