<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;

class PageFactory extends Factory
{
    protected $model = Page::class;

    public function definition(): array
    {
        return [
            'title' => ['en' => $this->faker->sentence(3), 'es' => $this->faker->sentence(3)],
            'slug' => ['en' => $this->faker->slug(3), 'es' => $this->faker->slug(3)],
            'intro' => ['en' => $this->faker->paragraph(), 'es' => $this->faker->paragraph()],
            'code' => null,
            'publishing_begins_at' => now(),
            'publishing_ends_at' => null,
            'seo_title' => ['en' => $this->faker->sentence(4), 'es' => $this->faker->sentence(4)],
            'seo_description' => ['en' => $this->faker->sentence(10), 'es' => $this->faker->sentence(10)],
            'overview_title' => ['en' => $this->faker->sentence(3), 'es' => $this->faker->sentence(3)],
            'overview_description' => ['en' => $this->faker->paragraph(), 'es' => $this->faker->paragraph()],
            'content_blocks' => [],
            'parent_id' => \SolutionForest\FilamentTree\Support\Utils::defaultParentId(),
            'order' => 0,
            'is_undeletable' => false,
        ];
    }

    public function homePage(): static
    {
        return $this->state(fn (array $attributes) => [
            'code' => Page::HOME_PAGE,
            'title' => ['en' => 'Home', 'es' => 'Inicio'],
            'slug' => ['en' => 'home', 'es' => 'inicio'],
        ]);
    }

    public function undeletable(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_undeletable' => true,
        ]);
    }

    public function unpublished(): static
    {
        return $this->state(fn (array $attributes) => [
            'publishing_begins_at' => null,
            'publishing_ends_at' => now()->subDay(),
        ]);
    }

    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'publishing_begins_at' => now()->addDays(7),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'publishing_begins_at' => now()->subDays(14),
            'publishing_ends_at' => now()->subDays(7),
        ]);
    }

    public function withCode(string $code): static
    {
        return $this->state(fn (array $attributes) => [
            'code' => $code,
        ]);
    }

    public function childOf(Page $parent): static
    {
        return $this->state(fn (array $attributes) => [
            'parent_id' => $parent->id,
        ]);
    }
}
