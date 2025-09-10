# Extending and customisation 

In this document, we will explain how you can change behaviour or extend functionalities to match your project requirements.

## Table of content

<!--ts-->
   * [Settings](#settings)
   * [Menu Builder](#menu-builder)
      * [Customizing Menu Styles](#customizing-menu-styles)
   * [Routes](#routes)
      * [Custom Route Helpers](#custom-route-helpers)
         * [Creating Custom Route Helpers](#creating-custom-route-helpers)
         * [Built-in Route Helpers](#built-in-route-helpers)
      * [Custom Controllers](#custom-controllers)
         * [Extending the Page Controller](#extending-the-page-controller)
         * [Using Custom Controllers with Route Helpers](#using-custom-controllers-with-route-helpers)
         * [Extending the SEO implementation](#extending-the-seo-implementation)
      * [Advanced Route Configuration](#advanced-route-configuration)
         * [Custom Route Middleware](#custom-route-middleware)
         * [Route Model Binding](#route-model-binding)
   * [Sitemap](#sitemap)
      * [Configuration](#configuration)
      * [Generation Methods](#generation-methods)
      * [Linkable Models](#linkable-models)
      * [Extending the SitemapGeneratorService](#extending-the-sitemapgeneratorservice)
   * [SEO tag pages](#seo-tag-pages)
      * [Customizing Tag Page Views](#customizing-tag-page-views)
      * [Extending Tag Page Content](#extending-tag-page-content)
      * [SEO tag page controller customisation](#seo-tag-page-controller-customisation)
      * [Advanced Extensions](#advanced-extensions)

<!-- Created by https://github.com/ekalinin/github-markdown-toc -->
<!-- Added by: sten, at: Thu Sep 11 00:02:20 CEST 2025 -->

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

2. **Extend the Settings model** by adding constants for easy referral and updating `$translatable`, if needed:
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

3. **Extend the SettingsResource** by overriding `getExtraFormTabs()`:
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

4. **Configure the extended model and resource** in your config file:
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

2. **Create the template files:**
```bash
# Main menu template
resources/views/vendor/filament-flexible-content-block-pages/tailwind/components/menu/mega.blade.php

# Menu item template  
resources/views/vendor/filament-flexible-content-block-pages/tailwind/components/menu/mega-item.blade.php
```

3. **Use in your templates:**
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
