<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\PageResource\Schemas;

use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Form\Components\UndeletableToggle;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\PageResource;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Form\Actions\CopyContentBlocksToLocalesAction;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Form\Fields\AuthorField;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Form\Fields\CodeField;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Form\Fields\ContentBlocksField;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Form\Fields\Groups\HeroCallToActionSection;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Form\Fields\Groups\HeroImageSection;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Form\Fields\Groups\OverviewFields;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Form\Fields\Groups\PublicationSection;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Form\Fields\Groups\SEOFields;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Form\Fields\IntroField;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Form\Fields\SlugField;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Form\Fields\TitleField;

class PageFormSchema
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make(flexiblePagesTrans('pages.tabs.lbl'))
                    ->columnSpan(2)
                    ->tabs([
                        Tab::make(flexiblePagesTrans('pages.tabs.general'))
                            ->icon('heroicon-m-globe-alt')
                            ->schema(static::getGeneralTabFields()),
                        Tab::make(flexiblePagesTrans('pages.tabs.content'))
                            ->icon('heroicon-o-rectangle-group')
                            ->schema(static::getContentTabFields()),
                        Tab::make(flexiblePagesTrans('pages.tabs.overview'))
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema(static::getOverviewTabFields()),
                        Tab::make(flexiblePagesTrans('pages.tabs.seo'))
                            ->icon('heroicon-o-globe-alt')
                            ->schema(static::getSEOTabFields()),
                        Tab::make(flexiblePagesTrans('pages.tabs.advanced'))
                            ->icon('heroicon-o-wrench-screwdriver')
                            ->schema(static::getAdvancedTabFields()),
                    ])
                    ->persistTabInQueryString(),
            ]);
    }

    public static function getGeneralTabFields(): array
    {
        $modelClass = PageResource::getModel();

        $fields = [
            TitleField::create(true),
            IntroField::create(),
            HeroImageSection::create(true, FilamentFlexibleContentBlockPages::config()->isHeroVideoUrlEnabled($modelClass)),
        ];

        if (FilamentFlexibleContentBlockPages::config()->isHeroCallToActionsEnabled($modelClass)) {
            $fields[] = HeroCallToActionSection::create();
        }

        return $fields;
    }

    public static function getContentTabFields(): array
    {
        return [
            CopyContentBlocksToLocalesAction::create(),
            ContentBlocksField::create(),
        ];
    }

    public static function getSEOTabFields(): array
    {
        return [
            SEOFields::create(1, true),
        ];
    }

    public static function getOverviewTabFields(): array
    {
        return [
            OverviewFields::create(1, true),
        ];
    }

    public static function getAdvancedTabFields(): array
    {
        $config = FilamentFlexibleContentBlockPages::config();
        $modelClass = PageResource::getModel();

        $fields = [
            PublicationSection::create(),
            CodeField::create(),
            SlugField::create(false),
        ];

        $gridFields = [];

        if ($config->isAuthorEnabled($modelClass)) {
            $gridFields[] = AuthorField::create();
        }

        if ($config->isUndeletableEnabled($modelClass)) {
            $gridFields[] = UndeletableToggle::create();
        }

        if (! empty($gridFields)) {
            $fields[] = Grid::make()->schema($gridFields);
        }

        return $fields;
    }
}
