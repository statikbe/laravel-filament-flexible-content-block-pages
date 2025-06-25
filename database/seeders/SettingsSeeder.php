<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Settings;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $locales = FilamentFlexibleContentBlockPages::config()->getSupportedLocales();
        $settingsModel = FilamentFlexibleContentBlockPages::config()->getSettingsModel();
        $settings = new $settingsModel;

        $settings->site_title = config('app.name');
        $this->setTranslatedField($settings, 'footer_copyright', 'Made with love by Statik', $locales);

        $settings->save();
    }

    private function setTranslatedField(Settings $settings, string $field, string $value, array $locales)
    {
        foreach ($locales as $locale) {
            $settings->setTranslation($field, $locale, $value);
        }
    }
}
