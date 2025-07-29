<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\MenuResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\MenuResource;

class ListMenus extends ListRecords
{
    protected static string $resource = MenuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}