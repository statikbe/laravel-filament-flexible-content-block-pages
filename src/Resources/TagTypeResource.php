<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesConfig;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\TagTypeResource\Pages\CreateTagType;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\TagTypeResource\Pages\EditTagType;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\TagTypeResource\Pages\ListTagTypes;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\TagTypeResource\Schemas\TagTypeFormSchema;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\TagTypeResource\Schemas\TagTypeTableSchema;

class TagTypeResource extends Resource
{
    use Translatable;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedHashtag;

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
        return TagTypeFormSchema::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TagTypeTableSchema::configure($table);
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
