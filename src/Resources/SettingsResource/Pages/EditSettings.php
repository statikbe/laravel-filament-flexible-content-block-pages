<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\SettingsResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesConfig;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Actions\FlexibleLocaleSwitcher;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Pages\EditRecord\Concerns\TranslatableWithMedia;

class EditSettings extends EditRecord
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
