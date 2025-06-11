<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\PageResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions\LocaleSwitcher;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Concerns\Translatable;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\PageResource;

class ListPages extends ListRecords
{
    use Translatable;

    protected static string $resource = PageResource::class;

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
