<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\TagResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesConfig;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Actions\FlexibleLocaleSwitcher;

class CreateTag extends CreateRecord
{
    use CreateRecord\Concerns\Translatable;

    public static function getResource(): string
    {
        return FilamentFlexibleContentBlockPages::config()->getResources()[FilamentFlexibleContentBlockPagesConfig::TYPE_TAG];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getActions(): array
    {
        return [
            FlexibleLocaleSwitcher::make(),
        ];
    }
}
