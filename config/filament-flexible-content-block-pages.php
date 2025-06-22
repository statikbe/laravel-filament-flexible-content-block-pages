<?php

// config for Statikbe/FilamentFlexibleContentBlockPages
use Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesConfig;

return [
    'models' => [
        'page' => \Statikbe\FilamentFlexibleContentBlockPages\Models\Page::class,
        'redirect' => \Statikbe\FilamentFlexibleContentBlockPages\Models\Redirect::class,
        'settings' => \Statikbe\FilamentFlexibleContentBlockPages\Models\Settings::class,
    ],

    'table_names' => [
        FilamentFlexibleContentBlockPagesConfig::TYPE_PAGE => 'pages',
        FilamentFlexibleContentBlockPagesConfig::TYPE_AUTHOR => 'users',
        FilamentFlexibleContentBlockPagesConfig::TYPE_SETTINGS => 'settings',
        FilamentFlexibleContentBlockPagesConfig::TYPE_REDIRECT => 'redirects',
    ],

    'resources' => [
        FilamentFlexibleContentBlockPagesConfig::TYPE_PAGE => \Statikbe\FilamentFlexibleContentBlockPages\Resources\PageResource::class,
        FilamentFlexibleContentBlockPagesConfig::TYPE_SETTINGS => \Statikbe\FilamentFlexibleContentBlockPages\Resources\SettingsResource::class,
        FilamentFlexibleContentBlockPagesConfig::TYPE_REDIRECT => \Statikbe\FilamentFlexibleContentBlockPages\Resources\RedirectResource::class,
    ],

    'panel' => [
        'path' => 'content',
    ],

    'seo' => [
        'default_canonical_locale' => 'nl',
    ],

];
