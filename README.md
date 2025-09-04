# Filament Flexible Content Block Pages

[![Latest Version on Packagist](https://img.shields.io/packagist/v/statikbe/laravel-filament-flexible-content-block-pages.svg?style=flat-square)](https://packagist.org/packages/statikbe/laravel-filament-flexible-content-block-pages)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/statikbe/laravel-filament-flexible-content-block-pages/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/statikbe/laravel-filament-flexible-content-block-pages/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/statikbe/laravel-filament-flexible-content-block-pages/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/statikbe/laravel-filament-flexible-content-block-pages/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/statikbe/laravel-filament-flexible-content-block-pages.svg?style=flat-square)](https://packagist.org/packages/statikbe/laravel-filament-flexible-content-block-pages)

A CMS solution for Laravel applications built on [Filament Flexible Content Blocks](https://github.com/statikbe/laravel-filament-flexible-content-blocks). 
This package extends the flexible content block system, into a full page management solution with routing, SEO, menus, and multilingual support.

Designed for developers who need a content management system that integrates seamlessly with existing Laravel Filament application, 
while providing content editors with an intuitive interface for managing pages and content.

## Key Features

- **Flexible page management** - Create pages with hero images, flexible content blocks, SEO fields, and publication controls
- **Hierarchical menu builder** - Drag-and-drop interface for creating navigation menus
- **Multilingual support** - Full localization with automatic route generation for multiple languages
- **SEO tools** - Automatic sitemap generation, meta tag management, and URL redirect handling when slugs change
- **Ready-to-use admin interface** - Pre-configured Filament panel with all resources and management tools
- **Developer-friendly** - Extendable models & tables, customizable templates, and comprehensive configuration options
- **Content organization** - Tag system, hierarchical page structure, and settings management
- **Works out-of-the-box** - Get the package quickly up and running, while focussing on easy configuration, customisation & extendability.

## Additional Features

- Website routing with customizable URL patterns
- Blade view components and CSS themes
- Media library integration via Spatie packages
- Automatic 301 redirects when page URLs change
- Multiple sitemap generation methods (manual, crawling, hybrid)
- Configurable content blocks and layouts

## Table of contents

TODO

## Installation

You can install the package via composer:

```bash
composer require statikbe/laravel-filament-flexible-content-block-pages
```

Publish the config file with:

```bash
php artisan vendor:publish --tag="filament-flexible-content-block-pages-config"
```

If you want to alter the names of the database tables, do so in the config file, **before running the migrations**.

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="filament-flexible-content-block-pages-migrations"
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-migrations"
php artisan migrate
```

Configure the Filament tailwind styling of the frontend by adding these view paths to `content` array of `tailwind.config.js`:

```javascript
content: [
    ...
    './vendor/solution-forest/filament-tree/resources/**/*.blade.php',
    './vendor/statikbe/laravel-filament-flexible-content-block-pages/**/*.blade.php',
    './vendor/statikbe/laravel-filament-flexible-content-blocks/**/*.blade.php',
    './config/filament-flexible-content-blocks.php',
]
```

In the tailwind config of your filament back-end, add the following lines to the `content` array:

```javascript
content: [
    ...
    './config/filament-flexible-content-blocks.php',
]
```

You can now seed the home page and default settings by running:

```bash
php artisan flexible-content-block-pages:seed
```

Further configure the [third-party packages that are used](#credits). Check the installation documentation of the following packages:

### [Laravel Filament Flexible Content Blocks](https://github.com/statikbe/laravel-filament-flexible-content-blocks)

Probably, you will want to tweak the configuration of the Flexible blocks package. Publish the configuration using [the
installation guide](https://github.com/statikbe/laravel-filament-flexible-content-blocks?tab=readme-ov-file#installation).

### [Laravel Localization](https://github.com/mcamara/laravel-localization?tab=readme-ov-file#installation):

Make sure the middlewares are properly set up if you want to use localised routes.

### [Laravel Tags](https://spatie.be/docs/laravel-tags/v4/installation-and-setup):

Publish the config and change the tag model to the package model:
```php 
[
    'tag_model' => \Statikbe\FilamentFlexibleContentBlockPages\Models\Tag::class,
]
```

Check [the configuration documentation](#configuration) for more explanations on how to tweak the package.

Most likely you want to publish the views, so you can customise it for your project:

```bash
php artisan vendor:publish --tag="filament-flexible-content-block-pages-views"
```

## Setup in your project

### Translations

If you want translated content and routes, go through the following steps: 

1. Configure the `supported_locales` in the Filament Flexible Content Blocks configuration or in a service provider
2. Configure the `route_helper` in [`filament-flexible-content-block-pages.php`](./config/filament-flexible-content-block-pages.php)

### Routes

Register the routes in your route file, probably `web.php`. 
Best at the bottom of the file, since the pages routes with slugs will catch many urls.

```php
\Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages::routes();
```

### Filament panel

The package contains a pre-configured panel. You can register the panel in the `app.php` configuration file.

```php
'providers' => [
    // ...
    \Statikbe\FilamentFlexibleContentBlockPages\FlexibleContentBlockPagesPanel::class,
    // ...
],
```

If you want you can build your own panel from the provided resources.

### Schedule

We suggest to add media library maintenance tasks to your schedule in `routes/console.php`:

```php
Schedule::command('media-library:clean')
    ->weeklyOn(1, '11:00');
Schedule::command('media-library:regenerate --only-missing')
    ->dailyAt('4:20');
```

## Menu builder

The package includes a powerful hierarchical menu builder with a drag-and-drop interface for creating navigation menus. Menus support multiple types of links and can be easily styled with custom templates.

### Features

- **Hierarchical structure** - With configurable max depth per menu
- **Multiple link types** - Internal routes, external URLs, and linkable models (Pages or your own project models)
- **Drag & drop management** - Intuitive tree interface for reordering and nesting items
- **Translation support** - Multilingual menu labels with locale-aware URLs
- **Conditional visibility** - Show/hide menu items without deleting them
- **Icon support** - Optional icons for menu items (basic implementation currently)
- **Dynamic labels** - Use model titles or custom labels for linked content

### Adding a menu to Blade

The package includes a `default` built-in menu style which is developed in a generic way so that you can tweak its styling by passing some attributes without having to publish the corresponding blade templates.

Example usage to have a horizontal menu using tailwind:
```blade
<x-flexible-pages-menu
    code="HEADER"
    style="default"
    ulClass="flex flex-row justify-start items-center gap-x-4"
    itemLinkClass="text-black hover:text-primary hover:underline"
    currentItemLinkClass="text-grey hover:no-underline"
/>
```

See the file `../tailwind/components/menu/default.blade.php` for all possible attributes.

### Adding linkable models

To make your models available in the menu builder, add them to the configuration:

```php
// config/filament-flexible-content-block-pages.php
'menu' => [
    'linkable_models' => [
        \App\Models\Page::class,
        \App\Models\Product::class,
        \App\Models\Category::class,
    ],
],
```

Your models should implement the `[HasMenuLabel](src/Models/Contracts/HasMenuLabel.php)` contract and the [HasMenuItemTrait](src/Models/Concerns/HasMenuItemTrait.php) trait:

```php
use Statikbe\FilamentFlexibleContentBlockPages\Models\Contracts\HasMenuLabel;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Concerns\HasMenuItemTrait;

class Product extends Model implements HasMenuLabel
{
    use HasMenuItemTrait;
    
    public function getMenuLabel(?string $locale = null): string
    {
        return $this->getTranslation('name', $locale ?? app()->getLocale());
    }
}
```

If you are using the Flexible Content Blocks title trait in your model, you can implement `HasMenuLabel` 
easily with [`HasTitleMenuLabelTrait`](src/Models/Concerns/HasTitleMenuLabelTrait.php).

### Menu seeding

It makes a lot of sense to create most of the menu's in seeders, so they can be automatically synced over different environments.
See the [menu seeding documentation](documentation/seeders.md) for programmatic menu creation.

For creating custom menu styles and advanced menu customization, see the [menu customization documentation](documentation/extending-and-customisation.md#menu).

## Settings

All settings are stored in one table in one record. The reason is to be able to add spatie medialibrary media as a config value.
Each setting is cached and refreshed when the settings change.

### Use settings

Access settings values using helper functions or static methods:

```php
use Statikbe\FilamentFlexibleContentBlockPages\Models\Settings;

// Helper functions (recommended)
$siteTitle = flexiblePagesSetting(Settings::SETTING_SITE_TITLE);
$contactInfo = flexiblePagesSetting(Settings::SETTING_CONTACT_INFO, 'en', 'info@statik.be');
$seoImageUrl = flexiblePagesSettingImageUrl(Settings::COLLECTION_DEFAULT_SEO, Settings::CONVERSION_DEFAULT_SEO);

// Static methods
$siteTitle = Settings::setting(Settings::SETTING_SITE_TITLE);
$seoImageHtml = Settings::imageHtml(Settings::COLLECTION_DEFAULT_SEO, Settings::CONVERSION_DEFAULT_SEO);
$settings = Settings::getSettings();
```

To add custom settings fields and extend the settings functionality, see the [settings extension documentation](documentation/extending-and-customisation.md#settings).

## Routing

The package provides a flexible routing system that supports hierarchical page structures and multilingual sites.

### URL Structure

Pages are organized in a three-level hierarchy:
- **Root pages**: `/about`, `/contact` 
- **Child pages**: `/services/web-development`
- **Grandchild pages**: `/services/web-development/laravel`

### Route Registration

Register the package routes in your `web.php` file. Place this at the **bottom** of your routes file since page routes with slugs will catch many URLs:

```php
// At the bottom of routes/web.php
\Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages::routes();
```

### Generating URLs

Use the facade to generate URLs for pages:

```php
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;

// Generate URL for a page
$url = FilamentFlexibleContentBlockPages::getUrl($page);

// Generate URL for a specific locale
$url = FilamentFlexibleContentBlockPages::getUrl($page, 'en');
```

In Blade templates:
```blade
<a href="{{ \Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages::getUrl($page) }}">
    {{ $page->title }}
</a>
```

### Route Helpers

The package includes two route helper implementations:

- **`PageRouteHelper`**: For non-multilingual sites with simple URLs
- **`LocalisedPageRouteHelper`**: For multilingual sites with localized URLs (e.g., `/en/about`, `/nl/over-ons`)

Configure which helper to use in your [configuration file](documentation/configuration.md#route-helper).

For advanced routing customization, custom route helpers, and controller extensions, see the [routing customization documentation](documentation/extending-and-customisation.md#routes).

## Redirects

The package includes automatic redirect management: when the slug of a page changes, a redirect from the old page 
to the new page is added. These redirects are stored in the database and are managable with the Filament resource, 
so you can add your own redirects. For example, you can add handy redirects for marketing campaigns.

We have integrated [spatie/laravel-missing-page-redirector](https://github.com/spatie/laravel-missing-page-redirector), so you can easily configure other redirects in the spatie packages config.

### Configuration

1. Prepend/append the [RedirectsMissingPages.php](src/Http/Middleware/RedirectsMissingPages.php) middleware to your global middleware stack:

```php
// bootstrap/app.php
->withMiddleware(function (Middleware $middleware) {
    $middleware->append([
        \Statikbe\FilamentFlexibleContentBlockPages\Http\Middleware\RedirectsMissingPages::class,
    ]);
})
```

2. **Optional:** If you want to hardcode a set of redirects, you can [do this in the config file of the spatie package](https://github.com/spatie/laravel-missing-page-redirector?tab=readme-ov-file#usage). Publish this package:

```php
php artisan vendor:publish --provider="Spatie\MissingPageRedirector\MissingPageRedirectorServiceProvider"
```

## Sitemap Generator

The package includes an automatic sitemap generator that creates XML sitemaps for your website with support for multilingual sites and various content types.

### Features

- **Multiple generation methods** - Manual, crawling, or hybrid approach
- **Multilingual support** - Automatic hreflang tags for alternate language versions
- **Smart priority calculation** - Homepage gets priority 1.0, parent pages 0.8, child pages 0.6
- **Dynamic change frequency** - Based on last modification date (weekly, monthly, yearly)
- **Flexible content inclusion** - Pages, routes, linkable models, and custom URLs
- **URL exclusion patterns** - Skip specific URLs or patterns from the sitemap

### Usage

Make sure the sitemap is enabled in the [configuration](documentation/configuration.md#sitemap-configuration).
Generate the sitemap manually by running:

```bash
php artisan flexible-content-block-pages:generate-sitemap
```

The sitemap will be saved to `public/sitemap.xml` and can be accessed at `https://yoursite.com/sitemap.xml`.

### Automatic Generation

You can schedule automatic sitemap generation in your `routes/console.php`:

```php
$schedule->command('flexible-content-block-pages:generate-sitemap')
         ->daily()
         ->at('03:00');
```

For advanced configuration options, generation methods, linkable models setup, and extending the sitemap generator service, 
see the [sitemap customisation documentation](documentation/extending-and-customisation.md#sitemap).

## Authorisation

Authorisation setup is not included in this package. Most projects will use an authorisation strategy project-wide, e.g. via policies.

However authorisation can be easily implemented. There are two easy strategies:

1. Use the panel and implement a simple access rule for the panel on the user model in `canAccessPanel(Panel $panel)`
2. Use a Filament authorisation library, like [Filament Shield](https://github.com/bezhanSalleh/filament-shield). 
Shield can automatically generate policies with permissions that you can link to specific roles. 

## Configuration

The package provides extensive configuration options to customize models, resources, database tables, and various features. 
You can modify the published configuration file to match your application's requirements.

For detailed configuration options and examples, see the [configuration documentation](documentation/configuration.md).

If you want to further customise or extend the functionality, have a look [at the options](documentation/extending-and-customisation.md).

## TODO's

check: 
- do install docs work
- Seppe: tailwind config complete? do we need to add flexible content blocks styling?
- Seppe: menu components ok?

menu:
- caching tree model + observer to clear cache
- Menu titels menu items

page:
- laravel scout

release:
- policies:
  - note: undeletable pages
- undeletable page toggle only for permission holder
- documentation
- Kristof: screenshots + banner + packagist + slack + filament plugin store

future:
- A simple asset manager (include or not?)
- Re-usable content blocks
- Contact form

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Sten Govaerts](https://github.com/sten)
- [All Contributors](../../contributors)

We built this on the shoulders of others by combining several Laravel packages into a cohesive CMS solution, making it opinionated.
We would like to thank the developers and contributors of the following packages:

- [artesaos/seotools](https://github.com/artesaos/seotools)
- [guava/filament-icon-picker](https://github.com/lukas-frey/filament-icon-picker)
- [mcamara/laravel-localization](https://github.com/mcamara/laravel-localization)
- [solution-forest/filament-tree](https://github.com/solutionforest/filament-tree)
- [spatie/laravel-missing-page-redirector](https://github.com/spatie/laravel-missing-page-redirector)
- [spatie/laravel-sitemap](https://github.com/spatie/laravel-sitemap)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
