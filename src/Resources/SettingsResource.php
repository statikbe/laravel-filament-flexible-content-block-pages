<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources;

use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Settings;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\SettingsResource\Pages\CreateSettings;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\SettingsResource\Pages\EditSettings;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\SettingsResource\Pages\ListSettings;

class SettingsResource extends Resource
{
    use Translatable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-adjustments-vertical';

    /**
     * @return class-string
     */
    public static function getModel(): string
    {
        return FilamentFlexibleContentBlockPages::config()->getSettingsModel()::class;
    }

    public static function getLabel(): ?string
    {
        return flexiblePagesTrans('settings.settings_lbl');
    }

    public static function getPluralLabel(): ?string
    {
        return flexiblePagesTrans('settings.settings_plural_lbl');
    }

    public static function getNavigationGroup(): ?string
    {
        return flexiblePagesTrans('settings.navigation_group');
    }

    public static function getNavigationSort(): ?int
    {
        return FilamentFlexibleContentBlockPages::config()->getSettingsNavigationSort();
    }

    public static function form(Schema $schema): Schema
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make(Settings::SETTING_SITE_TITLE),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSettings::route('/'),
            'create' => CreateSettings::route('/create'),
            'edit' => EditSettings::route('/{record}/edit'),
        ];
    }

    public static function getNavigationUrl(): string
    {
        // do not show the table, since we will only have one record:
        /** @var Settings|null $settings */
        $settings = static::getModel()::first();
        if ($settings) {
            return static::getUrl('edit', ['record' => $settings]);
        } else {
            return static::getUrl('create');
        }
    }

    protected static function getGeneralTabFormSchema(): array
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

    protected static function getSeoTabFormSchema(): array
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
    protected static function getExtraFormTabs(): array
    {
        return [];
    }
}
