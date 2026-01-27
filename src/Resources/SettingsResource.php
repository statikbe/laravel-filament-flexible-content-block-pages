<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Settings;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\SettingsResource\Pages\CreateSettings;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\SettingsResource\Pages\EditSettings;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\SettingsResource\Pages\ListSettings;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\SettingsResource\Schemas\SettingsFormSchema;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\SettingsResource\Schemas\SettingsTableSchema;

class SettingsResource extends Resource
{
    use Translatable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-vertical';

    /**
     * @return class-string
     */
    public static function getModel(): string
    {
        return FilamentFlexibleContentBlockPages::config()->getSettingsModel()::class;
    }

    public static function getLabel(): ?string
    {
        return flexiblePagesTrans('settings.settings_lbl');
    }

    public static function getPluralLabel(): ?string
    {
        return flexiblePagesTrans('settings.settings_plural_lbl');
    }

    public static function getNavigationGroup(): ?string
    {
        return flexiblePagesTrans('settings.navigation_group');
    }

    public static function getNavigationSort(): ?int
    {
        return FilamentFlexibleContentBlockPages::config()->getSettingsNavigationSort();
    }

    public static function form(Schema $schema): Schema
    {
        return SettingsFormSchema::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return SettingsTableSchema::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSettings::route('/'),
            'create' => CreateSettings::route('/create'),
            'edit' => EditSettings::route('/{record}/edit'),
        ];
    }

    public static function getNavigationUrl(): string
    {
        // do not show the table, since we will only have one record:
        /** @var Settings|null $settings */
        $settings = static::getModel()::first();
        if ($settings) {
            return static::getUrl('edit', ['record' => $settings]);
        } else {
            return static::getUrl('create');
        }
    }
}
