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
- **Menu/MenuItem** (`src/Models/Menu.php`, `src/Models/MenuItem.php`): Hierarchical menu system with nested structure using kalnoy/nestedset

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
- kalnoy/nestedset for hierarchical menu structure

## Menu Builder System (In Development)

### Current Status
The menu builder is partially implemented with the following components:

**✅ Completed:**
- Menu and MenuItem models with kalnoy/nestedset integration
- Database migrations for menus and menu_items tables
- MenuResource with basic CRUD operations
- ManageMenuItems page with drag-and-drop tree interface
- Alpine.js + SortableJS powered tree builder component
- Translation support (EN/NL)
- Configuration system with max depth settings

**🚧 Remaining Tasks:**
- MenuItem form/modal for adding and editing menu items
- Linkable model integration (following call-to-action patterns)
- Frontend helper methods and blade components for menu rendering
- Validation and error handling
- Proper nested set operations for reordering

### Architecture
- **Menu Model**: Simple container with name, code, and description
- **MenuItem Model**: Nested set structure with linkable polymorphic relationships
- **ManageMenuItems Page**: Dedicated interface for menu structure management
- **Tree Builder Component**: Custom Filament field with Alpine.js interactions

### Configuration
```php
'menu' => [
    'max_depth' => 2,  // Configurable nesting level
],
```

## Common Development Patterns

When extending this package:
1. Models extend the flexible content blocks contracts and traits
2. Resources follow Filament patterns with translatable support
3. Use the facade `FilamentFlexibleContentBlockPages` to access configuration
4. SEO handling follows a fallback pattern: page SEO → hero image → default settings
5. Route generation uses the configured route helper class
6. Menu items follow the linkable pattern for polymorphic relationships