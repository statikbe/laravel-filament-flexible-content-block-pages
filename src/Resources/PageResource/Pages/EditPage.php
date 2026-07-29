<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\PageResource\Pages;

use Filament\Actions\ActionGroup;
use Filament\Resources\Pages\EditRecord;
use Statikbe\FilamentFlexibleContentBlockPages\Actions\LinkedToMenuItemDeleteAction;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Actions\FlexibleLocaleSwitcher;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Form\Actions\ReplicateAction;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Pages\EditRecord\Concerns\TranslatableWithMedia;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Resource\Pages\Actions\CopyContentBlocksToLocalesAction;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Table\Actions\ViewPageAction;

class EditPage extends EditRecord
{
    use TranslatableWithMedia;

    public static function getResource(): string
    {
        return FilamentFlexibleContentBlockPages::config()->getPageResource();
    }

    protected function getActions(): array
    {
        return [
            CopyContentBlocksToLocalesAction::make(),
            FlexibleLocaleSwitcher::make(),
            ActionGroup::make([
                ViewPageAction::make(),
                ReplicateAction::make()
                    ->color('gray')
                    ->successRedirectUrl(function (ReplicateAction $action) {
                        return static::getResource()::getUrl('edit', ['record' => $action->getReplica()]);
                    }),
                LinkedToMenuItemDeleteAction::make()
                    ->color('danger')
                    ->visible(fn (Page $record) => $record->isDeletable()),
            ])
                ->icon('heroicon-m-ellipsis-vertical')
                ->color('gray')
                ->button(),
        ];
    }
}
