<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Cache;

use Closure;
use Illuminate\Support\Facades\Cache;

class TaggableCache
{
    public static function rememberForeverWithTag(string $tag, string $key, Closure $callback)
    {
        $cachedValue = Cache::rememberForever($key, $callback);

        // add key to tag cache
        $keysWithTag = Cache::get($tag, []);
        if (! in_array($key, $keysWithTag)) {
            $keysWithTag[] = $key;
            Cache::forever($tag, $keysWithTag);
        }

        return $cachedValue;
    }

    public static function flushTag(string $tag)
    {
        $taggedKeys = Cache::get($tag, []);
        foreach ($taggedKeys as $key) {
            Cache::forget($key);
        }

        Cache::forget($tag);
    }
}
