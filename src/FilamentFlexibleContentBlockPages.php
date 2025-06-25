<?php

namespace Statikbe\FilamentFlexibleContentBlockPages;

use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Settings;

class FilamentFlexibleContentBlockPages
{
    public function config(): FilamentFlexibleContentBlockPagesConfig
    {
        return app(FilamentFlexibleContentBlockPagesConfig::class);
    }

    public function routes(): void
    {
        $this->config()->getRouteHelper()::defineRoutes();
    }

    public function getUrl(Page $page, ?string $locale = null): string
    {
        return $this->config()->getRouteHelper()::getUrl($page, $locale);
    }

    public function getSettings(): Settings
    {
        return $this->config()->getSettingsModel()::getSettings();
    }
}
