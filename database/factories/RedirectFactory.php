<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Redirect;

class RedirectFactory extends Factory
{
    protected $model = Redirect::class;

    public function definition(): array
    {
        return [
            'old_url' => '/'.$this->faker->slug(3),
            'new_url' => '/'.$this->faker->slug(3),
            'status_code' => 301,
        ];
    }

    public function permanent(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_code' => 301,
        ]);
    }

    public function temporary(): static
    {
        return $this->state(fn (array $attributes) => [
            'status_code' => 302,
        ]);
    }

    public function withStatusCode(int $statusCode): static
    {
        return $this->state(fn (array $attributes) => [
            'status_code' => $statusCode,
        ]);
    }

    public function from(string $oldUrl): static
    {
        return $this->state(fn (array $attributes) => [
            'old_url' => $oldUrl,
        ]);
    }

    public function to(string $newUrl): static
    {
        return $this->state(fn (array $attributes) => [
            'new_url' => $newUrl,
        ]);
    }
}
