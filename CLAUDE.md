# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is a Laravel package that provides a CMS system with flexible content blocks for Filament. It extends the `statikbe/laravel-filament-flexible-content-blocks` package to create a complete page management system with routing, SEO, redirects, and tagging functionality.

## Development Commands

### Testing
```bash
# Run tests with Pest
composer test
# Run tests with coverage
composer test-coverage
```

### Code Quality
```bash
# Run PHPStan static analysis
composer analyse
# Format code with Laravel Pint
composer format
```

### Package Setup
```bash
# Prepare/discover package after composer changes
composer prepare
```

### Seeding
```bash
# Seed default pages and settings
php artisan flexible-content-block-pages:seed
```

## Architecture Overview

### Core Models
- **Page** (`src/Models/Page.php`): Main content model with flexible content blocks, multilingual support, SEO fields, and hierarchical structure (parent/child relationships up to 3 levels deep)
- **Settings** (`src/Models/Settings.php`): Global CMS settings and configuration
- **Redirect** (`src/Models/Redirect.php`): URL redirect management
- **Tag/TagType** (`src/Models/Tag.php`, `src/Models/TagType.php`): Content tagging system
- **Menu/MenuItem** (`src/Models/Menu.php`, `src/Models/MenuItem.php`): Hierarchical menu system using solution-forest/filament-tree with parent_id/order structure

### Key Components
- **Filament Resources**: Located in `src/Resources/` - provide admin interface for all models
- **Route Helpers**: `src/Routes/` handles URL generation for pages, including localized URLs via `LocalisedPageRouteHelper`
- **Page Controller**: `src/Http/Controllers/PageController.php` handles frontend page rendering with SEO optimization
- **Flexible Content Blocks**: Integrates with the parent package for flexible content building

### Configuration
- Main config: `config/filament-flexible-content-block-pages.php`
- Key settings: models, table names, resources, panel configuration, SEO defaults, route helper selection
- Template overrides can be configured via `page_templates` array

### Content Block System
Pages use the flexible content blocks system from the parent package, allowing:
- Rich content building with various block types
- Multilingual content support
- Hero images and overview fields
- SEO meta fields and social media optimization

### Routing Architecture
- Three-level hierarchical pages: grandparent → parent → child
- Localized routing support via `mcamara/laravel-localization`
- Custom route helpers for different URL patterns
- Frontend routes registered via `FilamentFlexibleContentBlockPages::routes()`

### Caching Strategy
- SEO image dimensions cached for 8 hours (`PageController::CACHE_SEO_IMAGE_TTL`)
- Taggable cache implementation in `src/Cache/TaggableCache.php`

### Key Dependencies
- Laravel Filament for admin interface
- Spatie packages for tags, media library, and redirects
- Laravel Localization for multilingual support
- SEOTools for meta tag and OpenGraph management
- solution-forest/filament-tree for hierarchical menu structure

## Menu Builder System

### Current Status
The menu builder is **fully implemented** using solution-forest/filament-tree with the following components:

**✅ Completed:**
- Menu and MenuItem models with solution-forest/filament-tree integration
- Database migrations with parent_id/order structure (migrated from kalnoy/nestedset)
- MenuResource with CRUD operations and enhanced management
- ManageMenuItems page with drag-and-drop tree interface using solution-forest/filament-tree
- Complete MenuItem form with dynamic type selection (URL, Route, Linkable Model)
- Linkable model integration with polymorphic relationships
- Enhanced tree display with icons and translated model labels
- Translation support for all menu components
- Frontend menu rendering components for various styles

**🔧 Key Features:**
- **Enhanced Tree Interface**: Icons indicate item type and visibility (eye-slash for hidden items)
- **Resource Integration**: Uses Filament resource labels and icons for linkable models
- **Smart Descriptions**: Shows route URLs instead of names, translated model labels
- **Flexible Configuration**: Structured linkable_models config with class/resource mapping
- **Translation Support**: Full multilingual support with translatable labels

### Architecture
- **Menu Model**: Container with name, code, description, and configurable styles
- **MenuItem Model**: Simple tree structure (parent_id/order) with ModelTree trait
- **ManageMenuItems Page**: Full tree management with create/edit/delete actions
- **MenuItemForm**: Dynamic form supporting URL, Route, and Model linking types
- **Frontend Components**: Menu rendering with multiple style support

### Configuration
```php
'menu' => [
    'max_depth' => 2,
    'linkable_models' => [
        [
            'class' => \Statikbe\FilamentFlexibleContentBlockPages\Models\Page::class,
            'resource' => \Statikbe\FilamentFlexibleContentBlockPages\Resources\PageResource::class,
        ],
    ],
    'styles' => ['default', 'horizontal', 'vertical', 'dropdown'],
],
```

**Migration Notes:**
- Successfully migrated from 15web/filament-tree + kalnoy/nestedset to solution-forest/filament-tree
- Removed complex nested set structure (_lft, _rgt) in favor of simple parent_id/order
- Enhanced with resource-based translations and icons
- Backward compatibility not maintained (no existing projects using menus)

## Development Guidelines

### Use Existing Filament Components
**IMPORTANT**: Always use existing Filament blade components and patterns instead of implementing custom HTML/CSS solutions.

- Use `<x-filament::button>` instead of custom button HTML
- Use `<x-filament::badge>` instead of custom badge styling  
- Use `<x-filament::icon>` for consistent icon rendering
- Use Filament's built-in form components and actions
- Use Filament's existing patterns for modals, notifications, and UI elements
- Leverage Filament's color system, sizing, and theming capabilities

This ensures:
- Consistent styling across the application
- Automatic theme support (light/dark mode)
- Built-in accessibility features
- Future compatibility with Filament updates
- Reduced maintenance overhead

## Common Development Patterns

When extending this package:
1. Models extend the flexible content blocks contracts and traits
2. Resources follow Filament patterns with translatable support
3. Use the facade `FilamentFlexibleContentBlockPages` to access configuration
4. SEO handling follows a fallback pattern: page SEO → hero image → default settings
5. Route generation uses the configured route helper class
6. Menu items follow the linkable pattern for polymorphic relationships
7. **Always prefer existing Filament components over custom implementations**