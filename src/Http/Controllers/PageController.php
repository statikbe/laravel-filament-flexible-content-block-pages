<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Http\Controllers;

use Artesaos\SEOTools\Facades\SEOMeta;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\HasPageAttributes;

class PageController extends AbstractSeoPageController
{
    use ValidatesRequests;

    const TEMPLATE_PATH = 'filament-flexible-content-block-pages::%s.pages.index';

    public function index(Page $page)
    {
        // If the page has a parent, it should be accessed via the parent or grandparent route instead.
        if ($page->hasParent()) {
            return redirect($page->getViewUrl(), Response::HTTP_MOVED_PERMANENTLY);
        }

        return $this->renderPage($page);
    }

    public function homeIndex()
    {
        // fetch cached home page:
        $page = FilamentFlexibleContentBlockPages::config()->getPageModel()::getByCode(Page::HOME_PAGE);

        if (! $page) {
            abort(Response::HTTP_NOT_FOUND);
        }

        return $this->renderPage($page);
    }

    public function childIndex(Page $parent, Page $page)
    {
        // check if the page is a child of the parent
        if (! $parent->isParentOf($page)) {
            abort(Response::HTTP_NOT_FOUND);
        }

        // redirect to canonical URL if the parent has a parent (page is a grandchild)
        if ($parent->hasParent()) {
            return redirect($page->getViewUrl(), Response::HTTP_MOVED_PERMANENTLY);
        }

        return $this->renderPage($page);
    }

    public function grandchildIndex(Page $grandparent, Page $parent, Page $page)
    {
        // check if the page is a child of the parent
        if (! $parent->isParentOf($page) || ! $grandparent->isParentOf($parent)) {
            abort(Response::HTTP_NOT_FOUND);
        }

        return $this->renderPage($page);
    }

    protected function renderPage(Page $page)
    {
        // check if this page is published:
        $this->abortIfUnpublished($page);

        // SEO
        $this->setBasicSEO($page);
        $this->setSEOLocalisationAndCanonicalUrl();
        $this->setSEOImage($page);

        return view($this->getTemplatePath($page), [
            'page' => $page,
        ]);
    }

    protected function abortIfUnpublished(HasPageAttributes $page)
    {
        /** @var class-string|null $pageModel */
        $pageModel = FilamentFlexibleContentBlockPages::config()->getPageModel();
        $viewUnpublishedPagesGate = FilamentFlexibleContentBlockPages::config()->getViewUnpublishedPagesGate($pageModel);

        if (! Auth::user() || ! ($viewUnpublishedPagesGate && Gate::allows($viewUnpublishedPagesGate, $page))) {
            if (! $page->isPublished()) {
                SEOMeta::setRobots('noindex');
                abort(Response::HTTP_GONE);
            }
        }
    }

    protected function getTemplatePath(Page $page)
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
