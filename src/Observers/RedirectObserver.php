<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Observers;

use Illuminate\Support\Facades\Cache;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Redirect;
use Statikbe\FilamentFlexibleContentBlockPages\Services\DatabaseAndConfigRedirector;

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
        $this->clearRedirectCache();
    }

    /**
     * Handle the Redirect "updated" event.
     */
    public function updated(Redirect $redirect): void
    {
        $this->clearRedirectCache();
    }

    /**
     * Handle the Redirect "deleted" event.
     */
    public function deleted(Redirect $redirect): void
    {
        $this->clearRedirectCache();
    }

    /**
     * Handle the Redirect "restored" event.
     */
    public function restored(Redirect $redirect): void
    {
        $this->clearRedirectCache();
    }

    /**
     * Handle the Redirect "force deleted" event.
     */
    public function forceDeleted(Redirect $redirect): void
    {
        $this->clearRedirectCache();
    }

    private function clearRedirectCache(): void
    {
        Cache::forget(DatabaseAndConfigRedirector::CACHE_REDIRECTS_KEY);
    }
}
