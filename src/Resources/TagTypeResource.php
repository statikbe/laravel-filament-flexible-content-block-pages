<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources;

use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Guava\FilamentIconPicker\Forms\IconPicker;
use Guava\FilamentIconPicker\Tables\IconColumn;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Form\Components\NameField;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\TagTypeResource\Pages\CreateTagType;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\TagTypeResource\Pages\EditTagType;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\TagTypeResource\Pages\ListTagTypes;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Form\Fields\CodeField;

class TagTypeResource extends Resource
{
    use Translatable;

    protected static ?string $navigationIcon = 'heroicon-o-hashtag';

    /**
     * @return class-string
     */
    public static function getModel(): string
    {
        return FilamentFlexibleContentBlockPages::config()->getTagTypeModel()::class;
    }

    public static function getNavigationGroup(): ?string
    {
        return flexiblePagesTrans('tag_types.navigation_group');
    }

    public static function getNavigationLabel(): string
    {
        return flexiblePagesTrans('tag_types.tag_type_plural_lbl');
    }

    public static function getModelLabel(): string
    {
        return flexiblePagesTrans('tag_types.tag_type_lbl');
    }

    public static function getPluralModelLabel(): string
    {
        return flexiblePagesTrans('tag_types.tag_type_plural_lbl');
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make([
                    NameField::create(true),
                    CodeField::create(),
                    Toggle::make('is_default_type')
                        ->label(flexiblePagesTrans('tag_types.tag_type_is_default_type_lbl'))
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(flexiblePagesTrans('form_component.name_lbl')),
                ColorColumn::make('colour')
                    ->label(flexiblePagesTrans('form_component.colour_lbl')),
                ToggleColumn::make('is_initial_status')
                    ->label(flexiblePagesTrans('tag_types.tag_type_is_default_type_lbl')),
                IconColumn::make('icon')
                    ->label(flexiblePagesTrans('form_component.icon_lbl')),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTagTypes::route('/'),
            'create' => CreateTagType::route('/create'),
            'edit' => EditTagType::route('/{record:code}/edit'),
        ];
    }
}
