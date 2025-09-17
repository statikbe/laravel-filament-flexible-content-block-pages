<?php

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

/*
|--------------------------------------------------------------------------
| Filament Flexible Content Block Pages Configuration
|--------------------------------------------------------------------------
| @see https://github.com/statikbe/laravel-filament-flexible-content-block-pages/blob/main/documentation/configuration.md
*/

return [
    /*
    |--------------------------------------------------------------------------
    | Model Classes
    |--------------------------------------------------------------------------
    |
    | Specify the model classes for each component type. This allows you to
    | extend the default models with your own implementations if needed.
    | All models should extend the corresponding base models from this package.
    | Except the page model, you can tailor to your exact needs by selecting the
    | necessary interfaces and traits.
    */
    'models' => [
        FilamentFlexibleContentBlockPagesConfig::TYPE_PAGE => \Statikbe\FilamentFlexibleContentBlockPages\Models\Page::class,
        FilamentFlexibleContentBlockPagesConfig::TYPE_REDIRECT => \Statikbe\FilamentFlexibleContentBlockPages\Models\Redirect::class,
        FilamentFlexibleContentBlockPagesConfig::TYPE_SETTINGS => \Statikbe\FilamentFlexibleContentBlockPages\Models\Settings::class,
        FilamentFlexibleContentBlockPagesConfig::TYPE_TAG => \Statikbe\FilamentFlexibleContentBlockPages\Models\Tag::class,
        FilamentFlexibleContentBlockPagesConfig::TYPE_TAG_TYPE => \Statikbe\FilamentFlexibleContentBlockPages\Models\TagType::class,
        FilamentFlexibleContentBlockPagesConfig::TYPE_MENU => \Statikbe\FilamentFlexibleContentBlockPages\Models\Menu::class,
        FilamentFlexibleContentBlockPagesConfig::TYPE_MENU_ITEM => \Statikbe\FilamentFlexibleContentBlockPages\Models\MenuItem::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Table Names
    |--------------------------------------------------------------------------
    |
    | Define the database table names used by the package.
    |
    */
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

    /*
    |--------------------------------------------------------------------------
    | Filament Resources
    |--------------------------------------------------------------------------
    |
    | Specify the Filament resource classes for managing each model type.
    | You can extend these resources to customize the admin interface
    | or create your own implementations.
    |
    */
    'resources' => [
        FilamentFlexibleContentBlockPagesConfig::TYPE_PAGE => \Statikbe\FilamentFlexibleContentBlockPages\Resources\PageResource::class,
        FilamentFlexibleContentBlockPagesConfig::TYPE_SETTINGS => \Statikbe\FilamentFlexibleContentBlockPages\Resources\SettingsResource::class,
        FilamentFlexibleContentBlockPagesConfig::TYPE_REDIRECT => \Statikbe\FilamentFlexibleContentBlockPages\Resources\RedirectResource::class,
        FilamentFlexibleContentBlockPagesConfig::TYPE_TAG => \Statikbe\FilamentFlexibleContentBlockPages\Resources\TagResource::class,
        FilamentFlexibleContentBlockPagesConfig::TYPE_TAG_TYPE => \Statikbe\FilamentFlexibleContentBlockPages\Resources\TagTypeResource::class,
        FilamentFlexibleContentBlockPagesConfig::TYPE_MENU => \Statikbe\FilamentFlexibleContentBlockPages\Resources\MenuResource::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Page Resource Configuration
    |--------------------------------------------------------------------------
    |
    | Configure various features and options for the page resource.
    | These settings control which fields and functionality are available
    | in the page management interface.
    |
    */
    'page_resource' => [
        \Statikbe\FilamentFlexibleContentBlockPages\Models\Page::class => [
            /*
            | Enable the feature to have call-to-action buttons in the hero of the page
            */
            'enable_hero_call_to_actions' => true,

            /*
            | Enable the feature for pages to have an author
            */
            'enable_author' => true,

            /*
            | Enable the feature for pages to have parent pages.
            */
            'enable_parent' => true,

            /*
            | Enable the feature for pages to have a boolean to make them undeletable.
            */
            'enable_undeletable' => true,

            /*
            | Enable the replicate action on the table
            */
            'enable_replicate_action_on_table' => false,

            /*
            | The Filament navigation menu sorting order of the page resource
            */
            'navigation_sort' => 5,

            'gates' => [
                'undeletable' => 'change_undeletable:page',
            ],
        ],
        // If you extend PageResource and want to use your own model, you can add your the extended page resource config for your own model here...
    ],

    /*
    |--------------------------------------------------------------------------
    | CMS Panel Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the Filament CMS panel settings including the access path
    | and middleware stack. The middleware ensures proper authentication,
    | session management, and CSRF protection.
    |
    */
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

    /*
    |--------------------------------------------------------------------------
    | Route Helper
    |--------------------------------------------------------------------------
    |
    | Specify the route helper class for generating page URLs. The default
    | LocalisedPageRouteHelper supports multi-language sites with localized
    | URLs. You can implement your own route helper for custom URL patterns.
    | Use the PageRouteHelper for non-translatable routes.
    |
    */
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

    /*
    |--------------------------------------------------------------------------
    | Page Templates
    |--------------------------------------------------------------------------
    |
    | Define custom Blade templates for specific page types. This allows you
    | to override the default page rendering with custom layouts for special
    | pages like home pages, landing pages, or other unique page types.
    | Use the code of the page as key and the blade template as value.
    |
    */
    'page_templates' => [
        // Page::HOME_PAGE => 'pages.home',
    ],

    /*
    |--------------------------------------------------------------------------
    | Menu Builder Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the menu builder system including navigation sorting,
    | maximum depth, linkable models, and available menu styles.
    |
    */
    'menu' => [
        /*
        | The Filament navigation menu sorting order of the menu resource
        */
        'navigation_sort' => 30,

        /*
        | Maximum depth allowed for menu items. Defines how many levels deep
        | the menu hierarchy can go (e.g., 2 = parent → child → grandchild).
        | This is the default value. You can override this in the menu database record.
        */
        'max_depth' => 2,

        /*
        | Enable the icon field for menu items. When enabled, menu items
        | can have icons assigned to them for better visual representation
        | in the frontend menu display.
        */
        'enable_menu_item_icon_field' => true,

        /*
        | Models that can be linked in menu items. These models must implement
        | the HasMenuLabel interface to provide a label for menu display, and the Linkable interface to get a url of the model.
        */
        'linkable_models' => [
            \Statikbe\FilamentFlexibleContentBlockPages\Models\Page::class,

            // Add your own models here:
            // \App\Models\Category::class,
            // \App\Models\Post::class,
        ],

        /*
        | Available menu styles for frontend rendering. These are style codes
        | only - the actual labels in the dropdowns in the UI come from translation files.
        | Each style corresponds to a different menu layout/appearance.
        */
        'styles' => [
            'default',

            // If needed, add your custom style(s) here:
            // 'mega',
            // 'horizontal',
            // 'vertical',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Sitemap Configuration
    |--------------------------------------------------------------------------
    |
    | Configure sitemap generation including which content types to include,
    | URL patterns to exclude, and custom URLs to add.
    |
    */
    'sitemap' => [
        /*
        | Enable or disable sitemap generation entirely.
        */
        'enabled' => true,

        /*
        | The default canonical locale for multilingual sites. This locale
        | will be used as the canonical URL in the sitemap when multiple
        | language versions of a page exist.
        */
        'default_canonical_locale' => 'nl',

        /*
        | The service class responsible for generating the sitemap.
        | You can extend or replace this with your own implementation by implementing the GeneratesSitemap interface.
        */
        'generator_service' => \Statikbe\FilamentFlexibleContentBlockPages\Services\SitemapGeneratorService::class,

        /*
        | Sitemap generation method. MANUAL requires calling the artisan command,
        | while AUTOMATIC generates the sitemap on each request (not recommended for production)
        | and HYBRID does a combination of both.
        */
        'method' => SitemapGeneratorMethod::MANUAL,

        /*
        | Include pages in the sitemap. When true, all published pages will be automatically added to the sitemap.
        */
        'include_pages' => true,

        /*
        | Include link routes in the sitemap. This adds GET routes that are defined in your application's route files.
        */
        'include_link_routes' => true,

        /*
        | Include linkable models in the sitemap. Models that implement the Linkable interface will be automatically included.
        */
        'include_linkable_models' => true,

        /*
        | URL patterns to exclude from the sitemap. Use regex patterns to match URLs that should not appear in the sitemap.
        */
        'exclude_patterns' => [
            // '/admin/.*',
            // '/test/.*',
        ],

        /*
        | Custom URLs to manually include in the sitemap. Add specific
        | URLs that should be included but might not be automatically discovered.
        */
        'custom_urls' => [
            // '/special-page',
            // '/custom-landing',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Redirects Configuration
    |--------------------------------------------------------------------------
    */
    'redirects' => [
        /*
        | The Filament navigation menu sorting order of the redirect resource
        */
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

    /*
    |--------------------------------------------------------------------------
    | Settings Configuration
    |--------------------------------------------------------------------------
    */
    'settings' => [
        /*
        | The Filament navigation menu sorting order of the settings resource
        */
        'navigation_sort' => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | Tags Configuration
    |--------------------------------------------------------------------------
    */
    'tags' => [
        /*
        | The Filament navigation menu sorting order of the tag resource
        */
        'navigation_sort' => 20,
    ],

    /*
    |--------------------------------------------------------------------------
    | Tag Pages Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how SEO tag pages work, including which models are included
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
