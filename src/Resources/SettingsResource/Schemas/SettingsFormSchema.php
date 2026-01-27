<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\SettingsResource\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Settings;

class SettingsFormSchema
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Settings')->columnSpan(2)->tabs([
                    Tab::make(flexiblePagesTrans('settings.settings_tab_site_general'))
                        ->schema(static::getGeneralTabFormSchema()),
                    Tab::make(flexiblePagesTrans('settings.settings_tab_seo'))
                        ->schema(static::getSeoTabFormSchema()),
                    ...static::getExtraFormTabs(),
                ])
                    ->persistTabInQueryString(),
            ]);
    }

    public static function getGeneralTabFormSchema(): array
    {
        return [
            TextInput::make(Settings::SETTING_SITE_TITLE)
                ->label(flexiblePagesTrans('settings.settings_site_title'))
                ->required(),
            TextInput::make(Settings::SETTING_FOOTER_COPYRIGHT)
                ->label(flexiblePagesTrans('settings.settings_footer_copyright'))
                ->hint(flexiblePagesTrans('settings.translatable_field_hint'))
                ->hintIcon('heroicon-m-language')
                ->required(),
            RichEditor::make(Settings::SETTING_CONTACT_INFO)
                ->label(flexiblePagesTrans('settings.settings_contact_info'))
                ->disableToolbarButtons([
                    'attachFiles',
                ]),
        ];
    }

    public static function getSeoTabFormSchema(): array
    {
        return [
            SpatieMediaLibraryFileUpload::make(Settings::COLLECTION_DEFAULT_SEO)
                ->label(flexiblePagesTrans('settings.settings_default_seo_image'))
                ->hint(flexiblePagesTrans('settings.settings_default_seo_image_hint'))
                ->collection(Settings::COLLECTION_DEFAULT_SEO)
                ->conversion(Settings::CONVERSION_THUMB)
                ->minFiles(1)
                ->maxFiles(1),
        ];
    }

    /**
     * One can define here extra tabs to append to the form. The tabs will appear after the default tabs.
     *
     * @return array<Tab>
     */
    public static function getExtraFormTabs(): array
    {
        return [];
    }
}
