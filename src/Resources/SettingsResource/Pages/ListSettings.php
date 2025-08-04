<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\SettingsResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesConfig;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Actions\FlexibleLocaleSwitcher;

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
            FlexibleLocaleSwitcher::make(),
            CreateAction::make()
                // only show when no settings are created yet
                ->hidden(FilamentFlexibleContentBlockPages::config()->getSettingsModel()::exists()),
        ];
    }
}
