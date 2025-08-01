<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Form\Fields\Types;

use Filament\Forms\Components\TextInput;

class UrlMenuItemType extends AbstractMenuItemType
{
    const TYPE_URL = 'url';

    final public function __construct() {}

    public static function make(): static
    {
        return new static;
    }

    public function getAlias(): string
    {
        return self::TYPE_URL;
    }

    public function getUrlField(): TextInput
    {
        return TextInput::make('url')
            ->label(flexiblePagesTrans('menu_items.form.url_lbl'))
            ->url()
            ->required()
            ->helperText(flexiblePagesTrans('menu_items.form.url_help'));
    }

    public function isUrlType(): bool
    {
        return true;
    }
}
