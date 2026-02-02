<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\PageResource\Pages;

use Filament\Actions\ActionGroup;
use Filament\Resources\Pages\EditRecord;
use Statikbe\FilamentFlexibleContentBlockPages\Actions\LinkedToMenuItemDeleteAction;
use Statikbe\FilamentFlexibleContentBlockPages\Actions\ViewAction;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesConfig;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\PageResource;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Actions\FlexibleLocaleSwitcher;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Form\Actions\ReplicateAction;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Pages\EditRecord\Concerns\TranslatableWithMedia;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Resource\Pages\Actions\CopyContentBlocksToLocalesAction;

class EditPage extends EditRecord
{
    use TranslatableWithMedia;

    public static function getResource(): string
    {
        return FilamentFlexibleContentBlockPages::config()->getResources()[FilamentFlexibleContentBlockPagesConfig::TYPE_PAGE];
    }

    protected function getActions(): array
    {
        return [
            CopyContentBlocksToLocalesAction::make(),
            FlexibleLocaleSwitcher::make(),
            ActionGroup::make([
                ViewAction::make(),
                ReplicateAction::make()
                    ->color('gray')
                    ->successRedirectUrl(fn (ReplicateAction $action) => PageResource::getUrl('edit', ['record' => $action->getReplica()])),
                LinkedToMenuItemDeleteAction::make()
                    ->color('danger')
                    ->visible(fn (Page $record) => $record->isDeletable()),
            ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->color('gray')
                ->button(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
