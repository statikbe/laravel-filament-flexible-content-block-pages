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
        'navigation_items' => [
            // WARNING: Do not place redirect NavigationItems first in the array.
            // Filament automatically redirects to the first navigation item on panel load.
            // If that item is an external redirect (like the home route), users will be
            // bounced out of the panel, creating the appearance of authentication failure.
            //
            // NavigationItem::make(fn () => flexiblePagesTrans('panel.navigation_items.go_to_website_lbl'))
            //     ->url('/')
            //     ->openUrlInNewTab()
            //     ->icon('heroicon-o-globe-alt')
            //     ->sort(-100),
        ],
    ],

    'seo' => [
        'default_canonical_locale' => 'nl',
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

        'max_depth' => 2,
        'linkable_models' => [
            // Models that can be linked in menu items
            // These models must implement HasMenuLabel interface
            \Statikbe\FilamentFlexibleContentBlockPages\Models\Page::class,

            // Add your own models here:
            // \App\Models\Category::class,
            // \App\Models\Product::class,
        ],
        'model_icons' => [
            // Configure icons for different model types based on their morph class
            'filament-flexible-content-block-pages::page' => 'heroicon-o-document-text',

            // Add custom icons for your models:
            // 'category' => 'heroicon-o-tag',
            // 'product' => 'heroicon-o-shopping-bag',
            // 'post' => 'heroicon-o-newspaper',
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
];
