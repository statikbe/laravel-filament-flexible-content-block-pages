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
            // Resources are automatically discovered via FilamentFlexibleContentBlockPages::getModelResource()
            \Statikbe\FilamentFlexibleContentBlockPages\Models\Page::class,

            // Add your own models here:
            // \App\Models\Category::class,
            // \App\Models\Post::class,
        ],
        'styles' => [
            // Available menu styles (codes only - labels come from translations)
            'default',

            // If needed, add your custom style(s) here:
            // 'mega',
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

        /*
        |--------------------------------------------------------------------------
        | Redirector
        |--------------------------------------------------------------------------
        |
        | This package implements its own redirector for spatie/laravel-missing-page-redirector.
        | To avoid manual configuration of this custom redirector in the spatie-package's config, we set the default here.
        | In case you would like to customise this, please change the redirector here and not in the spatie package.
        */
        'redirector' => \Statikbe\FilamentFlexibleContentBlockPages\Services\DatabaseAndConfigRedirector::class,
    ],

    'settings' => [
        'navigation_sort' => 5,
    ],

    'tags' => [
        'navigation_sort' => 20,
    ],

    /*
    |--------------------------------------------------------------------------
    | Tag Pages Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how tag pages work, including which models are included
    | and pagination settings.
    */
    'tag_pages' => [
        'models' => [
            /*
            | Which model classes should be included in tag pages.
            | These models must use the HasTags trait from spatie/laravel-tags.
            */
            'enabled' => [
                \Statikbe\FilamentFlexibleContentBlockPages\Models\Page::class,
                // Add your own taggable models here:
                // \App\Models\Article::class,
                // \App\Models\Product::class,
            ],

            /*
            | Whether to group similar content types together in display.
            | If true, all pages will be shown before all articles, etc.
            | If false, content will be mixed by publication date.
            */
            'group_by_type' => false,
        ],
        /*
         | The prefix of the route for tag pages. The format is:
         | /{route_path_prefix}/{tag:slug}
         */
        'route_path_prefix' => 'tag',
        'pagination' => [
            /*
            | Number of items per page for tag listings.
            */
            'item_count' => 20,

            /*
            | Show count of each content type in tag page title/description.
            | Example: "Laravel (5 pages, 12 articles)"
            */
            'show_type_counts' => true,
        ],
    ],
];
