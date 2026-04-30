# Extending and customisation

In this document, we will explain how you can change behaviour or extend functionalities to match your project requirements.

## Table of content

<!--ts-->

- [Extending and customisation](#extending-and-customisation)
  - [Table of content](#table-of-content)
  - [Settings](#settings)
  - [Custom Page Types](#custom-page-types)
    - [Overview](#overview)
    - [Step 1: Create the Migration](#step-1-create-the-migration)
    - [Step 2: Create the Model](#step-2-create-the-model)
    - [Step 3: Register the Morph Map](#step-3-register-the-morph-map)
    - [Step 4: Create the Filament Resource](#step-4-create-the-filament-resource)
    - [Step 5: Create the Resource Page Classes](#step-5-create-the-resource-page-classes)
    - [Step 6: Configure the Page Resource](#step-6-configure-the-page-resource)
  - [Menu Builder](#menu-builder)
    - [Customizing Menu Styles](#customizing-menu-styles)
  - [Routes](#routes)
    - [Custom Route Helpers](#custom-route-helpers)
      - [Creating Custom Route Helpers](#creating-custom-route-helpers)
      - [Built-in Route Helpers](#built-in-route-helpers)
    - [Custom Controllers](#custom-controllers)
      - [Extending the Page Controller](#extending-the-page-controller)
      - [Using Custom Controllers with Route Helpers](#using-custom-controllers-with-route-helpers)
      - [Extending the SEO implementation](#extending-the-seo-implementation)
    - [Advanced Route Configuration](#advanced-route-configuration)
      - [Custom Route Middleware](#custom-route-middleware)
      - [Route Model Binding](#route-model-binding)
  - [Sitemap](#sitemap)
    - [Configuration](#configuration)
    - [Generation Methods](#generation-methods)
    - [Linkable Models](#linkable-models)
    - [Extending the SitemapGeneratorService](#extending-the-sitemapgeneratorservice)
  - [SEO tag pages](#seo-tag-pages)
    - [Customizing Tag Page Views](#customizing-tag-page-views)
    - [Extending Tag Page Content](#extending-tag-page-content)
    - [SEO tag page controller customisation](#seo-tag-page-controller-customisation)
    - [Advanced Extensions](#advanced-extensions)

<!-- Created by https://github.com/ekalinin/github-markdown-toc -->
<!-- Added by: sten, at: Mon Sep 29 23:52:46 CEST 2025 -->

<!--te-->

## Settings

One of the most likely extensions you will want to do is add your own settings fields.

Here is a step by step guide to add custom settings fields:

1. **Create a migration** to add new columns:

```php
Schema::table(FilamentFlexibleContentBlockPages::config()->getSettingsTable(), function (Blueprint $table) {
    $table->string('custom_field')->nullable();
    $table->json('translatable_field')->nullable(); // for translatable fields
});
```

1. **Extend the Settings model** by adding constants for easy referral and updating `$translatable`, if needed:

```php
class CustomSettings extends \Statikbe\FilamentFlexibleContentBlockPages\Models\Settings
{
    const SETTING_CUSTOM_FIELD = 'custom_field';

    protected $translatable = [
        parent::SETTING_FOOTER_COPYRIGHT,
        parent::SETTING_CONTACT_INFO,
        self::SETTING_CUSTOM_FIELD, // if translatable
    ];
}
```

1. **Extend the SettingsResource** by overriding `getExtraFormTabs()`:

```php
class CustomSettingsResource extends \Statikbe\FilamentFlexibleContentBlockPages\Resources\SettingsResource
{
    protected static function getExtraFormTabs(): array
    {
        return [
            Tab::make('Custom Tab')->schema([
                TextInput::make(CustomSettings::SETTING_CUSTOM_FIELD)
                    ->label('Custom Field')
                    ->required(),
            ]),
        ];
    }
}
```

1. **Configure the extended model and resource** in your config file:

```php
// config/filament-flexible-content-block-pages.php
'models' => [
    // ...
    'settings' => \App\Models\CustomSettings::class,
],

'resources' => [
    // ...
    'settings' => \App\Resources\CustomSettingsResource::class,
],
```

## Custom Page Types

### Overview

Beyond the default `Page` model, you can create entirely separate page types that each have their own database table, model, Filament resource, and content blocks — while still reusing all the infrastructure the package provides.

This is useful when you need a distinct set of pages with different content blocks, different routing logic, or different admin panel behaviour.

The high-level steps:

1. Create a dedicated database table (migration)
2. Create a model extending the package's `Page` model
3. Register a morph map entry for the new model
4. Create a Filament resource extending `PageResource`
5. Create the resource page classes (List, Create, Edit)
6. Add `page_resource` configuration for the new model

### Step 1: Create the Migration

Create a migration for a new table (e.g. `pages_report`). The schema should mirror the columns you need from the default pages table. Refer to the [default pages migration](../database/migrations/create_pages_table.php.stub) for the full column list — include the translatable JSON columns (`title`, `slug`, `content_blocks`, etc.) and any optional columns (SEO, overview, hero, author, parent) that your page type needs.

> **Note:** All JSON columns are translatable via Spatie's laravel-translatable. If your page type uses the parent-child feature, make sure the `parent_id` foreign key references your own table, not the default `pages` table.

### Step 2: Create the Model

Create a model extending the package's `Page` model. There are several critical overrides:

```php
class ReportPage extends \Statikbe\FilamentFlexibleContentBlockPages\Models\Page
{
    public function getTable()
    {
        return 'pages_report';
    }

    public function getMorphClass()
    {
        return 'pages_report';
    }

    public static function registerContentBlocks(): array
    {
        return [
            TextImageBlock::class,
            // your content blocks
        ];
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->resolveRouteBindingQuery($this->newQuery(), $value, $field)->first();
    }

    public function getViewUrl(?string $locale = null): string
    {
        // your URL generation logic
    }

    public function getPreviewUrl(?string $locale = null): string
    {
        return $this->getViewUrl($locale);
    }
}
```

Key points:

- **`getTable()`** — must point to your custom table, not the default pages table.
- **`getMorphClass()`** — must return a **unique** string. The base `Page` model returns `flexiblePagesPrefix('page')`. If your custom page type inherits that same morph class, polymorphic relations (media library, menus, etc.) will conflict between page types. This value must match the morph map key in step 3.
- **`registerContentBlocks()`** — defines which content blocks are available for this page type, overriding the defaults.
- **`resolveRouteBinding()`** — override to ensure Laravel resolves route bindings from your table, not the default pages table.
- **`getViewUrl()` / `getPreviewUrl()`** — implement URL generation for your page type's routing.

### Step 3: Register the Morph Map

Laravel's [enforced morph map](https://laravel.com/docs/eloquent-relationships#custom-polymorphic-types) must include your custom page type. Without this, you will get a `ClassMorphViolationException` when the model is used in polymorphic relations (media, menus, content blocks, etc.).

In your `AppServiceProvider::boot()`:

```php
Relation::enforceMorphMap([
    // ... your other models
    'pages_report' => \App\Models\ReportPage::class,
]);
```

The key (`'pages_report'`) **must** match what `getMorphClass()` returns on your model.

### Step 4: Create the Filament Resource

Create a new Filament resource extending `PageResource`. The critical override is `getModel()`:

```php
class ReportPageResource extends \Statikbe\FilamentFlexibleContentBlockPages\Resources\PageResource
{
    public static function getModel(): string
    {
        return \App\Models\ReportPage::class;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReportPages::route('/'),
            'create' => CreateReportPage::route('/create'),
            'edit' => EditReportPage::route('/{record:id}/edit'),
        ];
    }
}
```

You can optionally override navigation methods (`getNavigationGroup()`, `getNavigationLabel()`, `getNavigationSort()`) and the `form()` method. When overriding the form, the parent resource provides helper methods you can reuse: `static::getGeneralTabFields()`, `static::getContentTabFields()`, `static::getOverviewTabFields()`, `static::getSEOTabFields()`, `static::getAdvancedTabFields()`.

### Step 5: Create the Resource Page Classes

Each Filament resource needs List, Create, and Edit page classes. These extend the package's base page classes and point back to your custom resource via `getResource()`.

The **List** and **Create** pages only need the `getResource()` override:

```php
class ListReportPages extends \Statikbe\FilamentFlexibleContentBlockPages\Resources\PageResource\Pages\ListPages
{
    public static function getResource(): string
    {
        return ReportPageResource::class;
    }
}
```

The **Edit** page additionally needs `getRecord()` and `getModel()` overrides to ensure Filament resolves the correct model class instead of the default Page:

```php
class EditReportPage extends \Statikbe\FilamentFlexibleContentBlockPages\Resources\PageResource\Pages\EditPage
{
    public static function getResource(): string
    {
        return ReportPageResource::class;
    }

    public function getRecord(): Model
    {
        return \App\Models\ReportPage::find($this->record->id);
    }

    public function getModel(): string
    {
        return \App\Models\ReportPage::class;
    }
}
```

### Step 6: Configure the Page Resource

Add a `page_resource` configuration entry keyed by your model class. This controls which features are enabled in the admin panel for this page type:

```php
// config/filament-flexible-content-block-pages.php
'page_resource' => [
    \App\Models\Page::class => [ /* ... */ ],

    \App\Models\ReportPage::class => [
        'enable_hero_call_to_actions' => false,
        'enable_hero_video_url' => false,
        'enable_author' => false,
        'enable_page_tree' => false,
        'enable_undeletable' => true,
        // ... see default page config for all available options
    ],
],
```

The package reads these settings to determine which form fields, table columns, and actions to show for each page type. This is how a single `PageResource` base class can serve multiple page types with different feature sets.

> **Note:** You do _not_ need to add your custom page type to the top-level `models` or `resources` config arrays — those are for the _default_ page type used by the package's routing and URL generation. Your custom resource is registered as a standalone Filament resource.

## Menu Builder

### Customizing Menu Styles

If needed, you can easily add your own menu styles in addition to the `default` style, e.g. the 'mega' menu style:

1. **Add new styles to config:**

```php
// config/filament-flexible-content-block-pages.php
'menu' => [
    'styles' => [
        'default',
        'mega', // Your custom style
    ],
],
```

**Tip:** Add translations if you want UX-friendly style dropdowns.

1. **Create the template files:**

```bash
# Main menu template
resources/views/vendor/filament-flexible-content-block-pages/tailwind/components/menu/mega.blade.php

# Menu item template
resources/views/vendor/filament-flexible-content-block-pages/tailwind/components/menu/mega-item.blade.php
```

1. **Use in your templates:**

```blade
<x-flexible-pages-menu code="HEADER" style="mega" />
```

The style can also be configured in the database model, then you can skip the `style` attribute.

## Routes

### Custom Route Helpers

The package provides two built-in route helpers, but you can create your own for custom URL patterns.

#### Creating Custom Route Helpers

Implement the `HandlesPageRoutes` contract:

```php
<?php

namespace App\Services;

use Statikbe\FilamentFlexibleContentBlockPages\Routes\Contracts\HandlesPageRoutes;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Tag;

class CustomRouteHelper implements HandlesPageRoutes
{
    public function definePageRoutes(): void
    {
        // Define your custom route patterns
        Route::get('/custom-pattern/{slug}', [CustomPageController::class, 'show'])
            ->name('custom.page');
    }

    public function defineSeoTagRoutes(): void
    {
        // Define custom tag routes if needed
        Route::get('/tags/{tag:slug}', [CustomTagController::class, 'show'])
            ->name('custom.tag');
    }

    public function getUrl(Page $page, ?string $locale = null): string
    {
        // Implement your custom URL generation logic
        return route('custom.page', ['slug' => $page->slug]);
    }

    public function getSeoTagUrl(Tag $tag, ?string $locale = null): string
    {
        return route('custom.tag', ['tag' => $tag]);
    }
}
```

Then update your configuration:

```php
// config/filament-flexible-content-block-pages.php
'route_helper' => \App\Services\CustomRouteHelper::class,
```

#### Built-in Route Helpers

**PageRouteHelper** (non-multilingual):

```php
'route_helper' => \Statikbe\FilamentFlexibleContentBlockPages\Routes\PageRouteHelper::class,
```

**LocalisedPageRouteHelper** (multilingual):

```php
'route_helper' => \Statikbe\FilamentFlexibleContentBlockPages\Routes\LocalisedPageRouteHelper::class,
```

### Custom Controllers

#### Extending the Page Controller

Create a custom controller by extending the base `PageController`:

```php
<?php

namespace App\Http\Controllers;

use Statikbe\FilamentFlexibleContentBlockPages\Http\Controllers\PageController as BasePageController;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomPageController extends BasePageController
{
    public function show(Request $request, ...$slugs): View
    {
        $page = $this->getPageFromSlugs($slugs);

        // Add your custom logic here
        $customData = $this->getCustomData($page);

        return $this->renderPage($page, [
            'customData' => $customData,
        ]);
    }

    protected function getCustomData(Page $page): array
    {
        // Your custom data logic
        return [
            'relatedPages' => $page->getRelatedPages(),
            'analytics' => $this->getAnalyticsData($page),
        ];
    }
}
```

#### Using Custom Controllers with Route Helpers

Update your custom route helper to use your controller:

```php
public function definePageRoutes(): void
{
    Route::get('/{slug}', [CustomPageController::class, 'show'])
        ->where('slug', '.*')
        ->name(static::ROUTE_PAGE);
}
```

#### Extending the SEO implementation

For advanced SEO customization, extend the functions in `AbstractSeoPageController`:

```php
<?php

namespace App\Http\Controllers;

use Statikbe\FilamentFlexibleContentBlockPages\Http\Controllers\PageController;

class CustomSeoController extends PageController
{
    protected function getMetaTitle(string $pageTitle): string
    {
        // Custom meta title logic
        return $pageTitle . ' | ' . config('app.name');
    }

    protected function getMetaDescription(?string $description): ?string
    {
        // Custom meta description logic
        return $description ?? 'Default description for ' . config('app.name');
    }
}
```

### Advanced Route Configuration

#### Custom Route Middleware

Add custom middleware to page routes:

```php
// in your routes file:

Route::middleware(['custom.middleware'])->group(function () {
    \Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages::routes();
});

```

#### Route Model Binding

Customize route model binding for pages:

```php
// In your RouteServiceProvider or custom route helper
Route::bind('page', function ($value) {
    return Page::where('slug->'.app()->getLocale(), $value)
        ->published()
        // ... add other query conditions
        ->firstOrFail();
});
```

## Sitemap

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
- Requires chromium

**Hybrid** (`SitemapGeneratorMethod::HYBRID`):

- Combines both approaches
- Crawls first, then adds manual entries
- Most comprehensive but slowest

### Linkable Models

To include your own models in the sitemap, ensure they implement [the `Linkable` contract and have a `getViewUrl()` method](https://github.com/statikbe/laravel-filament-flexible-content-blocks#linkable).

You will most likely already have added those models to the menu configuration's `linkable_models` array or
[call-to-actions models](https://github.com/statikbe/laravel-filament-flexible-content-blocks#linkable) then they will automatically be included in the sitemap.

If you do not want your model in menus or call-to-actions, you can extend the [SitemapGeneratorService](../src/Services/SitemapGeneratorService.php).

### Extending the SitemapGeneratorService

For full customization power, you can create your own sitemap generator service by extending the base class or completely
implementing a new service by implementing [GeneratesSitemap](../src/Services/Contracts/GeneratesSitemap.php):

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

## SEO tag pages

### Customizing Tag Page Views

Override the default tag page template by publishing the view:

```bash
php artisan vendor:publish --tag=filament-flexible-content-block-pages-views
```

Then customize the template at:

```
resources/views/vendor/filament-flexible-content-block-pages/tailwind/pages/tag_index.blade.php
```

You can then adjust the header and item layout.
Currently, the view does not focus on styling, only on proper HTML structure, because these pages are meant to be read by search engines.

### Extending Tag Page Content

**Add custom models** to tag pages by updating the configuration:

```php
// config/filament-flexible-content-block-pages.php
'tag_pages' => [
    'models' => [
        'enabled' => [
            \Statikbe\FilamentFlexibleContentBlockPages\Models\Page::class,
            \App\Models\BlogPost::class,
            \App\Models\Product::class,
            \App\Models\Event::class,
        ],
    ],
],
```

**Model requirements:**

- Must use the `HasTags` trait from spatie/laravel-tags
- Should implement a `scopePublished()` method for content filtering (or you need to customise the controller)
- Should have a `getViewUrl()` method for link generation by implementing the `Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\Linkable` interface

**Example model setup:**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Tags\HasTags;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\Linkable;

class BlogPost extends Model implements Linkable
{
    use HasTags;

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->where('published_at', '<=', now());
    }

    public function getViewUrl(?string $locale = null): string
    {
        return route('blog.show', $this);
    }

    public function getPreviewUrl(?string $locale = null): string
    {
        return $this->getViewUrl($locale);
    }
}
```

### SEO tag page controller customisation

**Extend the SeoTagController** for custom SEO handling:

```php
<?php

namespace App\Http\Controllers;

use Statikbe\FilamentFlexibleContentBlockPages\Http\Controllers\SeoTagController as BaseSeoTagController;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Tag;
use Illuminate\View\View;

class CustomSeoTagController extends BaseSeoTagController
{
    public function index(Tag $tag): View
    {
        $view = parent::index($tag);

        // Add custom SEO data
        SEOTools::opengraph()->addImage($this->getCustomTagImage($tag));
        SEOTools::twitter()->setImage($this->getCustomTagImage($tag));

        // Add custom view data
        $view->with([
            'customData' => $this->getCustomTagData($tag),
            'relatedTags' => $this->getRelatedTags($tag),
        ]);

        return $view;
    }

    private function getCustomTagImage(Tag $tag): ?string
    {
        // Custom logic for tag images
        return $tag->tagType->image_url ?? asset('images/default-tag.jpg');
    }

    private function getCustomTagData(Tag $tag): array
    {
        return [
            'totalViews' => $this->getTagPageViews($tag),
            'lastUpdated' => $this->getLastContentUpdate($tag),
        ];
    }
}
```

### Advanced Extensions

**Custom URL patterns** by customising your route helper:

```php
public function defineSeoTagRoutes(): void
{
    Route::get('/topics/{tag:slug}', [CustomSeoTagController::class, 'index'])
        ->name(static::ROUTE_SEO_TAG_PAGE);
}

public function getTagPageUrl(Tag $tag, ?string $locale = null): string
{
    return route(static::ROUTE_SEO_TAG_PAGE, ['tag' => $tag->slug]);
}
```
