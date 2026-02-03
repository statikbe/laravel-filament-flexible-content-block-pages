<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Http\Controllers;

use Artesaos\SEOTools\Facades\SEOMeta;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;

class PageController extends AbstractSeoPageController
{
    use ValidatesRequests;

    const TEMPLATE_PATH = 'filament-flexible-content-block-pages::%s.pages.index';

    public function index(Page $page)
    {
        // check if page is published:
        /** @var class-string|null $pageModel */
        $pageModel = FilamentFlexibleContentBlockPages::config()->getPageModel();
        $viewUnpublishedPagesGate = FilamentFlexibleContentBlockPages::config()->getViewUnpublishedPagesGate($pageModel);

        if (! Auth::user() || ! ($viewUnpublishedPagesGate && Gate::allows($viewUnpublishedPagesGate, $page))) {
            if (! $page->isPublished()) {
                SEOMeta::setRobots('noindex');
                abort(Response::HTTP_GONE);
            }
        }

        // SEO
        $this->setBasicSEO($page);
        $this->setSEOLocalisationAndCanonicalUrl();
        $this->setSEOImage($page);

        return view($this->getTemplatePath($page), [
            'page' => $page,
        ]);
    }

    public function homeIndex()
    {
        $page = FilamentFlexibleContentBlockPages::config()->getPageModel()::code(Page::HOME_PAGE)
            ->firstOrFail();

        return $this->index($page);
    }

    public function childIndex(Page $parent, Page $page)
    {
        // check if the page is a child of the parent
        if (! $parent->isParentOf($page)) {
            abort(Response::HTTP_NOT_FOUND);
        }

        // render the page with the regular page index function of the controller, or invoke the correct controller here:
        return $this->index($page);
    }

    public function grandchildIndex(Page $grandparent, Page $parent, Page $page)
    {
        // check if the page is a child of the parent
        if (! $parent->isParentOf($page) || ! $grandparent->isParentOf($parent)) {
            abort(Response::HTTP_NOT_FOUND);
        }

        // render the page with the regular page index function of the controller, or invoke the correct controller here:
        return $this->index($page);
    }

    private function getTemplatePath(Page $page)
    {
        // handle custom templates
        if ($page->code) {
            $customTemplate = FilamentFlexibleContentBlockPages::config()->getCustomPageTemplate($page->code);
            if ($customTemplate) {
                return $customTemplate;
            }
        }

        $theme = FilamentFlexibleContentBlockPages::config()->getTheme();

        return sprintf(self::TEMPLATE_PATH, $theme);
    }
}
