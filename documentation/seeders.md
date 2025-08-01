# Menu Seeders

This package provides a powerful and developer-friendly way to seed menus and menu items through the `MenuSeeder` abstract class. This base class offers typed methods with sensible defaults, making it easy to create complex menu structures programmatically.

## Getting Started

### Creating a Menu Seeder

Extend the `MenuSeeder` abstract class to create your own menu seeder:

```php
<?php

namespace Database\Seeders;

use Statikbe\FilamentFlexibleContentBlockPages\Database\Seeders\MenuSeeder;
use App\Models\Page;

class HeaderMenuSeeder extends MenuSeeder
{
    public function run()
    {
        // Create your menu structure here
        $menu = $this->createMenu('Header Navigation', 'header');
        
        // Add menu items...
    }
}
```

## Core Methods

### Creating Menus

#### `createMenu()`

Creates a new menu with typed arguments and sensible defaults:

```php
protected function createMenu(
    string $name,
    string $code,
    ?string $description = null,
    ?string $style = null,
    ?int $maxDepth = null
): Menu
```

**Parameters:**
- `$name` - Display name for content managers (not shown to visitors)
- `$code` - Unique identifier used in templates (e.g., 'header', 'footer')  
- `$description` - Optional description for content managers
- `$style` - Menu style (defaults to config default if not provided)
- `$maxDepth` - Maximum nesting levels (defaults to config default if not provided)

**Example:**
```php
$headerMenu = $this->createMenu(
    name: 'Header Navigation',
    code: 'header',
    description: 'Main navigation menu displayed in the header',
    style: 'horizontal',
    maxDepth: 3
);
```

### Creating Menu Items

### Route-Based Items

#### `createMenuItemForRoute()`

Creates menu items that link to Laravel routes:

```php
protected function createMenuItemForRoute(
    Menu|int $menu,
    string $route,
    string|array $label,
    bool $isVisible = true,
    string $target = '_self',
    ?string $icon = null,
    MenuItem|int $parent = -1
): MenuItem
```

**Features:**
- Automatically validates that the route exists

**Example:**
```php
$home = $this->createMenuItemForRoute(
    menu: $menu,
    route: 'home',
    label: 'Home',
    icon: 'heroicon-o-home'
);

$about = $this->createMenuItemForRoute(
    menu: $menu,
    route: 'about',
    label: ['en' => 'About', 'nl' => 'Over ons']
);
```

### URL-Based Items

#### `createMenuItemForUrl()`

Creates menu items that link to external URLs:

```php
protected function createMenuItemForUrl(
    Menu|int $menu,
    string $url,
    string|array $label,
    bool $isVisible = true,
    string $target = '_blank', // Note: defaults to _blank for external links
    ?string $icon = null,
    MenuItem|int $parent = -1
): MenuItem
```

**Features:**
- Defaults to `target='_blank'` for external links

**Example:**
```php
$github = $this->createMenuItemForUrl(
    menu: $menu,
    url: 'https://github.com/statikbe/laravel-filament-flexible-content-block-pages',
    label: 'GitHub',
    icon: 'heroicon-o-link'
);

// Internal URL with custom target
$admin = $this->createMenuItemForUrl(
    menu: $menu,
    url: '/admin',
    label: 'Admin Panel',
    target: '_self'
);
```

### Model-Based Items

#### `createMenuItemForModel()`

Creates menu items that link to model instances (Pages, Posts, etc.):

```php
protected function createMenuItemForModel(
    Menu|int $menu,
    Model $model,
    string|array|null $label = null,
    bool $useModelTitle = true,
    bool $isVisible = true,
    string $target = '_self',
    ?string $icon = null,
    MenuItem|int $parent = -1
): MenuItem
```

**Features:**
- Automatically uses `getMenuLabel()` from models implementing `HasMenuLabel`
- Falls back to class name + ID if no label provided
- Sets up polymorphic relationship correctly
- Supports `use_model_title` for dynamic labels

**Example:**
```php
$page = Page::where('slug', 'about')->first();

// Auto-use model's menu label
$aboutItem = $this->createMenuItemForModel(
    menu: $menu,
    model: $page,
    useModelTitle: true
);

// Override with custom label
$contactItem = $this->createMenuItemForModel(
    menu: $menu,
    model: $contactPage,
    label: 'Get in Touch',
    useModelTitle: false
);
```

## Building Hierarchical Menus

### Parent Assignment

You can create nested menu structures by specifying the parent when creating child items:

```php
// Create parent item
$about = $this->createMenuItemForRoute($menu, 'about', 'About');

// Create child items with parent assignment
$team = $this->createMenuItemForRoute(
    menu: $menu,
    route: 'about.team',
    label: 'Our Team',
    parent: $about
);

$history = $this->createMenuItemForRoute(
    menu: $menu,
    route: 'about.history',
    label: 'History',
    parent: $about
);

// Multi-level nesting
$services = $this->createMenuItemForRoute($menu, 'services', 'Services');

$webDev = $this->createMenuItemForRoute(
    menu: $menu,
    route: 'services.web-development',
    label: 'Web Development',
    parent: $services
);

$webDesign = $this->createMenuItemForRoute(
    menu: $menu,
    route: 'services.web-development.design',
    label: 'Web Design',
    parent: $webDev // Nested under web development
);
```

## Complete Example

Here's a comprehensive example showing all features:

```php
<?php

namespace Database\Seeders;

use Statikbe\FilamentFlexibleContentBlockPages\Database\Seeders\MenuSeeder;
use App\Models\Page;
use App\Models\Service;

class MainMenuSeeder extends MenuSeeder
{
    public function run()
    {
        // Create the main menu
        $menu = $this->createMenu(
            name: 'Main Navigation',
            code: 'main',
            description: 'Primary website navigation',
            style: 'horizontal',
            maxDepth: 3
        );

        // Home page
        $home = $this->createMenuItemForRoute(
            menu: $menu,
            route: 'home',
            label: ['en' => 'Home', 'nl' => 'Start'],
            icon: 'heroicon-o-home'
        );

        // About section with children
        $about = $this->createMenuItemForRoute(
            menu: $menu,
            route: 'about',
            label: ['en' => 'About', 'nl' => 'Over ons']
        );

        $this->createMenuItemForRoute(
            menu: $menu,
            route: 'about.team',
            label: ['en' => 'Our Team', 'nl' => 'Ons Team'],
            parent: $about
        );

        $this->createMenuItemForRoute(
            menu: $menu,
            route: 'about.history',
            label: ['en' => 'History', 'nl' => 'Geschiedenis'],
            parent: $about
        );

        // Services from models
        $services = $this->createMenuItemForRoute(
            menu: $menu,
            route: 'services',
            label: ['en' => 'Services', 'nl' => 'Diensten']
        );

        // Add service pages dynamically
        Service::published()->each(function ($service) use ($menu, $services) {
            $this->createMenuItemForModel(
                menu: $menu,
                model: $service,
                useModelTitle: true,
                parent: $services
            );
        });

        // External links
        $this->createMenuItemForUrl(
            menu: $menu,
            url: 'https://github.com/our-company',
            label: 'GitHub',
            icon: 'heroicon-o-link'
        );

        // Contact page from model
        $contactPage = Page::where('slug', 'contact')->first();
        if ($contactPage) {
            $this->createMenuItemForModel(
                menu: $menu,
                model: $contactPage,
                label: ['en' => 'Contact', 'nl' => 'Contact']
            );
        }
    }
}
```

## Advanced Features

### Automatic Order Management

Menu items are automatically ordered within their parent scope. The seeder calculates the next available order value to ensure proper sequence.

### Route Validation

Route-based menu items automatically validate that the specified route exists, throwing an `InvalidArgumentException` if not found.

### Label Normalization

String labels are automatically converted to translatable arrays using the current application locale.

### Model Integration

Models implementing the `HasMenuLabel` contract will have their `getMenuLabel()` method called automatically when `useModelTitle` is true.

## Running Seeders

Add your menu seeders to `DatabaseSeeder.php`:

```php
public function run()
{
    $this->call([
        HeaderMenuSeeder::class,
        FooterMenuSeeder::class,
        // ... other seeders
    ]);
}
```

Then run: `php artisan db:seed` or `php artisan db:seed --class=HeaderMenuSeeder`
