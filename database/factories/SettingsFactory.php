<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Settings;

class SettingsFactory extends Factory
{
    protected $model = Settings::class;

    public function definition(): array
    {
        return [
            'site_title' => $this->faker->company(),
            'contact_info' => [
                'en' => '<p>'.$this->faker->address().'</p><p>'.$this->faker->phoneNumber().'</p>',
                'es' => '<p>'.$this->faker->address().'</p><p>'.$this->faker->phoneNumber().'</p>',
            ],
            'footer_copyright' => [
                'en' => '© '.date('Y').' '.$this->faker->company().'. All rights reserved.',
                'es' => '© '.date('Y').' '.$this->faker->company().'. Todos los derechos reservados.',
            ],
        ];
    }

    public function withSiteTitle(string $title): static
    {
        return $this->state(fn (array $attributes) => [
            'site_title' => $title,
        ]);
    }

    public function withContactInfo(string $contactInfo, ?string $locale = 'en'): static
    {
        return $this->state(function (array $attributes) use ($contactInfo, $locale) {
            $info = $attributes['contact_info'];
            $info[$locale] = $contactInfo;

            return ['contact_info' => $info];
        });
    }

    public function withFooterCopyright(string $copyright, ?string $locale = 'en'): static
    {
        return $this->state(function (array $attributes) use ($copyright, $locale) {
            $copyrights = $attributes['footer_copyright'];
            $copyrights[$locale] = $copyright;

            return ['footer_copyright' => $copyrights];
        });
    }
}
