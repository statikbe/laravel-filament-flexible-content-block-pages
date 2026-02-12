<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Listeners;

use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
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

                    try {
                        DB::beginTransaction();

                        // clean up old redirects & avoid circular references:
                        // Rule 1: Delete records where $newUrlPath matches existing old_url.
                        // This means that the starting point of the redirect, has been recreated.
                        Redirect::where('old_url', $newUrlPath)->delete();

                        // Rule 2: Update records where $oldUrlPath matches existing new_url.
                        // This means that old slug, was already the destination of an existing redirect, so to avoid
                        // hopping multiple redirects, we update the existing redirect to the new slug.
                        Redirect::where('new_url', $oldUrlPath)->update(['new_url' => $newUrlPath]);

                        // Rule 3: Delete self-redirects
                        // Maybe we have created cases were the source and destination is the same.
                        Redirect::whereColumn('old_url', 'new_url')->delete();

                        // Rule 4: Add a new redirect if it doesn't exist yet
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

                        DB::commit();
                    } catch (Exception $e) {
                        report($e);
                        DB::rollBack();
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
