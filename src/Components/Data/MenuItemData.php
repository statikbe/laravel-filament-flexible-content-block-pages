<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Components\Data;

use Illuminate\Support\Collection;
use Statikbe\FilamentFlexibleContentBlockPages\Models\MenuItem;

class MenuItemData
{
    public function __construct(
        public string $label,
        public string $url,
        public ?string $target,
        public ?string $icon,
        public ?Collection $children,
    ) {}

    public static function create(MenuItem $item, string $locale): self
    {
        return new self(
            $item->getDisplayLabel($locale),
            $item->getCompleteUrl($locale),
            $item->getTarget(),
            $item->icon,
            $item->children->map(function (MenuItem $childItem) use ($locale) {
                return static::create($childItem, $locale);
            })
        );
    }

    public function hasChildren(): bool
    {
        return $this->children && $this->children->isNotEmpty();
    }

    public function isCurrentMenuItem(): bool
    {
        $currentUrl = request()->url();
        $itemUrl = $this->url;

        return MenuItem::urlsMatch($itemUrl, $currentUrl);
    }

    public function hasActiveChildren(): bool
    {
        if (! $this->children?->isEmpty()) {
            return false;
        }

        return $this->children->some(function ($child) {
            /** @var static $child */
            return $child->isCurrentMenuItem() || $child->hasActiveChildren();
        });
    }
}
