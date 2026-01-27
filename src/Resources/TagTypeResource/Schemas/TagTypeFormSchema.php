<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\TagTypeResource\Schemas;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Guava\IconPicker\Forms\Components\IconPicker;
use Statikbe\FilamentFlexibleContentBlockPages\Form\Components\NameField;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Form\Fields\CodeField;

class TagTypeFormSchema
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    NameField::create(true),
                    CodeField::create(true),
                    Toggle::make('is_default_type')
                        ->label(flexiblePagesTrans('tag_types.tag_type_is_default_type_lbl'))
                        ->default(false),
                    Toggle::make('has_seo_pages')
                        ->label(flexiblePagesTrans('tag_types.tag_type_has_seo_pages_lbl'))
                        ->default(false),
                    ColorPicker::make('colour')
                        ->label(flexiblePagesTrans('form_component.colour_lbl'))
                        ->regex('/^#([a-fA-F0-9]{6}|[a-fA-F0-9]{3})\b$/')
                        ->required(true),
                    IconPicker::make('icon')
                        ->label(flexiblePagesTrans('form_component.icon_lbl')),
                ]),
            ]);
    }
}
