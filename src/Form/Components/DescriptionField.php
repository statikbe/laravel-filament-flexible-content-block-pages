<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Form\Components;

use Filament\Forms\Components\RichEditor;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Form\Fields\Concerns\HasTranslatableHint;

class DescriptionField extends RichEditor
{
    use HasTranslatableHint;

    public static function create(string $field, bool $required = false): static
    {
        return static::make($field)
            ->label(flexiblePagesTrans("form_component.{$field}_lbl"))
            ->maxLength(255)
            ->live()
            ->addsTranslatableHint()
            ->required($required)
            ->disableToolbarButtons([
                'attachFiles',
            ]);
    }
}
