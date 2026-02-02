<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Menu;
use Statikbe\FilamentFlexibleContentBlockPages\Models\MenuItem;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;

class MenuItemFactory extends Factory
{
    protected $model = MenuItem::class;

    public function definition(): array
    {
        return [
            'menu_id' => Menu::factory(),
            'label' => ['en' => $this->faker->words(2, true), 'es' => $this->faker->words(2, true)],
            'link_type' => MenuItem::LINK_TYPE_URL,
            'url' => ['en' => $this->faker->url(), 'es' => $this->faker->url()],
            'route' => null,
            'linkable_type' => null,
            'linkable_id' => null,
            'target' => '_self',
            'icon' => null,
            'is_visible' => true,
            'use_model_title' => false,
            'parent_id' => config('filament-tree.default_parent_id', -1),
            'order' => 0,
        ];
    }

    public function forMenu(Menu $menu): static
    {
        return $this->state(fn (array $attributes) => [
            'menu_id' => $menu->id,
        ]);
    }

    public function url(string $url): static
    {
        return $this->state(fn (array $attributes) => [
            'link_type' => MenuItem::LINK_TYPE_URL,
            'url' => ['en' => $url, 'es' => $url],
            'route' => null,
            'linkable_type' => null,
            'linkable_id' => null,
        ]);
    }

    public function route(string $routeName): static
    {
        return $this->state(fn (array $attributes) => [
            'link_type' => MenuItem::LINK_TYPE_ROUTE,
            'url' => null,
            'route' => $routeName,
            'linkable_type' => null,
            'linkable_id' => null,
        ]);
    }

    public function linkedTo(Page $page): static
    {
        return $this->state(fn (array $attributes) => [
            'link_type' => $page->getMorphClass(),
            'url' => null,
            'route' => null,
            'linkable_type' => $page->getMorphClass(),
            'linkable_id' => $page->id,
        ]);
    }

    public function hidden(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_visible' => false,
        ]);
    }

    public function useModelTitle(): static
    {
        return $this->state(fn (array $attributes) => [
            'use_model_title' => true,
        ]);
    }

    public function childOf(MenuItem $parent): static
    {
        return $this->state(fn (array $attributes) => [
            'menu_id' => $parent->menu_id,
            'parent_id' => $parent->id,
        ]);
    }

    public function withTarget(string $target): static
    {
        return $this->state(fn (array $attributes) => [
            'target' => $target,
        ]);
    }
}
