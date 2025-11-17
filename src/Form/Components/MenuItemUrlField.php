<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Form\Components;

use Statikbe\FilamentFlexibleContentBlocks\Filament\Form\Fields\TitleField;

class MenuItemUrlField extends TitleField
{
    const FIELD = 'url';

    public static function create(bool $required = false): static
    {
        return parent::create($required)
            ->label(flexiblePagesTrans('menu_items.form.url_lbl'))
            ->url()
            ->required()
            ->helperText(flexiblePagesTrans('menu_items.form.url_help'));
    }

    public static function getFieldName(): string
    {
        return self::FIELD;
    }
}
