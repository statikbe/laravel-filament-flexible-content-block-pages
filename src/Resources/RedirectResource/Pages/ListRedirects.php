<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\RedirectResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesConfig;

class ListRedirects extends ListRecords
{
    public static function getResource(): string
    {
        return FilamentFlexibleContentBlockPages::config()->getResources()[FilamentFlexibleContentBlockPagesConfig::TYPE_REDIRECT];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
