<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Observers;

use Illuminate\Support\Facades\Cache;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Redirect;

/**
 * Clear redirect cache if a redirect changes
 */
class RedirectObserver
{
    /**
     * Handle the Redirect "created" event.
     */
    public function created(Redirect $redirect): void
    {
        Cache::forget(Redirect::CACHE_REDIRECTS_KEY);
    }

    /**
     * Handle the Redirect "updated" event.
     */
    public function updated(Redirect $redirect): void
    {
        Cache::forget(Redirect::CACHE_REDIRECTS_KEY);
    }

    /**
     * Handle the Redirect "deleted" event.
     */
    public function deleted(Redirect $redirect): void
    {
        Cache::forget(Redirect::CACHE_REDIRECTS_KEY);
    }

    /**
     * Handle the Redirect "restored" event.
     */
    public function restored(Redirect $redirect): void
    {
        Cache::forget(Redirect::CACHE_REDIRECTS_KEY);
    }

    /**
     * Handle the Redirect "force deleted" event.
     */
    public function forceDeleted(Redirect $redirect): void
    {
        Cache::forget(Redirect::CACHE_REDIRECTS_KEY);
    }
}
