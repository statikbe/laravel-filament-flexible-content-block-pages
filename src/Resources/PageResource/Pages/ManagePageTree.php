<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\PageResource\Pages;

use SolutionForest\FilamentTree\Resources\Pages\TreePage as BasePage;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\PageResource;

class ManagePageTree extends BasePage
{
    protected static string $resource = PageResource::class;

    // TODO make page depth configurable
    protected static int $maxDepth = 2;

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

    protected function hasViewAction(): bool
    {
        return false;
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    protected function getFooterWidgets(): array
    {
        return [];
    }

    // CUSTOMIZE ICON OF EACH RECORD, CAN DELETE
    // public function getTreeRecordIcon(?\Illuminate\Database\Eloquent\Model $record = null): ?string
    // {
    //     return null;
    // }
}
