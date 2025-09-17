<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Form\Components\MenuItemTypes;

abstract class AbstractMenuItemType
{
    abstract public function getAlias(): string;

    public function isUrlType(): bool
    {
        return false;
    }

    public function isRouteType(): bool
    {
        return false;
    }

    public function isModelType(): bool
    {
        return false;
    }
}
