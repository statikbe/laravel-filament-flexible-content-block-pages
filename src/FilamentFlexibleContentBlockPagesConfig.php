<?php

namespace Statikbe\FilamentFlexibleContentBlockPages;

use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;

class FilamentFlexibleContentBlockPagesConfig
{
    private string $pageModel;

    public function __construct()
    {
        $this->pageModel = $this->packageConfig('models.page', Page::class);
    }

    public function getPageModel(): Page
    {
        return app($this->pageModel);
    }

    public function getAuthorsTable(): string
    {
        return $this->packageConfig('table_names.authors', 'users');
    }

    public function getPagesTable(): string
    {
        return $this->packageConfig('table_names.pages', 'pages');
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

    private function packageConfig(string $configKey, $default = null): mixed
    {
        return config('filament-flexible-content-block-pages.'.$configKey);
    }
}
