# Filament Flexible Content Block Pages

[![Latest Version on Packagist](https://img.shields.io/packagist/v/statikbe/laravel-filament-flexible-content-block-pages.svg?style=flat-square)](https://packagist.org/packages/statikbe/laravel-filament-flexible-content-block-pages)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/statikbe/laravel-filament-flexible-content-block-pages/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/statikbe/laravel-filament-flexible-content-block-pages/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/statikbe/laravel-filament-flexible-content-block-pages/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/statikbe/laravel-filament-flexible-content-block-pages/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/statikbe/laravel-filament-flexible-content-block-pages.svg?style=flat-square)](https://packagist.org/packages/statikbe/laravel-filament-flexible-content-block-pages)

A complete CMS solution for Laravel applications built on [Filament Flexible Content Blocks](https://github.com/statikbe/laravel-filament-flexible-content-blocks). This package extends the flexible content block system into a full page management solution with routing, SEO, menus, and multilingual support.

Designed for developers who need a content management system that integrates seamlessly with existing Laravel applications while providing editors with an intuitive interface for managing pages and content.

## Key Features

- **Flexible page management** - Create pages with hero images, flexible content blocks, SEO fields, and publication controls
- **Hierarchical menu builder** - Configurable depth with drag-and-drop interface for creating navigation menus
- **Multilingual support** - Full localization with automatic route generation for multiple languages
- **SEO tools** - Automatic sitemap generation, meta tag management, and URL redirect handling
- **Ready-to-use admin interface** - Pre-configured Filament panel with all resources and management tools
- **Developer-friendly** - Extendable models, customizable templates, and comprehensive configuration options
- **Content organization** - Tag system, hierarchical page structure, and settings management

## Additional Features

- Website routing with customizable URL patterns
- Blade view components and CSS themes
- Media library integration via Spatie packages
- Automatic 301 redirects when page URLs change
- Multiple sitemap generation methods (manual, crawling, hybrid)
- Configurable content block types and layouts

This package combines several Laravel packages into a cohesive CMS solution, making it opinionated but comprehensive for typical content management needs. 

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
php artisan migrate
```

Configure the Filament tailwind styling by adding these view paths to `tailwind.config.js`:

```php
content: [
    ...
    './vendor/solution-forest/filament-tree/resources/**/*.blade.php',
    './vendor/statikbe/laravel-filament-flexible-content-block-pages/**/*.blade.php',
]
```

You can now seed the home page and default settings by running:

```bash
php artisan flexible-content-block-pages:seed
```

Further configure the third-party packages that are used. Check the installation documentation of these packages:

### [Laravel Localization](https://github.com/mcamara/laravel-localization?tab=readme-ov-file#installation):

Make sure the middlewares are properly setup if you want to use localised routes.

### [Laravel Tags](https://spatie.be/docs/laravel-tags/v4/installation-and-setup):

Publish the config and change the tag model to the package model:
```php 
[
    'tag_model' => \Statikbe\FilamentFlexibleContentBlockPages\Models\Tag::class,
]
```

Check [the configuration documentation](#configuration) for more explanations on how to tweak the package.

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="filament-flexible-content-block-pages-views"
```

## Setup in your project

### Translations

If you want translated content and routes, go through the following steps: 

1. Configure the supported locales in the Filament Flexible Content Blocks configuration
2. Configure the `route_helper` in [`filament-flexible-content-block-pages.php`](./config/filament-flexible-content-block-pages.php)

### Routes

Register the routes in your route file, probably `web.php`:

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
- **Multiple link types** - Internal routes, external URLs, and linkable models (Pages or your own project model)
- **Drag & drop management** - Intuitive tree interface for reordering and nesting items
- **Multiple menu styles** - Default, horizontal, vertical, and dropdown templates included
- **Translation support** - Multilingual menu labels with locale-aware URLs
- **Conditional visibility** - Show/hide menu items without deleting them
- **Icon support** - Optional icons for menu items (basic implementation currently)
- **Dynamic labels** - Use model titles or custom labels for linked content

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

Your models should implement the `HasMenuLabel` contract:

```php
use Statikbe\FilamentFlexibleContentBlockPages\Models\Contracts\HasMenuLabel;

class Product extends Model implements HasMenuLabel
{
    public function getMenuLabel(?string $locale = null): string
    {
        return $this->getTranslation('name', $locale ?? app()->getLocale());
    }
}
```

If you are using the Flexible Content Blocks title trait in your model, you can implement `HasMenuLabel` 
easily with [`HasTitleMenuLabelTrait`](src/Models/Concerns/HasTitleMenuLabelTrait.php).

### Customizing menu styles

The package includes several built-in menu styles, but you can easily add your own:

1. **Add new styles to config:**
```php
// config/filament-flexible-content-block-pages.php
'menu' => [
    'styles' => [
        'default',
        'horizontal', 
        'vertical',
        'dropdown',
        'mega', // Your custom style
    ],
],
```
**Tip:** Add translations if you want UX-friendly style dropdowns.

2. **Create the template files:**
```bash
# Main menu template
resources/views/vendor/filament-flexible-content-block-pages/tailwind/components/menu/mega.blade.php

# Menu item template  
resources/views/vendor/filament-flexible-content-block-pages/tailwind/components/menu/mega-item.blade.php
```

3. **Use in your templates:**
```blade
<x-flexible-pages-menu code="header" style="mega" />
```

The style can also be configured in the database model, then you can skip the `style` attribute.

See the [menu seeding documentation](documentation/seeders.md) for programmatic menu creation.

## Sitemap Generator

The package includes an automatic sitemap generator that creates XML sitemaps for your website with support for multilingual sites and various content types.

### Features

- **Multiple generation methods** - Manual, crawling, or hybrid approach
- **Multilingual support** - Automatic hreflang tags for alternate language versions
- **Smart priority calculation** - Homepage gets priority 1.0, parent pages 0.8, child pages 0.6
- **Dynamic change frequency** - Based on last modification date (weekly, monthly, yearly)
- **Flexible content inclusion** - Pages, routes, linkable models, and custom URLs
- **URL exclusion patterns** - Skip specific URLs or patterns from the sitemap
- **SEO optimization** - Includes last modification dates and proper XML structure

### Configuration

Enable and configure the sitemap generator in your config file:

```php
// config/filament-flexible-content-block-pages.php
'sitemap' => [
    'enabled' => true,
    'generator_service' => \Statikbe\FilamentFlexibleContentBlockPages\Services\SitemapGeneratorService::class,
    'method' => SitemapGeneratorMethod::MANUAL, // MANUAL, CRAWL, or HYBRID
    'include_pages' => true,
    'include_link_routes' => true,
    'include_linkable_models' => true,
    'exclude_patterns' => [
        '/admin/*',
        '/api/*',
    ],
    'custom_urls' => [
        'https://example.com/special-page',
    ],
],
```

### Generation Methods

**Manual** (`SitemapGeneratorMethod::MANUAL`):
- Generates sitemap based on database content (pages, routes, models)
- Faster and more predictable
- Best for most use cases

**Crawl** (`SitemapGeneratorMethod::CRAWL`):
- Crawls your website to discover URLs
- May find URLs not in your database
- Slower but comprehensive

**Hybrid** (`SitemapGeneratorMethod::HYBRID`):
- Combines both approaches
- Crawls first, then adds manual entries
- Most comprehensive but slowest

### Usage

Generate the sitemap manually:

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

### Linkable Models

To include your own models in the sitemap, ensure they implement [the `Linkable` contract and have a `getViewUrl()` method](https://github.com/statikbe/laravel-filament-flexible-content-blocks#linkable).

You will most likely already have added those models to the menu configuration's `linkable_models` array or 
[call-to-actions models](https://github.com/statikbe/laravel-filament-flexible-content-blocks#linkable) then they will automatically be included in the sitemap.

If you do not want your model in menus or call-to-actions, you can extend the [SitemapGeneratorService](src/Services/SitemapGeneratorService.php).

### Extending the SitemapGeneratorService

For full customization power, you can create your own sitemap generator service by extending the base class:

```php
<?php

namespace App\Services;

use Statikbe\FilamentFlexibleContentBlockPages\Services\SitemapGeneratorService;

class CustomSitemapGeneratorService extends SitemapGeneratorService
{
    protected function addCustomUrls(): void
    {
        parent::addCustomUrls();
        
        // Add your custom logic
        $this->addToSitemap(
            url: 'https://example.com/dynamic-page',
            lastModifiedAt: now(),
            priority: 0.7,
            frequency: 'weekly'
        );
    }
    
    protected function getLinkableModels(): array
    {
        $models = parent::getLinkableModels();
        
        // Add additional models not in menu/CTA config
        $models[] = \App\Models\BlogPost::class;
        $models[] = \App\Models\Event::class;
        
        return $models;
    }
    
    protected function calculatePriority($page): float
    {
        // Custom priority logic
        if ($page->is_featured) {
            return 0.9;
        }
        
        return parent::calculatePriority($page);
    }
}
```

Then update your configuration to use your custom service:

```php
// config/filament-flexible-content-block-pages.php
'sitemap' => [
    'generator_service' => \App\Services\CustomSitemapGeneratorService::class,
    // ... other config
],
```

You can override any protected method to customize the sitemap generation behavior, including priority calculation, change frequency, URL filtering, or adding entirely new content types.

## Configuration

TODO

## TODO's

check: 
- do install docs work
- Seppe: tailwind config complete? do we need to add flexible content blocks styling?
- Seppe: menu components ok?

menu: 
- Ben: menu seeder extra functions from VLWPLA
- page delete modal when page used in menu
  - orm listeners for linkable models that are in a menu to avoid accidental deletion.
- caching tree model + observer to clear cache
- Ben: add menu to default page template
- test global search and improve table search and ordering

page:
- Kristof: make table searchable, columns orderable, test global search

release:
- policies:
  - note: undeletable pages
- undeletable page toggle only for permission holder
- redirect controller
- tag controller
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

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
