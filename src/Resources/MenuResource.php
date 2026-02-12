<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\MenuResource\Pages\CreateMenu;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\MenuResource\Pages\EditMenu;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\MenuResource\Pages\ListMenus;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\MenuResource\Pages\ManageMenuItems;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\MenuResource\Schemas\MenuFormSchema;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\MenuResource\Schemas\MenuTableSchema;

class MenuResource extends Resource
{
    use Translatable;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBars3;

    protected static ?string $recordRouteKeyName = 'id';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModel(): string
    {
        return FilamentFlexibleContentBlockPages::config()->getMenuModel()::class;
    }

    public static function getLabel(): ?string
    {
        return flexiblePagesTrans('menus.lbl');
    }

    public static function getPluralLabel(): ?string
    {
        return flexiblePagesTrans('menus.plural_lbl');
    }

    public static function getNavigationGroup(): string
    {
        return flexiblePagesTrans('menus.nav_group');
    }

    public static function getNavigationSort(): ?int
    {
        return FilamentFlexibleContentBlockPages::config()->getMenuNavigationSort();
    }

    public static function form(Schema $schema): Schema
    {
        return MenuFormSchema::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return MenuTableSchema::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMenus::route('/'),
            'create' => CreateMenu::route('/create'),
            'edit' => EditMenu::route('/{record:id}/edit'),
            'items' => ManageMenuItems::route('/{record:id}/items'),
        ];
    }
}
