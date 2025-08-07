<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\PageResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\FilamentFlexibleContentBlockPagesConfig;
use Statikbe\FilamentFlexibleContentBlockPages\Models\MenuItem;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Actions\FlexibleLocaleSwitcher;
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
            DeleteAction::make()
                ->visible(fn (Page $record) => $record->isDeletable())
                ->action(function (Page $record, $action) {
                    // Prevent deletion if the page is referenced by a menu item
                    /** @var ?MenuItem $menuItem */
                    $menuItem = $record->menuItem();

                    if ($menuItem) {
                        Notification::make()
                            ->title(flexiblePagesTrans('pages.notifications.used_in_menu', [
                                'menu' => $menuItem->menu->name,
                                'menu_item' => $menuItem->getDisplayLabel(),
                            ]))
                            ->danger()
                            ->send();
                    } else {
                        $record->delete();
                        $action->success();
                    }
                }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
