<?php

namespace Statikbe\FilamentFlexibleContentBlockPages;

use Filament\Resources\Resource;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Redirect;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Settings;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Tag;
use Statikbe\FilamentFlexibleContentBlockPages\Models\TagType;
use Statikbe\FilamentFlexibleContentBlockPages\Routes\Contracts\HandlesPageRoutes;
use Statikbe\FilamentFlexibleContentBlockPages\Routes\LocalisedPageRouteHelper;
use Statikbe\FilamentFlexibleContentBlocks\FilamentFlexibleContentBlocksServiceProvider;

class FilamentFlexibleContentBlockPagesConfig
{
    const TYPE_PAGE = 'pages';

    const TYPE_REDIRECT = 'redirects';

    const TYPE_SETTINGS = 'settings';

    const TYPE_AUTHOR = 'authors';

    const TYPE_TAG = 'tags';

    const TYPE_TAG_TYPE = 'tag_types';

    const TYPE_TAGGABLE = 'taggables';

    private string $pageModel;

    private string $redirectModel;

    private string $settingsModel;

    private string $tagModel;

    private string $tagTypeModel;

    private HandlesPageRoutes $routeHelper;

    public function __construct()
    {
        $this->pageModel = $this->packageConfig('models.'.static::TYPE_PAGE, Page::class);
        $this->redirectModel = $this->packageConfig('models.'.static::TYPE_REDIRECT, Redirect::class);
        $this->settingsModel = $this->packageConfig('models.'.static::TYPE_SETTINGS, Settings::class);
        $this->tagModel = $this->packageConfig('models.'.static::TYPE_TAG, Tag::class);
        $this->tagTypeModel = $this->packageConfig('models.'.static::TYPE_TAG_TYPE, TagType::class);

    }

    public function getSupportedLocales(): array
    {
        return config(
            FilamentFlexibleContentBlocksServiceProvider::$name.'.supported_locales',
            config('app.supported_locales', ['en'])
        );
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

    public function getTagModel(): Tag
    {
        return app($this->tagModel);
    }

    public function getTagTypeModel(): TagType
    {
        return app($this->tagTypeModel);
    }

    public function getAuthorsTable(): string
    {
        return $this->packageConfig('table_names.'.static::TYPE_AUTHOR, 'users');
    }

    public function getPagesTable(): string
    {
        return $this->packageConfig('table_names.'.static::TYPE_PAGE, 'pages');
    }

    public function getRedirectsTable(): string
    {
        return $this->packageConfig('table_names.'.static::TYPE_REDIRECT, 'redirects');
    }

    public function getSettingsTable(): string
    {
        return $this->packageConfig('table_names.'.static::TYPE_SETTINGS, 'settings');
    }

    public function getTagsTable(): string
    {
        return $this->packageConfig('table_names.'.static::TYPE_TAG, 'tags');
    }

    public function getTagTypesTable(): string
    {
        return $this->packageConfig('table_names.'.static::TYPE_TAG_TYPE, 'tag_types');
    }

    public function getTaggablesTable(): string
    {
        return $this->packageConfig('table_names.'.static::TYPE_TAGGABLE, 'taggables');
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

    public function getPanelMiddleware(): array
    {
        return $this->packageConfig('panel.middleware', []);
    }

    public function getPanelAuthMiddleware(): array
    {
        return $this->packageConfig('panel.auth_middleware', []);
    }

    public function getSEODefaultCanonicalLocale(): string
    {
        return $this->packageConfig('seo.default_canonical_locale', 'en');
    }

    public function getRouteHelper(): HandlesPageRoutes
    {
        if (! isset($this->routeHelper)) {
            $this->routeHelper = app($this->packageConfig('route_helper', LocalisedPageRouteHelper::class));
        }

        return $this->routeHelper;
    }

    private function packageConfig(string $configKey, $default = null): mixed
    {
        return config('filament-flexible-content-block-pages.'.$configKey);
    }
}
