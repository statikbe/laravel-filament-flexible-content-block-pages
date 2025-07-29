<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Filament\Form\Fields\Types;

abstract class AbstractMenuItemType
{
    protected ?string $icon = null;

    public function getIcon(): ?string
    {
        return $this->icon;
    }

    public function icon(?string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

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
