# Filament Flexible Content Block Pages

[![Latest Version on Packagist](https://img.shields.io/packagist/v/statikbe/laravel-filament-flexible-content-block-pages.svg?style=flat-square)](https://packagist.org/packages/statikbe/laravel-filament-flexible-content-block-pages)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/statikbe/laravel-filament-flexible-content-block-pages/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/statikbe/laravel-filament-flexible-content-block-pages/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/statikbe/laravel-filament-flexible-content-block-pages/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/statikbe/laravel-filament-flexible-content-block-pages/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/statikbe/laravel-filament-flexible-content-block-pages.svg?style=flat-square)](https://packagist.org/packages/statikbe/laravel-filament-flexible-content-block-pages)

A simple content page management system with a flexible content block builder based on the [Filament Flexible Content Blocks](https://github.com/statikbe/laravel-filament-flexible-content-blocks).

This package aims to provide a basic, batteries-included CMS for Filament by providing page creation in Filament and 
renders web pages that can be easily extended and styled.

Other features that will be provided:
- Pages with hero, slugs, content blocks, publication options and SEO fields.
- Website: routing, blade views, CSS themes included.
- Menu builder with customisable blade templates
- Extendable settings model and Filament resource to store CMS settings and images.
- Redirect support for when slugs are renamed
- Sitemap generation
- A ready-to-use, extendable Filament panel with all CMS features implemented.
- Extendable models, resources and database tables.
- A simple asset manager (TODO)
- Re-usable content blocks (TODO)
- Contact form (TODO)

This package combines several existing packages and is therefore quite opinionated. 

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

Check [the configuration documentation}(#configuration) for more explanations on how to tweak the package.

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

## Configuration

TODO

## TODO's

- policies:
  - note: undeletable pages
- undeletable page toggle only for permission holder
- redirect controller
- tag controller
- sitemap implementation
- asset manager install in panel
- orm listeners for linkable models that are in a menu to avoid accidental deletion.
- frontend caching for menus

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
