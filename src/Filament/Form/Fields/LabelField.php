<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Filament\Form\Fields;

use Statikbe\FilamentFlexibleContentBlocks\Filament\Form\Fields\TitleField;

class LabelField extends TitleField
{
    const FIELD = 'label';

    public static function create(bool $required = false): static
    {
        $field = static::getFieldName();

        return parent::create($required)
            ->label(flexiblePagesTrans('menu_items.form.label_lbl'));
    }

    public static function getFieldName(): string
    {
        return self::FIELD;
    }
}
