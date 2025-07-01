<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Observers;

use Statikbe\FilamentFlexibleContentBlockPages\Cache\TaggableCache;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Settings;

class SettingsObserver
{
    /**
     * Handle the Settings "created" event.
     */
    public function created(Settings $settings): void
    {
        TaggableCache::flushTag(Settings::CACHE_TAG_SETTINGS);
    }

    /**
     * Handle the Settings "updated" event.
     */
    public function updated(Settings $settings): void
    {
        TaggableCache::flushTag(Settings::CACHE_TAG_SETTINGS);
    }

    /**
     * Handle the Settings "deleted" event.
     */
    public function deleted(Settings $settings): void
    {
        TaggableCache::flushTag(Settings::CACHE_TAG_SETTINGS);
    }

    /**
     * Handle the Settings "restored" event.
     */
    public function restored(Settings $settings): void
    {
        TaggableCache::flushTag(Settings::CACHE_TAG_SETTINGS);
    }

    /**
     * Handle the Settings "force deleted" event.
     */
    public function forceDeleted(Settings $settings): void
    {
        TaggableCache::flushTag(Settings::CACHE_TAG_SETTINGS);
    }
}
