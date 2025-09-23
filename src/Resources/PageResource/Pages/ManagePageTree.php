<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\PageResource\Pages;

use Illuminate\Database\Eloquent\Model;
use SolutionForest\FilamentTree\Actions\EditAction;
use SolutionForest\FilamentTree\Actions\ViewAction;
use SolutionForest\FilamentTree\Resources\Pages\TreePage;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesConfig;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\HasPageAttributes;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\Linkable;

class ManagePageTree extends TreePage
{
    public static function getMaxDepth(): int
    {
        return FilamentFlexibleContentBlockPages::config()->getPageTreeMaximumDepth(static::getResource()::getModel());
    }

    public static function getResource(): string
    {
        return FilamentFlexibleContentBlockPages::config()->getResources()[FilamentFlexibleContentBlockPagesConfig::TYPE_PAGE];
    }

    protected function getActions(): array
    {
        return [
            $this->getCreateAction(),
        ];
    }

    protected function hasDeleteAction(): bool
    {
        return false;
    }

    protected function hasEditAction(): bool
    {
        return true;
    }

    protected function configureEditAction(EditAction $action): EditAction
    {
        return $action
            ->iconButton()
            ->authorize(fn (Model $record): bool => static::getResource()::canEdit($record))
            ->url(function (Model $record) {
                return FilamentFlexibleContentBlockPages::config()->getResources()[FilamentFlexibleContentBlockPagesConfig::TYPE_PAGE]::getUrl('edit', ['record' => $record]);
            });
    }

    protected function hasViewAction(): bool
    {
        return true;
    }

    protected function configureViewAction(ViewAction $action): ViewAction
    {
        return $action
            ->iconButton()
            ->authorize(fn (Model $record): bool => static::getResource()::canView($record))
            ->url(function (Linkable $record) {
                return $record->getPreviewUrl();
            }, true);
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    protected function getFooterWidgets(): array
    {
        return [];
    }

    public function getTreeRecordIcon(?Model $record = null): ?string
    {
        /** @var HasPageAttributes $record */
        return $record?->isPublished() ? null : 'heroicon-o-eye-slash';
    }
}
