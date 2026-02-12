<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Menu;

class MenuFactory extends Factory
{
    protected $model = Menu::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->words(2, true),
            'code' => $this->faker->unique()->slug(2),
            'description' => $this->faker->sentence(),
            'title' => ['en' => $this->faker->words(2, true), 'es' => $this->faker->words(2, true)],
            'style' => 'default',
            'max_depth' => null,
        ];
    }

    public function withCode(string $code): static
    {
        return $this->state(fn (array $attributes) => [
            'code' => $code,
        ]);
    }

    public function withStyle(string $style): static
    {
        return $this->state(fn (array $attributes) => [
            'style' => $style,
        ]);
    }

    public function withMaxDepth(int $maxDepth): static
    {
        return $this->state(fn (array $attributes) => [
            'max_depth' => $maxDepth,
        ]);
    }

    public function main(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Main Menu',
            'code' => 'main',
        ]);
    }

    public function footer(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Footer Menu',
            'code' => 'footer',
        ]);
    }
}
