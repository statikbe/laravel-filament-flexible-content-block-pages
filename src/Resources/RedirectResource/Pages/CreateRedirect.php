<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\RedirectResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesConfig;

class CreateRedirect extends CreateRecord
{
    public static function getResource(): string
    {
        return FilamentFlexibleContentBlockPages::config()->getResources()[FilamentFlexibleContentBlockPagesConfig::TYPE_REDIRECT];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
