<?php

// config for Statikbe/FilamentFlexibleContentBlockPages
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesConfig;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;

return [
    'models' => [
        FilamentFlexibleContentBlockPagesConfig::TYPE_PAGE => \Statikbe\FilamentFlexibleContentBlockPages\Models\Page::class,
        FilamentFlexibleContentBlockPagesConfig::TYPE_REDIRECT => \Statikbe\FilamentFlexibleContentBlockPages\Models\Redirect::class,
        FilamentFlexibleContentBlockPagesConfig::TYPE_SETTINGS => \Statikbe\FilamentFlexibleContentBlockPages\Models\Settings::class,
        FilamentFlexibleContentBlockPagesConfig::TYPE_TAG => \Statikbe\FilamentFlexibleContentBlockPages\Models\Tag::class,
        FilamentFlexibleContentBlockPagesConfig::TYPE_TAG_TYPE => \Statikbe\FilamentFlexibleContentBlockPages\Models\TagType::class,
    ],

    'table_names' => [
        FilamentFlexibleContentBlockPagesConfig::TYPE_PAGE => 'pages',
        FilamentFlexibleContentBlockPagesConfig::TYPE_AUTHOR => 'users',
        FilamentFlexibleContentBlockPagesConfig::TYPE_SETTINGS => 'settings',
        FilamentFlexibleContentBlockPagesConfig::TYPE_REDIRECT => 'redirects',
        FilamentFlexibleContentBlockPagesConfig::TYPE_TAG => 'tags',
        FilamentFlexibleContentBlockPagesConfig::TYPE_TAGGABLE => 'taggables',
        FilamentFlexibleContentBlockPagesConfig::TYPE_TAG_TYPE => 'tag_types',
    ],

    'resources' => [
        FilamentFlexibleContentBlockPagesConfig::TYPE_PAGE => \Statikbe\FilamentFlexibleContentBlockPages\Resources\PageResource::class,
        FilamentFlexibleContentBlockPagesConfig::TYPE_SETTINGS => \Statikbe\FilamentFlexibleContentBlockPages\Resources\SettingsResource::class,
        FilamentFlexibleContentBlockPagesConfig::TYPE_REDIRECT => \Statikbe\FilamentFlexibleContentBlockPages\Resources\RedirectResource::class,
        FilamentFlexibleContentBlockPagesConfig::TYPE_TAG => \Statikbe\FilamentFlexibleContentBlockPages\Resources\TagResource::class,
        FilamentFlexibleContentBlockPagesConfig::TYPE_TAG_TYPE => \Statikbe\FilamentFlexibleContentBlockPages\Resources\TagTypeResource::class,
    ],

    'panel' => [
        'path' => 'content',
        'middleware' => [
            EncryptCookies::class,
            AddQueuedCookiesToResponse::class,
            StartSession::class,
            AuthenticateSession::class,
            ShareErrorsFromSession::class,
            VerifyCsrfToken::class,
            SubstituteBindings::class,
            DisableBladeIconComponents::class,
            DispatchServingFilamentEvent::class,
        ],
        'auth_middleware' => [
            Authenticate::class,
        ],
        'navigation_items' => [
            NavigationItem::make(fn () => flexiblePagesTrans('panel.navigation_items.go_to_website_lbl'))
                ->url('/')
                ->openUrlInNewTab()
                ->icon('heroicon-o-globe-alt')
                ->sort(-100),
        ],
    ],

    'seo' => [
        'default_canonical_locale' => 'nl',
    ],

    'route_helper' => \Statikbe\FilamentFlexibleContentBlockPages\Routes\LocalisedPageRouteHelper::class,

    'page_templates' => [
        //Page::HOME_PAGE => 'pages.home',
    ],
];
