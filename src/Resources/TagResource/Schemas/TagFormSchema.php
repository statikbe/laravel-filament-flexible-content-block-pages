<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\TagResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Statikbe\FilamentFlexibleContentBlockPages\Form\Components\DescriptionField;
use Statikbe\FilamentFlexibleContentBlockPages\Form\Components\NameField;

class TagFormSchema
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    NameField::create(true),
                    DescriptionField::create('seo_description', false),
                    Select::make('type')
                        ->label(flexiblePagesTrans('tags.tag_type_lbl'))
                        ->hint(flexiblePagesTrans('tags.tag_type_hint'))
                        ->relationship('tagType', 'name')
                        ->preload()
                        ->default(function (Select $component) {
                            $relationship = $component->getRelationship();
                            if (! $relationship) {
                                return null;
                            }

                            /** @phpstan-ignore-next-line */
                            return $relationship->getModel()->query()
                                ->where('is_default_type', true)->first()?->getKey() ?? null;
                        }),
                ]),
            ]);
    }
}