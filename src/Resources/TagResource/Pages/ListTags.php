<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\TagResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Pages\ListRecords\Concerns\Translatable;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesConfig;

class ListTags extends ListRecords
{
    use Translatable;

    public static function getResource(): string
    {
        return FilamentFlexibleContentBlockPages::config()->getResources()[FilamentFlexibleContentBlockPagesConfig::TYPE_TAG];
    }

    protected function getActions(): array
    {
        return [
            // we cannot include a language switch because of an incompatibility between spatie/laravel-tags and
            // spatie/laravel-translatable. See: https://github.com/spatie/laravel-tags/issues/508
            CreateAction::make(),
        ];
    }

    public function isTableSearchable(): bool
    {
        return true;
    }
}
