<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources;

use App\Models\Page;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Form;
use Filament\Resources\Components\Tab;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\PageResource\Pages\CreatePage;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\PageResource\Pages\EditPage;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\PageResource\Pages\ListPages;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Form\Actions\CopyContentBlocksToLocalesAction;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Form\Fields\AuthorField;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Form\Fields\CodeField;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Form\Fields\ContentBlocksField;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Form\Fields\Groups\HeroImageSection;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Form\Fields\Groups\OverviewFields;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Form\Fields\Groups\PublicationSection;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Form\Fields\Groups\SEOFields;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Form\Fields\IntroField;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Form\Fields\ParentField;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Form\Fields\SlugField;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Form\Fields\TitleField;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Table\Actions\PublishAction;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Table\Actions\ViewAction;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Table\Columns\PublishedColumn;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Table\Columns\TitleColumn;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Table\Filters\PublishedFilter;
use Statikbe\FilamentFlexibleContentBlocks\FilamentFlexibleBlocksConfig;

class PageResource extends Resource
{
    use Translatable;

    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $recordRouteKeyName = 'id';

    public static function getLabel(): ?string
    {
        return flexiblePagesTrans('pages.lbl');
    }

    public static function getPluralLabel(): ?string
    {
        return flexiblePagesTrans('pages.plural_lbl');
    }

    public static function getNavigationGroup(): string
    {
        return flexiblePagesTrans('pages.nav_group');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make(flexiblePagesTrans('pages.tabs.lbl'))
                    ->columnSpan(2)
                    ->tabs([
                        Tab::make(flexiblePagesTrans('pages.tabs.general'))
                            ->schema([
                                TitleField::create(true),
                                // TODO feature flag
                                CodeField::create(),
                                SlugField::create(false),
                                // TODO feature flag
                                ParentField::create()
                                    ->searchable(['title', 'code', 'slug', 'intro']),
                                IntroField::create(),
                                // TODO feature flag
                                AuthorField::create(),
                                HeroImageSection::create(true),
                                PublicationSection::create(),
                            ]),
                        Tab::make(flexiblePagesTrans('pages.tabs.content'))
                            ->schema([
                                CopyContentBlocksToLocalesAction::create(),
                                ContentBlocksField::create(),
                            ]),
                        Tab::make(flexiblePagesTrans('pages.tabs.overview'))
                            ->schema([
                                OverviewFields::create(1, true),
                            ]),
                        Tab::make(flexiblePagesTrans('pages.tabs.seo'))
                            ->schema([
                                SEOFields::create(1, true),
                            ]),
                    ])
                    ->persistTabInQueryString(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TitleColumn::create(),
                TextColumn::make('created_at')
                    ->label(flexiblePagesTrans('pages.table.created_at_col'))
                    ->dateTime(FilamentFlexibleBlocksConfig::getPublishingDateFormatting())
                    ->sortable(),
                PublishedColumn::create(),
            ])
            ->filters([
                PublishedFilter::create(),
            ])
            ->actions([
                EditAction::make(),
                PublishAction::make(),
                ViewAction::make(),
            ])
            ->bulkActions([
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
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'edit' => EditPage::route('/{record:id}/edit'),
        ];
    }
}
