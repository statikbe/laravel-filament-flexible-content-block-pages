<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\PageResource\Pages;

use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use LaraZeus\SpatieTranslatable\Resources\Pages\ListRecords\Concerns\Translatable;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesConfig;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Actions\FlexibleLocaleSwitcher;

class ListPages extends ListRecords
{
    use Translatable;

    public static function getResource(): string
    {
        return FilamentFlexibleContentBlockPages::config()->getResources()[FilamentFlexibleContentBlockPagesConfig::TYPE_PAGE];
    }

    protected function getActions(): array
    {
        return [
            FlexibleLocaleSwitcher::make(),
            CreateAction::make(),
            Action::make('page_tree')
                ->label(flexiblePagesTrans('pages.actions.page_tree_lbl'))
                ->icon('heroicon-o-arrow-turn-down-right')
                ->color('gray')
                ->visible(FilamentFlexibleContentBlockPages::config()->isParentAndPageTreeEnabled($this->getModel()))
                ->url(fn () => FilamentFlexibleContentBlockPages::config()->isParentAndPageTreeEnabled($this->getModel()) ? static::getResource()::getUrl('tree') : null),
        ];
    }

    public function isTableSearchable(): bool
    {
        return true;
    }

    protected function applySearchToTableQuery(Builder $query): Builder
    {
        if (filled($searchQuery = $this->getTableSearch())) {
            /** @phpstan-ignore-next-line */
            $query->search($searchQuery)
                // Add search on code. This should not be included in the default search which could be used on the public website:
                ->orWhereRaw('LOWER(code) LIKE ?', ["%{$searchQuery}%"]);
        }

        return $query;
    }
}
