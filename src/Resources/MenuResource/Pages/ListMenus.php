<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\MenuResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\MenuResource;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Actions\FlexibleLocaleSwitcher;

class ListMenus extends ListRecords
{
    use ListRecords\Concerns\Translatable;

    protected static string $resource = MenuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            FlexibleLocaleSwitcher::make(),
            CreateAction::make(),
        ];
    }
}
