<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Listeners;

use Illuminate\Http\Response;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Redirect;
use Statikbe\FilamentFlexibleContentBlocks\Events\SlugChanged;

/**
 * Create a redirect when a slug of a page has changed after creation.
 */
class SlugChangedListener
{
    public function handle(SlugChanged $event): void
    {
        // add redirect:
        if ($event->recordWasPublished) {
            foreach ($event->changedSlugs as $changedSlug) {
                /** @var array{ locale: string, oldSlug: ?string, newSlug: ?string } $changedSlug */
                $oldUrl = null;
                $newUrl = null;

                if ($changedSlug['newSlug'] && ! empty(trim($changedSlug['newSlug']))) {
                    if ($event->record instanceof Page) {

                        $oldUrl = $this->getUrl($event->record, $changedSlug['locale'], $changedSlug['oldSlug']);
                        $newUrl = $this->getUrl($event->record, $changedSlug['locale'], $changedSlug['newSlug']);
                    }
                }

                if ($newUrl && $oldUrl) {
                    $oldUrlPath = parse_url($oldUrl, PHP_URL_PATH);
                    $newUrlPath = parse_url($newUrl, PHP_URL_PATH);

                    $redirectExists = Redirect::where('old_url', $oldUrlPath)
                        ->where('new_url', $newUrlPath)
                        ->exists();

                    if (! $redirectExists) {
                        $redirect = new Redirect;
                        $redirect->old_url = $oldUrlPath;
                        $redirect->new_url = $newUrlPath;
                        $redirect->status_code = Response::HTTP_MOVED_PERMANENTLY;
                        $redirect->save();
                    }
                }
            }
        }
    }

    private function getUrl(Page $page, string $locale, string $slug): string
    {
        $currentSlug = $page->getTranslation('slug', $locale);
        $page->setTranslation('slug', $locale, $slug);
        $url = $page->getViewUrl($locale);
        $page->setTranslation('slug', $locale, $currentSlug);

        return $url;
    }
}
