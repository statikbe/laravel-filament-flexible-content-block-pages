# Frontend Templates and Components

This document covers frontend templating, theme customization, and available Blade components for rendering pages, menus and the language switch.

## Table of Contents

<!--ts-->
   * [General remark](#general-remark)
      * [Publishing Views](#publishing-views)
      * [View Structure](#view-structure)
      * [Theme](#theme)
   * [Available Blade Components](#available-blade-components)
      * [Menu Components](#menu-components)
         * [&lt;x-flexible-pages-menu&gt;](#x-flexible-pages-menu)
      * [Language Switch](#language-switch)
         * [&lt;x-flexible-pages-language-switch&gt;](#x-flexible-pages-language-switch)
      * [Base page Layout](#base-page-layout)
         * [&lt;x-flexible-pages-base-layout&gt;](#x-flexible-pages-base-layout)
   * [Page Templates](#page-templates)
      * [Custom Page Templates](#custom-page-templates)
   * [Styling Integration](#styling-integration)
      * [Tailwind CSS Configuration](#tailwind-css-configuration)
   * [Frontend Best Practices](#frontend-best-practices)
      * [Performance](#performance)
      * [Accessibility](#accessibility)

<!-- Created by https://github.com/ekalinin/github-markdown-toc -->
<!-- Added by: sten, at: Mon Sep 29 23:52:46 CEST 2025 -->

<!--te-->

## General remark

This package provides only Blade templates. These templates are only a starting point to start implementing your own 
project requirements. The idea is that the backend is flexible and easily extendable, while the frontend requirements 
will differ most likely between projects, so they (currently) only provide basic styling. 

### Publishing Views

Publish the package views to customize templates:

```bash
php artisan vendor:publish --tag="filament-flexible-content-block-pages-views"
```

This creates the views in:
```
resources/views/vendor/filament-flexible-content-block-pages/
└── tailwind/
    ├── components/
    │   ├── layouts/
    │   │   └── base.blade.php
    │   └── menu/
    │       ├── default.blade.php
    │       └── default-item.blade.php
    └── pages/
        ├── show.blade.php
        └── tag_index.blade.php
```

### View Structure

**Base Layout** (`components/layouts/base.blade.php`):
- HTML5 document structure
- SEO meta tags integration
- Responsive viewport setup
- Tailwind CSS integration

**Page Template** (`pages/show.blade.php`):
- Page content rendering
- Hero section display
- Content blocks rendering
- SEO structured data

**Menu Templates** (`components/menu/`):
- Hierarchical menu rendering
- Active state handling
- Multi-level dropdown support

### Theme

We provide a basic [TailwindCSS](https://tailwindcss.com/) theme. 
But you can [create another custom theme](./configuration.md#theme-configuration).

## Available Blade Components

The package provides several Blade components for common frontend functionality:

### Menu Components

#### `<x-flexible-pages-menu>`

Renders hierarchical menus with full customization support:

```blade
<x-flexible-pages-menu
    code="HEADER"
    style="default"
    ulClass="flex flex-row justify-start items-center gap-x-4"
    itemLinkClass="text-black hover:text-primary hover:underline"
    currentItemLinkClass="text-grey hover:no-underline"
    childUlClass="absolute top-full left-0 bg-white shadow-lg"
    childItemLinkClass="block px-4 py-2 hover:bg-gray-100"
/>
```

**Available parameters:**
- `code` - Menu code of the menu model (required)
- `style` - Menu style template (default: 'default')
- `ulClass` - CSS classes for `<ul>` elements
- `itemLinkClass` - CSS classes for menu item links
- `currentItemLinkClass` - CSS classes for active/current page links
- `childUlClass` - CSS classes for submenu `<ul>` elements
- `childItemLinkClass` - CSS classes for submenu item links
- `itemClass` - CSS classes for `<li>` elements
- `childItemClass` - CSS classes for submenu `<li>` elements

**Creating custom menu styles:**

1. Add the style to your configuration:
```php
// config/filament-flexible-content-block-pages.php
'menu' => [
    'styles' => ['default', 'mega', 'sidebar'],
],
```

2. Create the corresponding templates and make a custom implementation based on the `default` example:
```
resources/views/vendor/filament-flexible-content-block-pages/tailwind/components/menu/mega.blade.php
resources/views/vendor/filament-flexible-content-block-pages/tailwind/components/menu/mega-item.blade.php
```

### Language Switch

#### `<x-flexible-pages-language-switch>`

Renders a navigation component with language switching links for multilingual sites:

```blade
<x-flexible-pages-language-switch
    class="flex gap-2"
/>
```

The current implementation is very basic. Maybe we will add a more advanced component in the future.

### Base page Layout

#### `<x-flexible-pages-base-layout>`

Provides a base HTML structure skeleton with SEO meta tags that is used to render the pages. 
For example, you can implement a custom page like this:

```blade
<x-flexible-pages-base-layout>
    <header>
        <x-flexible-pages-menu code="HEADER" />
        <x-flexible-pages-language-switch />
    </header>

    <main>
        <x-flexible-hero :page="$page"/>
        <x-flexible-content-blocks :page="$page"/>
    </main>

    <footer>
        <x-flexible-pages-menu code="FOOTER" />
    </footer>
</x-flexible-pages-base-layout>
```

## Page Templates

### Custom Page Templates

You can create custom templates for a specific page. You can use the page code to create [a mapping in the configuration](./configuration.md#page-templates).

**1. Create template file:**
```blade
{{-- resources/views/pages/product-template.blade.php --}}
<x-flexible-pages-base-layout>
    <div class="product-page">
        <div class="product-hero">
            {{-- Custom product page layout --}}
        </div>
        
        <div class="product-content">
            {!! $page->renderContentBlocks() !!}
        </div>
        
        <div class="product-sidebar">
            {{-- Additional product information --}}
        </div>
    </div>
</x-flexible-pages-base-layout>
```

**2. Register in configuration:**
```php
// config/filament-flexible-content-block-pages.php
'page_templates' => [
    'default' => 'filament-flexible-content-block-pages::tailwind.pages.show',
    'product' => 'pages.product-template',
    'landing' => 'pages.landing-template',
],
```

**NB:** `product` and `landing` are codes on the Page model.

## Styling Integration

### Tailwind CSS Configuration

Ensure Tailwind CSS includes the required package paths in your `tailwind.config.js` which is used by the front-end.
See tailwind installation steps in the [main README.md](../README.md)

## Frontend Best Practices

Here are some snippets with ideas for frontend optimisations.

### Performance

**Lazy loading:**
```blade
{{-- Lazy load content blocks --}}
<div class="content-blocks" x-data="{ loaded: false }" x-intersect="loaded = true">
    <template x-if="loaded">
        {!! $page->renderContentBlocks() !!}
    </template>
</div>
```

### Accessibility

**Semantic HTML:**

We have tried to optimise the flexible content block templates for accessibility. But in the page templates, you might still need to pay attention.
```blade
<article role="main" aria-labelledby="page-title">
    <header>
        <h1 id="page-title">{{ $page->title }}</h1>
        @if($page->publishing_begins_at)
            <time datetime="{{ $page->publishing_begins_at->toISOString() }}">
                {{ $page->publishing_begins_at->format('F j, Y') }}
            </time>
        @endif
    </header>

    <main>
        {!! $page->renderContentBlocks() !!}
    </main>
</article>
```
