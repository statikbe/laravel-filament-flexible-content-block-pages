<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Models;

use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Observers\RedirectObserver;

/**
 * @property string $new_url
 * @property string $old_url
 * @property int $status_code
 *
 * @see RedirectObserver for clearing the redirect cache.
 */
#[ObservedBy(RedirectObserver::class)]
class Redirect extends Model
{
    use HasFactory;

    const CACHE_REDIRECTS_KEY = 'filament-flexible-content-block-pages:redirects';

    protected $guarded = ['id'];

    public function getTable()
    {
        return FilamentFlexibleContentBlockPages::config()->getRedirectsTable();
    }

    /**
     * Returns a list of old and new urls with the status code if set compatible with spatie/laravel-missing-page-redirector,
     * that merges the redirects set in the database over the redirects set in the config.
     */
    public static function getDirectionMap(): array
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
        $configRedirects = config('missing-page-redirector.redirects');

        // Merge both values
        return array_merge($configRedirects, $dbRedirects);
    }

    public function getMorphClass()
    {
        return 'filament-flexible-content-block-pages::redirect';
    }
}
