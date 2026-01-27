<?php

namespace Statikbe\FilamentFlexibleContentBlockPages;

use Exception;
use Filament\Contracts\Plugin;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Resources\Resource;
use Filament\Widgets\Widget;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;

final class FlexibleContentBlockPagesPlugin implements Plugin
{
    protected static self $instance;

    protected static array $resources;

    protected static array $pages = [];

    protected static array $widgets = [];

    public function getId(): string
    {
        return FilamentFlexibleContentBlockPagesServiceProvider::PACKAGE_PREFIX;
    }

    public function register(Panel $panel): void
    {
        $panel
            ->resources(self::getResources())
            ->pages(self::getPages())
            ->widgets(self::getWidgets());
    }

    public function boot(Panel $panel): void
    {
        //
    }

    public static function make(): static
    {
        return app(self::class);
    }

    public static function get(): self
    {
        if (isset(self::$instance)) {
            return self::$instance;
        }

        $instance = filament(app(static::class)->getId());

        // Check if the instance is of the expected type for phpstan:
        if (! $instance instanceof FlexibleContentBlockPagesPlugin) {
            throw new Exception('Expected a FlexibleContentBlocksPagesPlugin instance');
        }

        self::$instance = $instance;

        return $instance;
    }

    /**
     * @return array<class-string<resource>>
     */
    public static function getResources(): array
    {
        static::$resources ??= FilamentFlexibleContentBlockPages::config()->getResources();

        return static::$resources;
    }

    /**
     * @return array<class-string<Page>>
     */
    public static function getPages(): array
    {
        return static::$pages;
    }

    /**
     * @return array<class-string<Widget>>
     */
    public static function getWidgets(): array
    {
        return static::$widgets;
    }

    /**
     * @param  array<class-string<Page>>  $pages
     */
    public static function pages(array $pages): static
    {
        static::$pages = $pages;

        return new static;
    }

    /**
     * @param  array<class-string<Widget>>  $widgets
     */
    public static function widgets(array $widgets): static
    {
        static::$widgets = $widgets;

        return new static;
    }

    /**
     * @param  array<class-string<resource>>  $resources
     */
    public static function resources(array $resources): static
    {
        static::$resources = $resources;

        return new static;
    }
}
