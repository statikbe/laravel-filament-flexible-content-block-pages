<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Cache;

use Closure;
use Illuminate\Cache\TaggableStore;
use Illuminate\Support\Facades\Cache;

class TaggableCache
{
    public static function rememberForeverWithTag(string $tag, string $key, Closure $callback): mixed
    {
        if (static::storeSupportsTagging()) {
            return Cache::tags([$tag])->rememberForever($key, $callback);
        }

        $cachedValue = Cache::rememberForever($key, $callback);

        // add key to tag index for manual tracking
        $keysWithTag = Cache::get($tag, []);
        if (! in_array($key, $keysWithTag)) {
            $keysWithTag[] = $key;
            Cache::forever($tag, $keysWithTag);
        }

        return $cachedValue;
    }

    public static function flushTag(string $tag): void
    {
        if (static::storeSupportsTagging()) {
            Cache::tags([$tag])->flush();

            return;
        }

        $taggedKeys = Cache::get($tag, []);
        foreach ($taggedKeys as $key) {
            Cache::forget($key);
        }

        Cache::forget($tag);
    }

    public static function storeSupportsTagging(): bool
    {
        return Cache::getStore() instanceof TaggableStore;
    }
}
