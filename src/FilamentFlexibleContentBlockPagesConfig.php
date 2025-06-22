<?php

namespace Statikbe\FilamentFlexibleContentBlockPages;

use Filament\Resources\Resource;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Redirect;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Settings;

class FilamentFlexibleContentBlockPagesConfig
{
    const TYPE_PAGE = 'pages';

    const TYPE_REDIRECT = 'redirects';

    const TYPE_SETTINGS = 'settings';

    const TYPE_AUTHOR = 'authors';

    private string $pageModel;

    private string $redirectModel;

    private string $settingsModel;

    public function __construct()
    {
        $this->pageModel = $this->packageConfig('models.page', Page::class);
        $this->redirectModel = $this->packageConfig('models.redirect', \Statikbe\FilamentFlexibleContentBlockPages\Models\Redirect::class);
        $this->settingsModel = $this->packageConfig('models.settings', \Statikbe\FilamentFlexibleContentBlockPages\Models\Settings::class);
    }

    public function getPageModel(): Page
    {
        return app($this->pageModel);
    }

    public function getRedirectModel(): Redirect
    {
        return app($this->redirectModel);
    }

    public function getSettingsModel(): Settings
    {
        return app($this->settingsModel);
    }

    public function getAuthorsTable(): string
    {
        return $this->packageConfig('table_names.authors', 'users');
    }

    public function getPagesTable(): string
    {
        return $this->packageConfig('table_names.pages', 'pages');
    }

    public function getRedirectsTable(): string
    {
        return $this->packageConfig('table_names.redirects', 'redirects');
    }

    public function getSettingsTable(): string
    {
        return $this->packageConfig('table_names.settings', 'settings');
    }

    /**
     * @return array<class-string<resource>>
     */
    public function getResources(): array
    {
        return $this->packageConfig('resources');
    }

    public function getPanelPath(): string
    {
        return $this->packageConfig('panel.path', 'content');
    }

    public function getSEODefaultCanonicalLocale(): string
    {
        return $this->packageConfig('seo.default_canonical_locale', 'en');
    }

    private function packageConfig(string $configKey, $default = null): mixed
    {
        return config('filament-flexible-content-block-pages.'.$configKey);
    }
}
