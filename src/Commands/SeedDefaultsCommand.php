<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;
use Statikbe\FilamentFlexibleContentBlockPages\Models\TagType;

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
        $this->seedTagTypes();

        $this->info('Default home page and settings seeded successfully!');
    }

    public function seedHomePage(): void
    {
        $pageModel = FilamentFlexibleContentBlockPages::config()->getPageModel();
        if (! $pageModel::code(Page::HOME_PAGE)->exists()) {
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
        if ($settingsModel::query()->count() === 0) {
            $locales = FilamentFlexibleContentBlockPages::config()->getSupportedLocales();
            $settings = new $settingsModel;

            $settings->site_title = config('app.name');
            $this->setTranslatedField($settings, 'footer_copyright', 'Made with love by Statik', $locales);

            $settings->save();
        }
    }

    /**
     * Set a translated field value for all given locales.
     * 
     * @param Model $model Model that uses HasTranslations trait
     * @param string $field
     * @param string $value
     * @param array<string> $locales
     */
    private function setTranslatedField(Model $model, string $field, string $value, array $locales)
    {
        foreach ($locales as $locale) {
            /** @phpstan-ignore-next-line */
            $model->setTranslation($field, $locale, $value);
        }
    }

    private function seedTagTypes()
    {
        $locales = FilamentFlexibleContentBlockPages::config()->getSupportedLocales();
        $tagTypeModel = FilamentFlexibleContentBlockPages::config()->getTagTypeModel();

        if (! $tagTypeModel::code(TagType::TYPE_DEFAULT)->exists()) {
            $seoType = new $tagTypeModel;
            $seoType->code = TagType::TYPE_DEFAULT;
            $this->setTranslatedField($seoType, 'name', 'Standaard', $locales);
            $seoType->colour = '#4FC3F7';
            $seoType->icon = 'heroicon-s-tag';
            $seoType->is_default_type = true;
            $seoType->has_seo_pages = false;
            $seoType->save();
        }

        if (! $tagTypeModel::code(TagType::TYPE_SEO)->exists()) {
            $seoType = new $tagTypeModel;
            $seoType->code = TagType::TYPE_SEO;
            $this->setTranslatedField($seoType, 'name', "Alleen voor SEO-pagina's", $locales);
            $seoType->colour = '#FFC107';
            $seoType->icon = 'heroicon-o-globe-alt';
            $seoType->is_default_type = false;
            $seoType->has_seo_pages = true;
            $seoType->save();
        }
    }
}
