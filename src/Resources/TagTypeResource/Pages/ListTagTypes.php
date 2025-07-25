<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\TagTypeResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions\LocaleSwitcher;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Concerns\Translatable;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesConfig;

class ListTagTypes extends ListRecords
{
    use Translatable;

    public static function getResource(): string
    {
        return FilamentFlexibleContentBlockPages::config()->getResources()[FilamentFlexibleContentBlockPagesConfig::TYPE_TAG_TYPE];
    }

    protected function getActions(): array
    {
        return [
            LocaleSwitcher::make(),
            CreateAction::make(),
        ];
    }

    public function isTableSearchable(): bool
    {
        return true;
    }
}
