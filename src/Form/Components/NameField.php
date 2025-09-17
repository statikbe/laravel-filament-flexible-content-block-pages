<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Form\Components;

use Statikbe\FilamentFlexibleContentBlocks\Filament\Form\Fields\TitleField;

class NameField extends TitleField
{
    const FIELD = 'name';

    public static function create(bool $required = false): static
    {
        return parent::create($required)
            ->label(flexiblePagesTrans('form_component.name_lbl'));
    }

    public static function getFieldName(): string
    {
        return static::FIELD;
    }
}
