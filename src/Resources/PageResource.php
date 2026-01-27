<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\PageResource\Pages\CreatePage;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\PageResource\Pages\EditPage;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\PageResource\Pages\ListPages;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\PageResource\Pages\ManagePageTree;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\PageResource\Schemas\PageFormSchema;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\PageResource\Schemas\PageTableSchema;

class PageResource extends Resource
{
    use Translatable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';

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

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with([
                'parent',
            ]);

        return $query;
    }

    public static function form(Schema $schema): Schema
    {
        return PageFormSchema::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PageTableSchema::configure($table);
    }

    public static function getPages(): array
    {
        $pages = [
            'index' => ListPages::route('/'),
            'create' => CreatePage::route('/create'),
            'edit' => EditPage::route('/{record:id}/edit'),
        ];

        if (FilamentFlexibleContentBlockPages::config()->isParentAndPageTreeEnabled(static::getModel())) {
            $pages['tree'] = ManagePageTree::route('/tree');
        }

        return $pages;
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
            'overview_title',
            'overview_description',
            'code',
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return method_exists($record, 'getTranslation') ? $record->getTranslation('title', app()->getLocale()) : $record->getAttribute('title');
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        /** @var Page $record */
        $published = trans('filament-flexible-content-blocks::filament-flexible-content-blocks.is_published_state_unpublished');
        if ($record->isPublished()) {
            $published = trans('filament-flexible-content-blocks::filament-flexible-content-blocks.columns.is_published_state_published');
        }

        return [
            flexiblePagesTrans('pages.search.intro_lbl') => Str::limit(strip_tags($record->intro)),
            flexiblePagesTrans('pages.search.is_published_lbl') => $published,
        ];
    }
}
