<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\RedirectResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesConfig;

class EditRedirect extends EditRecord
{
    public static function getResource(): string
    {
        return FilamentFlexibleContentBlockPages::config()->getResources()[FilamentFlexibleContentBlockPagesConfig::TYPE_REDIRECT];
    }

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
