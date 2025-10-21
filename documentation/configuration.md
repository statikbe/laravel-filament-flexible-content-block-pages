# Configuration

In this document, we explain all settings available in the [filament-flexible-content-block-pages configuration file](../config/filament-flexible-content-block-pages.php).

## Table of content

<!--ts-->
   * [Overview](#overview)
   * [Model Classes](#model-classes)
   * [Database Table Names](#database-table-names)
   * [Filament Resources](#filament-resources)
   * [Page Resource Configuration](#page-resource-configuration)
      * [Page Resource Options](#page-resource-options)
   * [CMS Panel Configuration](#cms-panel-configuration)
   * [Route Helper](#route-helper)
   * [Theme Configuration](#theme-configuration)
   * [Page Templates](#page-templates)
   * [Menu Builder Configuration](#menu-builder-configuration)
      * [Menu Configuration Options](#menu-configuration-options)
   * [Sitemap Configuration](#sitemap-configuration)
      * [Sitemap Generation Methods](#sitemap-generation-methods)
   * [Redirects Configuration](#redirects-configuration)
   * [Settings Configuration](#settings-configuration)
   * [Tags Configuration](#tags-configuration)
   * [Tag Pages Configuration](#tag-pages-configuration)
      * [Tag Pages Options](#tag-pages-options)
   * [Advanced Customization](#advanced-customization)
      * [Extending Models](#extending-models)
      * [Custom Route Helpers](#custom-route-helpers)
      * [Custom Menu Styles](#custom-menu-styles)

<!-- Created by https://github.com/ekalinin/github-markdown-toc -->
<!-- Added by: sten, at: Mon Sep 29 23:52:45 CEST 2025 -->

<!--te-->

## Overview

The Filament Flexible Content Block Pages package provides a comprehensive CMS system with flexible content blocks for Filament. 
The configuration file allows you to customize models, resources, database tables, and various features to match your application's requirements.

## Model Classes

Configure the model classes used by the package. You can extend the default models with your own implementations if needed.

```php
'models' => [
    FilamentFlexibleContentBlockPagesConfig::TYPE_PAGE => \Statikbe\FilamentFlexibleContentBlockPages\Models\Page::class,
    FilamentFlexibleContentBlockPagesConfig::TYPE_REDIRECT => \Statikbe\FilamentFlexibleContentBlockPages\Models\Redirect::class,
    FilamentFlexibleContentBlockPagesConfig::TYPE_SETTINGS => \Statikbe\FilamentFlexibleContentBlockPages\Models\Settings::class,
    FilamentFlexibleContentBlockPagesConfig::TYPE_TAG => \Statikbe\FilamentFlexibleContentBlockPages\Models\Tag::class,
    FilamentFlexibleContentBlockPagesConfig::TYPE_TAG_TYPE => \Statikbe\FilamentFlexibleContentBlockPages\Models\TagType::class,
    FilamentFlexibleContentBlockPagesConfig::TYPE_MENU => \Statikbe\FilamentFlexibleContentBlockPages\Models\Menu::class,
    FilamentFlexibleContentBlockPagesConfig::TYPE_MENU_ITEM => \Statikbe\FilamentFlexibleContentBlockPages\Models\MenuItem::class,
],
```

All models should extend the corresponding base models from this package, except the page model, 
which you can tailor to your exact needs by selecting the necessary interfaces and traits.

## Database Table Names

Define the database table names used by the package. You can customize these table names if they conflict with your existing database schema.

```php
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
```

## Filament Resources

Specify the Filament resource classes for managing each model type. You can extend these resources to customize the admin interface or create your own implementations.

```php
'resources' => [
    FilamentFlexibleContentBlockPagesConfig::TYPE_PAGE => \Statikbe\FilamentFlexibleContentBlockPages\Resources\PageResource::class,
    FilamentFlexibleContentBlockPagesConfig::TYPE_SETTINGS => \Statikbe\FilamentFlexibleContentBlockPages\Resources\SettingsResource::class,
    FilamentFlexibleContentBlockPagesConfig::TYPE_REDIRECT => \Statikbe\FilamentFlexibleContentBlockPages\Resources\RedirectResource::class,
    FilamentFlexibleContentBlockPagesConfig::TYPE_TAG => \Statikbe\FilamentFlexibleContentBlockPages\Resources\TagResource::class,
    FilamentFlexibleContentBlockPagesConfig::TYPE_TAG_TYPE => \Statikbe\FilamentFlexibleContentBlockPages\Resources\TagTypeResource::class,
    FilamentFlexibleContentBlockPagesConfig::TYPE_MENU => \Statikbe\FilamentFlexibleContentBlockPages\Resources\MenuResource::class,
],
```

If you want to extend the Page resource, you can change the form tabs, by overwriting the form fields in each tab.
You can override following functions: `getGeneralTabFields()`, `getContentTabFields()`, `getOverviewTabFields()`, `getSEOTabFields()` & `getAdvancedTabFields()`.

## Page Resource Configuration

Configure various features and options for the page resource. These settings control which fields and functionality are available in the page management interface.

```php
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
        'enable_page_tree' => true,

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

        /*
        | Authorisation gates for the page resource.
        */
        'gates' => [
            /*
            | The authorisation gate to show the undeletable toggle on the edit page.
            | The value should be the name of the gate to execute `Gate::allows($gateName, User $user, Page $page)`
            */
            'undeletable' => 'change_undeletable',
        ],
        
        /*
         | If the page tree is enabled with `enable_page_tree`, you can here configure its settings.  
         */
        'page_tree' => [
            /*
             | The maximum depth of the page tree. 
             */
            'max_depth' => 2,
        ],
    ],
    // Add extended page resource configurations here...
],
```

### Page Resource Options

- **enable_hero_call_to_actions**: When enabled, pages can have call-to-action buttons in their hero sections
- **enable_author**: Allows pages to have an assigned author from the users table
- **enable_page_tree**: Enables hierarchical page structure with parent-child relationships and the tree hierarchy page
- **enable_undeletable**: Adds a boolean field to protect important pages from deletion
- **enable_replicate_action_on_table**: Shows the replicate action in the table
- **navigation_sort**: Controls the order of the page resource in the Filament navigation menu
- **gates.undeletable**: The authorisation gate to allow the deletable toggle to be shown on the page edit page.
- **gates.view_unpublished_pages**: The authorisation gate to allow viewing unpublished pages on the website. This is useful for content editors to preview.
- **page_tree.max_depth**: The maximum allowed page depth in the tree hierarchy of pages. First, enable the page tree.

## CMS Panel Configuration

Configure the Filament CMS panel settings including the access path and middleware stack. Most likely, you will not need
to change these middleware settings.

```php
'panel' => [
    // Admin panel URL path
    'path' => 'admin/website',
    
    // Middleware stack for the panel
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
    
    // Authentication middleware
    'auth_middleware' => [
        Authenticate::class,
    ],
],
```

## Route Helper

Specify the route helper class for generating page URLs. The default `LocalisedPageRouteHelper` supports multi-language sites with localized URLs.

```php
'route_helper' => \Statikbe\FilamentFlexibleContentBlockPages\Routes\LocalisedPageRouteHelper::class,
```

For non-translatable routes, you can use:
```php
'route_helper' => \Statikbe\FilamentFlexibleContentBlockPages\Routes\PageRouteHelper::class,
```

### Enable home page route

The package provides a home page route where the page with code `HOME` is rendered.
If you want to implement your own custom home page, you can disable this:

```php
'enable_home_route' => false,
```

## Theme Configuration

Configure the theme for templates including pages, layouts, menus, and language switch components.

```php
'theme' => 'tailwind',
```

To create a custom theme:
1. Publish the views using the artisan command
2. Create a new directory under `resources/views/components/{theme}`
3. Update the theme configuration to use your custom theme name

## Page Templates

Define custom Blade templates for specific page types. This allows you to override the default page rendering with custom layouts.
The key should be the code of the page and the value is the Blade template path.

```php
'page_templates' => [
    // Use page code as key and blade template as value
    'home' => 'pages.home',
    'contact' => 'pages.contact-custom',
],
```

## Menu Builder Configuration

Configure the menu builder system including navigation sorting, maximum depth, linkable models, and available menu styles.

```php
'menu' => [
    // Navigation sorting order in Filament admin
    'navigation_sort' => 30,
    
    // Maximum menu hierarchy depth (default can be overridden per menu)
    'max_depth' => 2,
    
    // Enable icon field for menu items
    'enable_menu_item_icon_field' => true,
    
    // Models that can be linked in menu items
    'linkable_models' => [
        \Statikbe\FilamentFlexibleContentBlockPages\Models\Page::class,
        // Add your own linkable models here
    ],
    
    // Available menu styles for frontend rendering
    'styles' => [
        'default',
        // Add custom styles here
    ],
],
```

### Menu Configuration Options

- **navigation_sort**: Controls the order of the menu resource in the Filament navigation
- **max_depth**: Defines how many levels deep the menu hierarchy can go (e.g., 2 = parent → child → grandchild)
- **enable_menu_item_icon_field**: When enabled, menu items can have icons assigned for better visual representation
- **linkable_models**: Models that can be linked in menu items. Must implement `HasMenuLabel` and `Linkable` interfaces
- **styles**: Available menu styles for frontend rendering. Style codes only - labels come from translation files

## Sitemap Configuration

Configure sitemap generation including content types to include, URL patterns to exclude, and custom URLs to add.

```php
'sitemap' => [
    // Enable/disable sitemap generation
    'enabled' => true,
    
    // Default canonical locale for multilingual sites
    'default_canonical_locale' => 'nl',
    
    // Service class for sitemap generation
    'generator_service' => \Statikbe\FilamentFlexibleContentBlockPages\Services\SitemapGeneratorService::class,
    
    // Generation method (MANUAL, AUTOMATIC, or HYBRID)
    'method' => SitemapGeneratorMethod::MANUAL,
    
    // Include published pages in sitemap
    'include_pages' => true,
    
    // Include GET routes in sitemap
    'include_link_routes' => true,
    
    // Include linkable models in sitemap
    'include_linkable_models' => true,
    
    // URL patterns to exclude (regex)
    'exclude_patterns' => [
        '/admin/.*',
        '/test/.*',
    ],
    
    // Custom URLs to include
    'custom_urls' => [
        '/special-page',
    ],
],
```

### Sitemap Generation Methods

- **MANUAL**: Requires calling the artisan command to generate the sitemap
- **AUTOMATIC**: Generates the sitemap on each request (not recommended for production)
- **HYBRID**: Combines both manual and automatic generation

## Redirects Configuration

Configure redirect management including navigation sorting and the redirector service.

```php
'redirects' => [
    // Navigation sorting order
    'navigation_sort' => 10,
    
    // Custom redirector implementation
    'redirector' => \Statikbe\FilamentFlexibleContentBlockPages\Services\DatabaseAndConfigRedirector::class,
],
```

The package integrates with `spatie/laravel-missing-page-redirector` to handle 404 errors and URL redirects.

## Settings Configuration

Configure the global settings management.

```php
'settings' => [
    'navigation_sort' => 5,
],
```

Settings provide a way to manage site-wide configuration options through the admin interface.

## Tags Configuration

Configure the tagging system including navigation sorting.

```php
'tags' => [
    'navigation_sort' => 20,
],
```

The tagging system allows you to categorize and organize content with support for tag types and hierarchical tag structures.

## Tag Pages Configuration

Configure how SEO tag pages work, including which models are included and pagination settings.

**Important**: for SEO tag pages to work, you need to have tags with a tag type with `has_seo_pages` set to true.

```php
'tag_pages' => [
    'models' => [
        // Models included in tag pages (must use HasTags trait)
        'enabled' => [
            \Statikbe\FilamentFlexibleContentBlockPages\Models\Page::class,
        ],
        
        // Group content types together in display
        'group_by_type' => false,
    ],
    
    // Route prefix for tag pages (/{route_path_prefix}/{tag:slug})
    'route_path_prefix' => 'tag',
    
    'pagination' => [
        // Items per page
        'item_count' => 20,
        
        // Show content type counts in title
        'show_type_counts' => true,
    ],
],
```

### Tag Pages Options

- **enabled**: Models that should appear on tag pages. Must use the `HasTags` trait from spatie/laravel-tags
- **group_by_type**: Whether to group similar content types together or mix by publication date
- **route_path_prefix**: URL prefix for tag pages
- **item_count**: Number of items displayed per page
- **show_type_counts**: Whether to show counts like "Laravel (5 pages, 12 articles)" in titles

## Advanced Customization

### Extending Models

To extend the default models, create your own model classes that extend the base models:

```php
use Statikbe\FilamentFlexibleContentBlockPages\Models\Page as BasePage;

class Page extends BasePage
{
    // Your customizations
}
```

Then update the configuration:

```php
'models' => [
    FilamentFlexibleContentBlockPagesConfig::TYPE_PAGE => \App\Models\Page::class,
],
```

### Custom Route Helpers

Create custom route helpers by implementing the appropriate interface:

```php
use Statikbe\FilamentFlexibleContentBlockPages\Contracts\RouteHelper;

class CustomRouteHelper implements RouteHelper
{
    // Implement required methods
}
```

### Custom Menu Styles

Add custom menu styles by:

1. Adding the style code to the configuration
2. Creating the corresponding Blade component
3. Adding translations for the style label

```php
'menu' => [
    'styles' => [
        'default',
        'custom-mega-menu',
    ],
],
```
