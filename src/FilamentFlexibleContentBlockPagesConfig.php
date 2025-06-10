<?php

namespace Statikbe\FilamentFlexibleContentBlockPages;

use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;

class FilamentFlexibleContentBlockPagesConfig
{
    private string $pageModel;

    public function __construct()
    {
        $this->pageModel = $this->getConfig('models.page', Page::class);
    }

    public function getPageModel(): Page
    {
        return app($this->pageModel);
    }

    public function getAuthorsTable(): string
    {
        return $this->getConfig('table_names.authors', 'users');
    }

    public function getPagesTable(): string
    {
        return $this->getConfig('table_names.pages', 'pages');
    }

    private function getConfig(string $configKey, $default = null): mixed
    {
        return config('filament-flexible-content-block-pages.'.$configKey);
    }
}
