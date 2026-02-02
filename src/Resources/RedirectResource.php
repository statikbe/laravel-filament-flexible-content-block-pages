<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\RedirectResource\Pages\CreateRedirect;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\RedirectResource\Pages\EditRedirect;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\RedirectResource\Pages\ListRedirects;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\RedirectResource\Schemas\RedirectFormSchema;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\RedirectResource\Schemas\RedirectTableSchema;

class RedirectResource extends Resource
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBolt;

    public static function getModel(): string
    {
        return FilamentFlexibleContentBlockPages::config()->getRedirectModel()::class;
    }

    public static function getLabel(): ?string
    {
        return flexiblePagesTrans('redirects.redirects_lbl');
    }

    public static function getPluralLabel(): ?string
    {
        return flexiblePagesTrans('redirects.redirects_plural_lbl');
    }

    public static function getNavigationGroup(): string
    {
        return flexiblePagesTrans('redirects.nav_group');
    }

    public static function getNavigationSort(): ?int
    {
        return FilamentFlexibleContentBlockPages::config()->getRedirectNavigationSort();
    }

    public static function form(Schema $schema): Schema
    {
        return RedirectFormSchema::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RedirectTableSchema::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRedirects::route('/'),
            'create' => CreateRedirect::route('/create'),
            'edit' => EditRedirect::route('/{record}/edit'),
        ];
    }
}
