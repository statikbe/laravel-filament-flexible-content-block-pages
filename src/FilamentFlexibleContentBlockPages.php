<?php

namespace Statikbe\FilamentFlexibleContentBlockPages;

use Filament\Facades\Filament;
use Filament\Panel;
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
        $this->config()->getRouteHelper()->defineRoutes();
    }

    public function pageRoutes(): void
    {
        $this->config()->getRouteHelper()->definePageRoutes();
    }

    public function seoTagRoutes(): void
    {
        $this->config()->getRouteHelper()->defineSeoTagRoutes();
    }

    public function getUrl(Page $page, ?string $locale = null): string
    {
        return $this->config()->getRouteHelper()->getUrl($page, $locale);
    }

    public function settings(): Settings
    {
        return $this->config()->getSettingsModel()::getSettings();
    }

    /**
     * Looks up the resource class of a model in ANY panel.
     *
     * @param  string<class-string>  $modelClass
     * @return string<class-string>|null
     */
    public function getModelResource(string $modelClass): ?string
    {
        // 1. try the current panel:
        $resourceClass = Filament::getModelResource($modelClass);
        if ($resourceClass) {
            return $resourceClass;
        }

        // 2. try all other panels:
        foreach (Filament::getPanels() as $panel) {
            /** @var Panel $panel */
            $resourceClass = $panel->getModelResource($modelClass);

            if ($resourceClass) {
                return $resourceClass;
            }
        }

        return null;
    }
}
