<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Statikbe\FilamentFlexibleContentBlockPages\Models\TagType;

class TagTypeFactory extends Factory
{
    protected $model = TagType::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->slug(2),
            'name' => ['en' => $this->faker->words(2, true), 'es' => $this->faker->words(2, true)],
            'colour' => $this->faker->hexColor(),
            'icon' => null,
            'is_default_type' => false,
            'has_seo_pages' => false,
        ];
    }

    public function default(): static
    {
        return $this->state(fn (array $attributes) => [
            'code' => TagType::TYPE_DEFAULT,
            'name' => ['en' => 'Default', 'es' => 'Predeterminado'],
            'is_default_type' => true,
        ]);
    }

    public function seo(): static
    {
        return $this->state(fn (array $attributes) => [
            'code' => TagType::TYPE_SEO,
            'name' => ['en' => 'SEO', 'es' => 'SEO'],
            'has_seo_pages' => true,
        ]);
    }

    public function withSeoPages(): static
    {
        return $this->state(fn (array $attributes) => [
            'has_seo_pages' => true,
        ]);
    }

    public function withCode(string $code): static
    {
        return $this->state(fn (array $attributes) => [
            'code' => $code,
        ]);
    }

    public function withColour(string $colour): static
    {
        return $this->state(fn (array $attributes) => [
            'colour' => $colour,
        ]);
    }

    public function withIcon(string $icon): static
    {
        return $this->state(fn (array $attributes) => [
            'icon' => $icon,
        ]);
    }
}
