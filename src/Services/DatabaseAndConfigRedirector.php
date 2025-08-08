<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Services;

use Illuminate\Support\Facades\Cache;
use Spatie\MissingPageRedirector\Redirector\Redirector;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Redirect;
use Symfony\Component\HttpFoundation\Request;

class DatabaseAndConfigRedirector implements Redirector
{
    const CACHE_REDIRECTS_KEY = 'filament-flexible-content-block-pages:redirects';

    /**
     * Returns a list of old and new urls with the status code if set compatible with spatie/laravel-missing-page-redirector,
     * that merges the redirects set in the database over the redirects set in the config.
     */
    public function getRedirectsFor(Request $request): array
    {
        // Get from the database and remember forever
        // we clear this on new model or updated model
        $dbRedirects = Cache::rememberForever(static::CACHE_REDIRECTS_KEY, function () {
            return Redirect::all()->flatMap(function (Redirect $redirect) {
                if ($redirect->status_code) {
                    return [
                        $redirect->old_url => [$redirect->new_url, $redirect->status_code],
                    ];
                } else {
                    return [$redirect->old_url => $redirect->new_url];
                }
            })->toArray();
        });

        // Get the redirects from the config
        $configRedirects = config('missing-page-redirector.redirects', []);

        // Merge both values
        return array_merge($configRedirects, $dbRedirects);
    }
}
