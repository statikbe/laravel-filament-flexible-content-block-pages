<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;

class SeedDefaultsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'flexible-content-block-pages:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed default home page and settings';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('Seeding default home page and settings...');

        $this->seedHomePage();
        $this->seedSettings();

        $this->info('Default home page and settings seeded successfully!');
    }

    public function seedHomePage(): void
    {
        $pageModel = FilamentFlexibleContentBlockPages::config()->getPageModel();
        if(!$pageModel::code(Page::HOME_PAGE)->exists()) {
            $locales = FilamentFlexibleContentBlockPages::config()->getSupportedLocales();

            $homePage = new $pageModel;
            $homePage->code = Page::HOME_PAGE;
            $this->setTranslatedField($homePage, 'title', 'Home', $locales);
            $homePage->save();
        }
    }

    public function seedSettings(): void
    {
        $settingsModel = FilamentFlexibleContentBlockPages::config()->getSettingsModel();
        if($settingsModel::query()->count() === 0) {
            $locales = FilamentFlexibleContentBlockPages::config()->getSupportedLocales();
            $settings = new $settingsModel;

            $settings->site_title = config('app.name');
            $this->setTranslatedField($settings, 'footer_copyright', 'Made with love by Statik', $locales);

            $settings->save();
        }
    }

    private function setTranslatedField(Model $model, string $field, string $value, array $locales)
    {
        foreach ($locales as $locale) {
            $model->setTranslation($field, $locale, $value);
        }
    }
}
