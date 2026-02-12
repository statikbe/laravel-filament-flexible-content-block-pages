<?php

use Illuminate\Support\Facades\Cache;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Redirect;
use Statikbe\FilamentFlexibleContentBlockPages\Services\DatabaseAndConfigRedirector;

it('can create a redirect', function () {
    $redirect = Redirect::factory()->create([
        'old_url' => '/old-page',
        'new_url' => '/new-page',
        'status_code' => 301,
    ]);

    expect($redirect)->toBeInstanceOf(Redirect::class)
        ->and($redirect->old_url)->toBe('/old-page')
        ->and($redirect->new_url)->toBe('/new-page')
        ->and($redirect->status_code)->toBe(301);
});

it('supports different status codes', function () {
    $permanent = Redirect::factory()->permanent()->create();
    $temporary = Redirect::factory()->temporary()->create();
    $code307 = Redirect::factory()->withStatusCode(307)->create();
    $code308 = Redirect::factory()->withStatusCode(308)->create();

    expect($permanent->status_code)->toBe(301)
        ->and($temporary->status_code)->toBe(302)
        ->and($code307->status_code)->toBe(307)
        ->and($code308->status_code)->toBe(308);
});

it('can use from and to factory methods', function () {
    $redirect = Redirect::factory()
        ->from('/source-path')
        ->to('/destination-path')
        ->create();

    expect($redirect->old_url)->toBe('/source-path')
        ->and($redirect->new_url)->toBe('/destination-path');
});

it('clears cache when created', function () {
    Cache::shouldReceive('forget')
        ->once()
        ->with(DatabaseAndConfigRedirector::CACHE_REDIRECTS_KEY);

    Redirect::factory()->create();
});

it('clears cache when updated', function () {
    // Create without cache expectations
    $redirect = Redirect::factory()->create();

    Cache::shouldReceive('forget')
        ->once()
        ->with(DatabaseAndConfigRedirector::CACHE_REDIRECTS_KEY);

    $redirect->update(['new_url' => '/updated-url']);
});

it('clears cache when deleted', function () {
    $redirect = Redirect::factory()->create();

    Cache::shouldReceive('forget')
        ->once()
        ->with(DatabaseAndConfigRedirector::CACHE_REDIRECTS_KEY);

    $redirect->delete();
});

it('returns correct morph class', function () {
    $redirect = Redirect::factory()->create();

    expect($redirect->getMorphClass())->toBe('filament-flexible-content-block-pages::redirect');
});
