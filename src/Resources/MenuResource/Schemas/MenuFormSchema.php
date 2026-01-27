<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\MenuResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Form\Fields\CodeField;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Form\Fields\TitleField;

class MenuFormSchema
{
    public static function configure(Schema $schema): Schema
    {
        $availableStyles = FilamentFlexibleContentBlockPages::config()->getMenuStyles();
        $showStyleField = count($availableStyles) > 1;

        $formFields = [
            TextInput::make('name')
                ->label(flexiblePagesTrans('menus.form.name_lbl'))
                ->helperText(flexiblePagesTrans('menus.form.name_help'))
                ->required()
                ->maxLength(255),

            CodeField::create(true)
                ->helperText(flexiblePagesTrans('menus.form.code_help')),

            Textarea::make('description')
                ->label(flexiblePagesTrans('menus.form.description_lbl'))
                ->helperText(flexiblePagesTrans('menus.form.description_help'))
                ->rows(3)
                ->columnSpan(2),

            TitleField::create()
                ->label(flexiblePagesTrans('menus.form.title_lbl'))
                ->helperText(flexiblePagesTrans('menus.form.title_help')),
        ];

        // Only show the style field if there are multiple styles available:
        if ($showStyleField) {
            $formFields[] = Select::make('style')
                ->label(flexiblePagesTrans('menus.form.style_lbl'))
                ->options(FilamentFlexibleContentBlockPages::config()->getMenuStyleOptions())
                ->default(FilamentFlexibleContentBlockPages::config()->getDefaultMenuStyle())
                ->required()
                ->helperText(flexiblePagesTrans('menus.form.style_help'));
        }

        $formFields[] = TextInput::make('max_depth')
            ->label(flexiblePagesTrans('menus.form.max_depth_lbl'))
            ->numeric()
            ->minValue(1)
            ->maxValue(10)
            ->placeholder((string) FilamentFlexibleContentBlockPages::config()->getMenuMaxDepth())
            ->helperText(flexiblePagesTrans('menus.form.max_depth_help'));

        return $schema
            ->components([
                Section::make(flexiblePagesTrans('menus.form.general_section'))
                    ->schema($formFields)
                    ->columns(2),
            ]);
    }
}
