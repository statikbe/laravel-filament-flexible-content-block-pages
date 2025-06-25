<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;

class HomePageSeeder extends Seeder
{
    public function run(): void
    {
        $locales = FilamentFlexibleContentBlockPages::config()->getSupportedLocales();
        $pageModel = FilamentFlexibleContentBlockPages::config()->getPageModel();

        $homePage = new $pageModel;
        $homePage->code = Page::HOME_PAGE;
        $this->setTranslatedField($homePage, 'title', 'Home', $locales);
        $homePage->save();
    }

    private function setTranslatedField(Page $homePage, string $field, string $value, array $locales)
    {
        foreach ($locales as $locale) {
            $homePage->setTranslation($field, $locale, $value);
        }
    }
}
