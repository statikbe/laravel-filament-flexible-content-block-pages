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

You can now seed the home page and default settings by running:

```bash
php artisan flexible-content-block-pages:seed
```

Further configure the third-party packages that are used. Check the installation documentation of these packages:

- [Laravel Localization](https://github.com/mcamara/laravel-localization?tab=readme-ov-file#installation): 
  Make sure the middlewares are properly setup if you want to use localised routes.


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

## Configuration

TODO

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
