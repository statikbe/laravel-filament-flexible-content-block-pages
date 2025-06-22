<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\PageResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\LocaleSwitcher;
use Filament\Resources\Pages\EditRecord;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesConfig;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Pages\EditRecord\Concerns\TranslatableWithMedia;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Resource\Pages\Actions\CopyContentBlocksToLocalesAction;

class EditPage extends EditRecord
{
    use TranslatableWithMedia;

    public static function getResource(): string
    {
        return FilamentFlexibleContentBlockPages::config()->getResources()[FilamentFlexibleContentBlockPagesConfig::TYPE_PAGE];
    }

    protected function getActions(): array
    {
        return [
            CopyContentBlocksToLocalesAction::make(),
            LocaleSwitcher::make(),
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
