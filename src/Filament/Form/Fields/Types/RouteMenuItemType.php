<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Filament\Form\Fields\Types;

use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Route;

class RouteMenuItemType extends AbstractMenuItemType
{
    const TYPE_ROUTE = 'route';

    public function __construct()
    {
        $this->icon = 'heroicon-o-map';
    }

    public static function make(?string $model = null): static
    {
        return new static;
    }

    public function getAlias(): string
    {
        return self::TYPE_ROUTE;
    }

    public function getRouteField(): Select
    {
        return Select::make('route')
            ->label(flexiblePagesTrans('menu_items.form.route_lbl'))
            ->options($this->getRouteOptions())
            ->searchable()
            ->required()
            ->helperText(flexiblePagesTrans('menu_items.form.route_help'));
    }

    public function getRouteOptions(): array
    {
        $routes = [];
        $routeCollection = Route::getRoutes();

        foreach ($routeCollection as $route) {
            $name = $route->getName();
            if ($name && $this->isAllowedRoute($name)) {
                $routes[$name] = $name;
            }
        }

        ksort($routes);

        return $routes;
    }

    protected function isAllowedRoute(string $routeName): bool
    {
        $config = config('filament-flexible-content-blocks.link_routes', [
            'allowed' => ['*'],
            'denied' => ['debugbar*', 'telescope*', '_ignition*', 'filament*'],
        ]);

        $allowed = $config['allowed'] ?? ['*'];
        $denied = $config['denied'] ?? [];

        // Check if route is denied
        foreach ($denied as $pattern) {
            if (fnmatch($pattern, $routeName)) {
                return false;
            }
        }

        // Check if route is allowed
        foreach ($allowed as $pattern) {
            if (fnmatch($pattern, $routeName)) {
                return true;
            }
        }

        return false;
    }

    public function isRouteType(): bool
    {
        return true;
    }
}
