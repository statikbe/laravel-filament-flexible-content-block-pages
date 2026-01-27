<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Guava\IconPicker\Forms\Components\IconPicker;
use Guava\IconPicker\Tables\Columns\IconColumn;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesConfig;
use Statikbe\FilamentFlexibleContentBlockPages\Form\Components\NameField;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\TagTypeResource\Pages\CreateTagType;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\TagTypeResource\Pages\EditTagType;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\TagTypeResource\Pages\ListTagTypes;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Form\Fields\CodeField;
use Statikbe\FilamentFlexibleContentBlocks\FilamentFlexibleBlocksConfig;

class TagTypeResource extends Resource
{
    use Translatable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-hashtag';

    protected static ?string $recordTitleAttribute = 'name';

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

    public static function getLabel(): ?string
    {
        return flexiblePagesTrans('tag_types.tag_type_lbl');
    }

    public static function getModelLabel(): string
    {
        return flexiblePagesTrans('tag_types.tag_type_lbl');
    }

    public static function getPluralModelLabel(): string
    {
        return flexiblePagesTrans('tag_types.tag_type_plural_lbl');
    }

    public static function getNavigationParentItem(): ?string
    {
        return FilamentFlexibleContentBlockPages::config()->getResources()[FilamentFlexibleContentBlockPagesConfig::TYPE_TAG]::getNavigationLabel();
    }

    public static function getNavigationSort(): ?int
    {
        return 3;
    }

    public static function form(Schema $schema): Schema
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(flexiblePagesTrans('form_component.name_lbl')),
                ColorColumn::make('colour')
                    ->label(flexiblePagesTrans('form_component.colour_lbl')),
                ToggleColumn::make('is_default_type')
                    ->label(flexiblePagesTrans('tag_types.tag_type_is_default_type_lbl')),
                IconColumn::make('icon')
                    ->label(flexiblePagesTrans('form_component.icon_lbl')),
                TextColumn::make('created_at')
                    ->label(flexiblePagesTrans('menu_items.table.created_at_col'))
                    ->dateTime(FilamentFlexibleBlocksConfig::getPublishingDateFormatting())
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(flexiblePagesTrans('menu_items.table.updated_at_col'))
                    ->dateTime(FilamentFlexibleBlocksConfig::getPublishingDateFormatting())
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
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
