<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Settings;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\SettingsResource\Pages;

class SettingsResource extends Resource
{
    use Translatable;

    public static function getModel(): string
    {
        return FilamentFlexibleContentBlockPages::config()->getSettingsModel()::class;
    }

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-vertical';

    protected static ?int $navigationSort = 1;

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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                Tables\Columns\TextColumn::make(Settings::SETTING_SITE_TITLE)
                    ->label(trans('filament.settings_lbl')),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListSettings::route('/'),
            'create' => Pages\CreateSettings::route('/create'),
            'edit' => Pages\EditSettings::route('/{record}/edit'),
        ];
    }

    public static function getNavigationUrl(): string
    {
        // do not show the table, since we will only have one record:
        if ($settings = static::getModel()::first()) {
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
