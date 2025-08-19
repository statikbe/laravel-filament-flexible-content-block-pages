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

    protected $guarded = ['id'];

    public function getTable()
    {
        return FilamentFlexibleContentBlockPages::config()->getRedirectsTable();
    }

    public function getMorphClass()
    {
        return flexiblePagesPrefix('redirect');
    }
}
