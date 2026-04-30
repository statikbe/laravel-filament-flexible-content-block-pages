<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Components;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\Component;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesConfig;
use Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesServiceProvider;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;

class EditButton extends Component
{
    public string $editUrl;

    public function __construct(
        public Page $page,
    ) {
        $pageResource = FilamentFlexibleContentBlockPages::config()->getResources()[FilamentFlexibleContentBlockPagesConfig::TYPE_PAGE];
        $this->editUrl = $pageResource::getUrl('edit', ['record' => $this->page]);
    }

    public function shouldRender(): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        if (! FilamentFlexibleContentBlockPages::config()->isEditButtonEnabled($this->page::class)) {
            return false;
        }

        $gate = FilamentFlexibleContentBlockPages::config()->getEditButtonGate($this->page::class);

        if (! $gate) {
            return false;
        }

        return Gate::allows($gate, $this->page);
    }

    public function render()
    {
        $theme = FilamentFlexibleContentBlockPages::config()->getTheme();
        $package = FilamentFlexibleContentBlockPagesServiceProvider::PACKAGE_PREFIX;
        $template = "{$package}::{$theme}.components.edit-button";

        if (view()->exists($template)) {
            return view($template);
        }

        /** @var view-string $fallbackTemplate */
        $fallbackTemplate = "{$package}::tailwind.components.edit-button";

        return view($fallbackTemplate);
    }
}
