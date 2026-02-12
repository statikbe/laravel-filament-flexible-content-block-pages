<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\TagResource\Pages\CreateTag;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\TagResource\Pages\EditTag;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\TagResource\Pages\ListTags;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\TagResource\Schemas\TagFormSchema;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\TagResource\Schemas\TagTableSchema;

class TagResource extends Resource
{
    use Translatable;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $recordRouteKeyName = 'id';

    protected static ?string $recordTitleAttribute = 'name';

    /**
     * @return class-string
     */
    public static function getModel(): string
    {
        return FilamentFlexibleContentBlockPages::config()->getTagModel()::class;
    }

    public static function getNavigationGroup(): ?string
    {
        return flexiblePagesTrans('tags.navigation_group');
    }

    public static function getLabel(): ?string
    {
        return flexiblePagesTrans('tags.tag_lbl');
    }

    public static function getModelLabel(): string
    {
        return flexiblePagesTrans('tags.tag_lbl');
    }

    public static function getPluralModelLabel(): string
    {
        return flexiblePagesTrans('tags.tag_plural_lbl');
    }

    public static function getNavigationSort(): ?int
    {
        return FilamentFlexibleContentBlockPages::config()->getTagNavigationSort();
    }

    public static function form(Schema $schema): Schema
    {
        return TagFormSchema::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TagTableSchema::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTags::route('/'),
            'create' => CreateTag::route('/create'),
            'edit' => EditTag::route('/{record:id}/edit'),
        ];
    }
}
