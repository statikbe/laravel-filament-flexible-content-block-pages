<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Statikbe\FilamentFlexibleContentBlockPages\Actions\LinkedToMenuItemBulkDeleteAction;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\PageResource\Pages\CreatePage;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\PageResource\Pages\EditPage;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\PageResource\Pages\ListPages;
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

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $recordRouteKeyName = 'id';

    protected static ?string $recordTitleAttribute = 'title';

    protected static int $globalSearchResultsLimit = 10;

    protected static ?bool $isGlobalSearchForcedCaseInsensitive = true;

    public static function getModel(): string
    {
        return FilamentFlexibleContentBlockPages::config()->getPageModel()::class;
    }

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

    public static function getNavigationSort(): ?int
    {
        return FilamentFlexibleContentBlockPages::config()->getPageNavigationSort(static::getModel());
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery()
            ->with([
                'parent',
            ]);

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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

    protected static function getGeneralTabFields(): array
    {
        $fields = [
            TitleField::create(true),
            IntroField::create(),
            HeroImageSection::create(true),
        ];

        if (FilamentFlexibleContentBlockPages::config()->isHeroCallToActionsEnabled(static::getModel())) {
            $fields[] = HeroCallToActionSection::create();
        }

        return $fields;
    }

    protected static function getContentTabFields(): array
    {
        return [
            CopyContentBlocksToLocalesAction::create(),
            ContentBlocksField::create(),
        ];
    }

    protected static function getSEOTabFields(): array
    {
        return [
            SEOFields::create(1, true),
        ];
    }

    protected static function getOverviewTabFields(): array
    {
        return [
            OverviewFields::create(1, true),
        ];
    }

    protected static function getAdvancedTabFields(): array
    {
        $config = FilamentFlexibleContentBlockPages::config();
        $modelClass = static::getModel();

        $fields = [
            PublicationSection::create(),
            CodeField::create(),
            SlugField::create(false),
        ];

        $gridFields = [];

        if ($config->isAuthorEnabled($modelClass)) {
            $gridFields[] = AuthorField::create();
        }

        if ($config->isParentEnabled($modelClass)) {
            $gridFields[] = ParentField::create()
                ->searchable(['title', 'code', 'slug', 'intro']);
        }

        if (! empty($gridFields)) {
            $fields[] = Grid::make()->schema($gridFields);
        }

        return $fields;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TitleColumn::create()
                    ->searchable(query: function ($query, $search) {
                        $locale = app()->getLocale();
                        $search = strtolower($search);

                        return $query->whereRaw("LOWER(title->>'$.{$locale}') LIKE ?", ["%{$search}%"]);
                    }),
                TextColumn::make('created_at')
                    ->label(flexiblePagesTrans('pages.table.created_at_col'))
                    ->dateTime(FilamentFlexibleBlocksConfig::getPublishingDateFormatting())
                    ->sortable(),
                PublishedColumn::create()
                    ->sortable(),
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
                LinkedToMenuItemBulkDeleteAction::make(),
            ])
            ->recordUrl(
                fn ($record): string => static::getUrl('edit', ['record' => $record])
            )
            ->modifyQueryUsing(function (Builder $query) {
                $query->with(['menuItem']);
            });
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

    public static function getGloballySearchableAttributes(): array
    {
        return [
            'title',
            'intro',
            'content_blocks',
            'seo_title',
            'seo_description',
            'seo_keywords',
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return $record->getTranslation('title', app()->getLocale());
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $published = trans('filament-flexible-content-blocks::filament-flexible-content-blocks.columns.is_published_state_unpublished');
        if ($record->isPublished()) {
            $published = trans(
                'filament-flexible-content-blocks::filament-flexible-content-blocks.columns.is_published_state_published'
            );
        }

        return [
            trans('filament-flexible-content-blocks::filament-flexible-content-blocks.form_component.intro_lbl') => Str::limit(strip_tags($record->intro)),
            trans('filament-flexible-content-blocks::filament-flexible-content-blocks.columns.is_published') => $published,
        ];
    }
}
