---
name: setup-flexible-pages
description: Install and set up the statikbe/laravel-filament-flexible-content-block-pages CMS in a Laravel project - publishing config and migrations, registering the Filament panel and the frontend routes, Tailwind sources, localisation, tags, redirects and seeding the first pages.
---

# Setting up the Flexible Content Block Pages CMS

## When to use this skill

Use this skill when the package is being installed in a project for the first time, when the CMS
panel or the frontend page routes do not work yet, or when the user asks to "set up the CMS",
"install the pages package" or "add the CMS to this project".

For a project where the CMS already runs, use the task specific skills instead (adding a setting,
a page template, a linkable model).

## Before you start

Check what is already done instead of redoing it. Look for:

- `config/filament-flexible-content-block-pages.php` (config published),
- a migration matching `*_create_*pages_table.php` in `database/migrations`,
- `FlexibleContentBlockPagesPanel::class` in `bootstrap/providers.php`,
- `FilamentFlexibleContentBlockPages::routes();` in `routes/web.php`.

Ask the user whether the site is **multilingual** before configuring locales and the route helper.
It decides the route helper and the whole URL structure, and changing it later changes every URL.

## Steps

### 1. Install and publish

```bash
composer require statikbe/laravel-filament-flexible-content-block-pages
php artisan vendor:publish --tag="filament-flexible-content-block-pages-config"
```

If the project wants different table names, change them in the config **before** migrating.

```bash
php artisan vendor:publish --tag="filament-flexible-content-block-pages-migrations"
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="medialibrary-migrations"
php artisan migrate
```

### 2. Register the Filament panel

The package ships a pre-configured panel. Add it to `bootstrap/providers.php`:

```php
return [
    // ...
    \Statikbe\FilamentFlexibleContentBlockPages\FlexibleContentBlockPagesPanel::class,
];
```

Only build a custom panel from the individual resources when the project explicitly asks for it.

### 3. Register the frontend routes

In `routes/web.php`, **at the bottom of the file**, because the page routes catch many URLs:

```php
\Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages::routes();
```

This line must stay **outside** any `mcamara/laravel-localization` middleware group: the
localisation middleware is already included in `routes()`. Any more specific route of the project
must be registered before this line.

### 4. Localisation

For a multilingual site:

1. configure `supported_locales` in the `filament-flexible-content-blocks` config (or a service
   provider);
2. set `route_helper` in `config/filament-flexible-content-block-pages.php` to
   `LocalisedPageRouteHelper` (localised URLs, the default) or `PageRouteHelper` (single language);
3. follow the `mcamara/laravel-localization` installation and register its middleware.

Publish the package translations only when the labels must be changed:

```bash
php artisan vendor:publish --tag="filament-flexible-content-block-pages-translations"
```

### 5. Tags

`spatie/laravel-tags` must point at the package tag model, otherwise tagging breaks:

```php
// config/tags.php
'tag_model' => \Statikbe\FilamentFlexibleContentBlockPages\Models\Tag::class,
```

### 6. Tailwind sources

The frontend CSS needs the package blade files as Tailwind sources:

```css
@source "../../vendor/statikbe/laravel-filament-flexible-content-block-pages/**/*.blade.php";
@source "../../vendor/statikbe/laravel-filament-flexible-content-blocks/**/*.blade.php";
@source "../../config/filament-flexible-content-blocks.php";
```

And the Filament theme (usually `resources/css/filament/admin/theme.css`), note the deeper path
and the extra filament-tree source:

```css
@source "../../../../vendor/statikbe/laravel-filament-flexible-content-block-pages/**/*.blade.php";
@source "../../../../vendor/statikbe/laravel-filament-flexible-content-blocks/**/*.blade.php";
@source "../../../../config/filament-flexible-content-blocks.php";
@source "../../../../vendor/solution-forest/filament-tree/resources/**/*.blade.php";
```

Forgetting these gives an unstyled CMS frontend or admin, which is easy to misdiagnose as a broken
install.

### 7. Seed the first content

```bash
php artisan flexible-content-block-pages:seed
```

This creates the home page and the settings record, only when they do not exist yet.

### 8. Redirects and schedule

Add the redirect middleware (see the redirect configuration in the package README), and add the
media library maintenance tasks to `routes/console.php`:

```php
Schedule::command('media-library:clean')->weeklyOn(1, '11:00');
Schedule::command('media-library:regenerate --only-missing')->dailyAt('4:20');
```

### 9. Customising the frontend

Publish the views only when they will actually be customised:

```bash
php artisan vendor:publish --tag="filament-flexible-content-block-pages-views"
```

Published views are no longer updated with the package, so do not publish them "just in case".

## Verify

- `php artisan route:list` shows the page routes at the bottom, and the CMS panel path.
- The panel loads and shows Pages, Menus, Settings, Tags and Redirects.
- The home page renders on `/`.
- Tell the user which steps were already done and which you added.

## Gotchas

- Registering `FilamentFlexibleContentBlockPages::routes()` too early in `web.php` makes the CMS
  swallow other routes.
- Wrapping `routes()` in the localisation middleware group applies that middleware twice.
- Changing table names after migrating requires new migrations, not a config change.
- The package extends `statikbe/laravel-filament-flexible-content-blocks`: its config
  (`filament-flexible-content-blocks.php`) usually needs publishing and tweaking too, for the
  content blocks, locales and image conversions.
