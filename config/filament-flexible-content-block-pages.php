<?php

// config for Statikbe/FilamentFlexibleContentBlockPages
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesConfig;
use Statikbe\FilamentFlexibleContentBlockPages\Services\Enum\SitemapGeneratorMethod;

return [
    'models' => [
        FilamentFlexibleContentBlockPagesConfig::TYPE_PAGE => \Statikbe\FilamentFlexibleContentBlockPages\Models\Page::class,
        FilamentFlexibleContentBlockPagesConfig::TYPE_REDIRECT => \Statikbe\FilamentFlexibleContentBlockPages\Models\Redirect::class,
        FilamentFlexibleContentBlockPagesConfig::TYPE_SETTINGS => \Statikbe\FilamentFlexibleContentBlockPages\Models\Settings::class,
        FilamentFlexibleContentBlockPagesConfig::TYPE_TAG => \Statikbe\FilamentFlexibleContentBlockPages\Models\Tag::class,
        FilamentFlexibleContentBlockPagesConfig::TYPE_TAG_TYPE => \Statikbe\FilamentFlexibleContentBlockPages\Models\TagType::class,
        FilamentFlexibleContentBlockPagesConfig::TYPE_MENU => \Statikbe\FilamentFlexibleContentBlockPages\Models\Menu::class,
        FilamentFlexibleContentBlockPagesConfig::TYPE_MENU_ITEM => \Statikbe\FilamentFlexibleContentBlockPages\Models\MenuItem::class,
    ],

    'table_names' => [
        FilamentFlexibleContentBlockPagesConfig::TYPE_PAGE => 'pages',
        FilamentFlexibleContentBlockPagesConfig::TYPE_AUTHOR => 'users',
        FilamentFlexibleContentBlockPagesConfig::TYPE_SETTINGS => 'settings',
        FilamentFlexibleContentBlockPagesConfig::TYPE_REDIRECT => 'redirects',
        FilamentFlexibleContentBlockPagesConfig::TYPE_TAG => 'tags',
        FilamentFlexibleContentBlockPagesConfig::TYPE_TAGGABLE => 'taggables',
        FilamentFlexibleContentBlockPagesConfig::TYPE_TAG_TYPE => 'tag_types',
        FilamentFlexibleContentBlockPagesConfig::TYPE_MENU => 'menus',
        FilamentFlexibleContentBlockPagesConfig::TYPE_MENU_ITEM => 'menu_items',
    ],

    'resources' => [
        FilamentFlexibleContentBlockPagesConfig::TYPE_PAGE => \Statikbe\FilamentFlexibleContentBlockPages\Resources\PageResource::class,
        FilamentFlexibleContentBlockPagesConfig::TYPE_SETTINGS => \Statikbe\FilamentFlexibleContentBlockPages\Resources\SettingsResource::class,
        FilamentFlexibleContentBlockPagesConfig::TYPE_REDIRECT => \Statikbe\FilamentFlexibleContentBlockPages\Resources\RedirectResource::class,
        FilamentFlexibleContentBlockPagesConfig::TYPE_TAG => \Statikbe\FilamentFlexibleContentBlockPages\Resources\TagResource::class,
        FilamentFlexibleContentBlockPagesConfig::TYPE_TAG_TYPE => \Statikbe\FilamentFlexibleContentBlockPages\Resources\TagTypeResource::class,
        FilamentFlexibleContentBlockPagesConfig::TYPE_MENU => \Statikbe\FilamentFlexibleContentBlockPages\Resources\MenuResource::class,
    ],

    'page_resource' => [
        \Statikbe\FilamentFlexibleContentBlockPages\Models\Page::class => [
            'enable_hero_call_to_actions' => true,
            'enable_author' => true,
            'enable_parent' => true,
            'enable_undeletable' => true,
            'navigation_sort' => 5,
        ],
        // If you extend PageResource and want to use your own model, you can add your the extended page resource config for your own model here...
    ],

    'panel' => [
        'path' => 'admin/website',
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
    ],

    'route_helper' => \Statikbe\FilamentFlexibleContentBlockPages\Routes\LocalisedPageRouteHelper::class,

    /*
    |--------------------------------------------------------------------------
    | Theme
    |--------------------------------------------------------------------------
    |
    | It is possible to create different themes for templates (pages, layouts,
    | menus, language switch components). Creating a new theme is done by
    | publishing the views and then creating a new directory under
    | resources/views/components/{theme}. You should then specify the name
    | of your theme below.
    |
    */
    'theme' => 'tailwind',

    'page_templates' => [
        // Page::HOME_PAGE => 'pages.home',
    ],

    'menu' => [
        'navigation_sort' => 30,
        'max_depth' => 2,
        'enable_menu_item_icon_field' => true,
        'linkable_models' => [
            // Models that can be linked in menu items
            // These models must implement HasMenuLabel interface
            // Resources are automatically discovered via Filament::getModelResource()
            \Statikbe\FilamentFlexibleContentBlockPages\Models\Page::class,

            // Add your own models here:
            // \App\Models\Category::class,
            // \App\Models\Post::class,
        ],
        'styles' => [
            // Available menu styles (codes only - labels come from translations)
            'default',
            'horizontal',
            'vertical',
            'dropdown',

            // Add your custom styles here:
            // 'mega',
            // 'mobile',
            // 'breadcrumb',
        ],
    ],

    'sitemap' => [
        'enabled' => true,
        'default_canonical_locale' => 'nl',
        'generator_service' => \Statikbe\FilamentFlexibleContentBlockPages\Services\SitemapGeneratorService::class,
        'method' => SitemapGeneratorMethod::MANUAL,
        'include_pages' => true,
        'include_link_routes' => true,
        'include_linkable_models' => true,
        'exclude_patterns' => [
            // URL patterns to exclude from sitemap
        ],
        'custom_urls' => [
            // Custom URLs to include in sitemap
        ],
    ],

    'redirects' => [
        'navigation_sort' => 10,
    ],

    'settings' => [
        'navigation_sort' => 5,
    ],

    'tags' => [
        'navigation_sort' => 20,
    ],
];
