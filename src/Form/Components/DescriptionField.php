<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Form\Components;

use Filament\Forms\Components\Field;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Form\Fields\FlexibleRichEditorField;

class DescriptionField
{
    public static function create(string $field, bool $required = false): Field
    {
        return FlexibleRichEditorField::createTranslatable($field)
            ->label(flexiblePagesTrans("form_component.{$field}_lbl"))
            ->live()
            ->required($required);
    }
}
