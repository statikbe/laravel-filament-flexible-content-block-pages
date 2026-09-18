<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Observers;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Statikbe\FilamentFlexibleContentBlockPages\Cache\TaggableCache;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Settings;

/**
 * Flushes the settings cache when the media of the settings changes.
 * Media uploads do not change any attribute of the settings model, so the model is not dirty and its updated event
 * is never fired. Without this observer the cached image URLs would never be refreshed.
 */
class SettingsMediaObserver
{
    public function saved(Media $media): void
    {
        $this->flushSettingsCache($media);
    }

    public function deleted(Media $media): void
    {
        $this->flushSettingsCache($media);
    }

    private function flushSettingsCache(Media $media): void
    {
        if ($this->isSettingsMedia($media)) {
            TaggableCache::flushTag(Settings::CACHE_TAG_SETTINGS);
        }
    }

    private function isSettingsMedia(Media $media): bool
    {
        $settingsModel = FilamentFlexibleContentBlockPages::config()->getSettingsModel();

        // the model type is the morph alias, unless the morph map is not registered:
        return in_array($media->model_type, [$settingsModel->getMorphClass(), $settingsModel::class], true);
    }
}
