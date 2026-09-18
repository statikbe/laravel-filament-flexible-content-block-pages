---
name: add-flexible-pages-linkable-model
description: Make an Eloquent model linkable in the CMS of statikbe/laravel-filament-flexible-content-block-pages, so editors can select it in menu items and call to action blocks, and so it is included in the sitemap. Implements the HasMenuLabel and Linkable contracts and registers the model in the config.
---

# Making a model linkable in the CMS

## When to use this skill

Use this skill when a project model (a blog post, event, product, category, ...) must be
selectable in the **menu builder**, linkable from **call to action blocks**, or included in the
**sitemap**. Signs: "editors must be able to add news items to the menu", "link to a product from a
block", "add the events to the sitemap".

For linking to CMS pages, nothing is needed: `Page` is linkable out of the box.

## What "linkable" means here

Two contracts, one config entry:

- `Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\Linkable` gives the model a URL:
  `getViewUrl()` and `getPreviewUrl()`.
- `Statikbe\FilamentFlexibleContentBlockPages\Models\Contracts\HasMenuLabel` extends `Linkable` and
  adds what the menu builder needs: `menuItem()`, `getMenuLabel()` and `scopeSearchForMenuItems()`.
- `menu.linkable_models` in `config/filament-flexible-content-block-pages.php` lists the models.

The same config list feeds the sitemap, so registering a model for the menu also puts it in the
sitemap when `sitemap.include_linkable_models` is true.

## Ask before you start

- **Which URL does the model have?** The model needs a real frontend route to link to. If there is
  no route yet, that has to be built first, ask the user.
- **Is there a preview URL for unpublished records?** When the model has a published state,
  `getPreviewUrl()` should show unpublished records to logged in editors. If not, it can return the
  same URL as `getViewUrl()`.

## Steps

### 1. Implement the contract

Most models can use the ready made traits. `HasTitleMenuLabelTrait` implements `getMenuLabel()` and
`scopeSearchForMenuItems()` for a model with a translatable `title`, and includes
`HasMenuItemTrait` which implements the `menuItem()` morph relation.

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Concerns\HasTitleMenuLabelTrait;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Contracts\HasMenuLabel;

class NewsItem extends Model implements HasMenuLabel
{
    use HasTitleMenuLabelTrait;
    use HasTranslations;

    protected $translatable = ['title', 'slug'];

    public function getViewUrl(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();

        return route('news.show', ['newsItem' => $this->getTranslation('slug', $locale)]);
    }

    public function getPreviewUrl(?string $locale = null): string
    {
        return $this->getViewUrl($locale);
    }
}
```

When the model has no translatable `title`, implement `getMenuLabel()` and
`scopeSearchForMenuItems()` yourself and use `HasMenuItemTrait` for the relation only:

```php
use HasMenuItemTrait;

public function getMenuLabel(?string $locale = null): string
{
    return $this->name;
}

public function scopeSearchForMenuItems($query, string $search)
{
    return $query->where('name', 'like', "%{$search}%");
}
```

### 2. Register the model

```php
// config/filament-flexible-content-block-pages.php
'menu' => [
    'linkable_models' => [
        Page::class,
        \App\Models\NewsItem::class,
    ],
],
```

This is a **flat list of model class strings**. A model that does not implement `HasMenuLabel` is
silently skipped: it simply never appears as a menu item type, without any error.

### 3. Add a morph alias

Menu items store the link type as the model's morph class, so give the model a morph alias instead
of storing the fully qualified class name in the database:

```php
// in a service provider
Relation::morphMap([
    'news_item' => \App\Models\NewsItem::class,
]);
```

### 4. Call to action blocks (optional)

Linking from CTA blocks is configured in the **parent package**, in the `call_to_action_models` key
of `config/filament-flexible-content-blocks.php`. The sitemap merges those models with
`menu.linkable_models`. A custom block that links to the model uses the same `Linkable` contract,
see the `filament-flexible-content-blocks-custom-block` skill of the parent package.

### 5. Verify

- In the CMS, open a menu and add an item: the model appears as a link type and its records are
  searchable by label.
- The saved menu item renders a correct URL on the frontend.
- `php artisan flexible-content-block-pages:generate-sitemap` (or the configured schedule) includes
  the model's URLs.

## Gotchas

- `scopeSearchForMenuItems()` is called statically by the menu item type
  (`NewsItem::searchForMenuItems($search)`), so it must be a real Eloquent scope on the model.
- `getMenuLabel()` must always return a string, never null, or the menu builder shows empty options.
- `getViewUrl()` is called for every locale of a multilingual site: use the `$locale` argument
  instead of `app()->getLocale()`.
- Registering the model in the menu config also adds it to the sitemap. If it should not be in the
  sitemap, either exclude it there or set `sitemap.include_linkable_models` to false.
