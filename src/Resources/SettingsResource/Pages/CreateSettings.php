<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\SettingsResource\Pages;

use Filament\Actions\LocaleSwitcher;
use Filament\Resources\Pages\CreateRecord;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesConfig;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Actions\FlexibleLocaleSwitcher;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Pages\CreateRecord\Concerns\TranslatableWithMedia;

class CreateSettings extends CreateRecord
{
    use TranslatableWithMedia;

    public static function getResource(): string
    {
        return FilamentFlexibleContentBlockPages::config()->getResources()[FilamentFlexibleContentBlockPagesConfig::TYPE_SETTINGS];
    }

    protected function getHeaderActions(): array
    {
        return [
            FlexibleLocaleSwitcher::make(),
        ];
    }
}
