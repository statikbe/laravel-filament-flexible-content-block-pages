<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\SettingsResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions\LocaleSwitcher;
use Filament\Resources\Pages\ListRecords;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesConfig;

class ListSettings extends ListRecords
{
    use ListRecords\Concerns\Translatable;

    public static function getResource(): string
    {
        return FilamentFlexibleContentBlockPages::config()->getResources()[FilamentFlexibleContentBlockPagesConfig::TYPE_SETTINGS];
    }

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
            CreateAction::make()
                // only show when no settings are created yet
                ->hidden(FilamentFlexibleContentBlockPages::config()->getSettingsModel()::exists()),
        ];
    }
}
