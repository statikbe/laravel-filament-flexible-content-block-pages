<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Tag;
use Statikbe\FilamentFlexibleContentBlockPages\Models\TagType;

class TagFactory extends Factory
{
    protected $model = Tag::class;

    public function definition(): array
    {
        $name = $this->faker->words(2, true);

        return [
            'name' => ['en' => $name, 'es' => $this->faker->words(2, true)],
            'slug' => ['en' => \Illuminate\Support\Str::slug($name), 'es' => \Illuminate\Support\Str::slug($this->faker->words(2, true))],
            'seo_description' => ['en' => $this->faker->sentence(10), 'es' => $this->faker->sentence(10)],
            'type' => null,
            'order_column' => 0,
        ];
    }

    public function forTagType(TagType $tagType): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => $tagType->code,
        ]);
    }

    public function withName(string $name, ?string $locale = 'en'): static
    {
        return $this->state(function (array $attributes) use ($name, $locale) {
            $names = $attributes['name'];
            $names[$locale] = $name;

            return [
                'name' => $names,
                'slug' => array_map(fn ($n) => \Illuminate\Support\Str::slug($n), $names),
            ];
        });
    }

    public function withSlug(string $slug, ?string $locale = 'en'): static
    {
        return $this->state(function (array $attributes) use ($slug, $locale) {
            $slugs = $attributes['slug'];
            $slugs[$locale] = $slug;

            return ['slug' => $slugs];
        });
    }

    public function withSeoDescription(string $description, ?string $locale = 'en'): static
    {
        return $this->state(function (array $attributes) use ($description, $locale) {
            $descriptions = $attributes['seo_description'] ?? [];
            $descriptions[$locale] = $description;

            return ['seo_description' => $descriptions];
        });
    }
}
