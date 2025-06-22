<?php

use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;

function flexiblePagesTrans(string $translationKey, array $replace = [], ?string $locale = null): string
{
    return trans("filament-flexible-content-block-pages::filament-flexible-content-block-pages.$translationKey", $replace, $locale);
}

function flexiblePagesSetting(string $settingField, ?string $locale = null, $default = null): string|bool|null
{
    return FilamentFlexibleContentBlockPages::config()->getSettingsModel()::setting($settingField, $locale) ?? $default;
}

function flexiblePagesSettingImageUrl(string $imageCollection, ?string $imageConversion = null): ?string
{
    return FilamentFlexibleContentBlockPages::config()->getSettingsModel()::imageUrl($imageCollection, $imageConversion);
}
