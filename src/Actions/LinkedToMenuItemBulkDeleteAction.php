<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Contracts\HasMenuLabel;
use Statikbe\FilamentFlexibleContentBlockPages\Models\MenuItem;

/**
 * Bulk delete action with extra check to see if the model is linked to a menu item.
 */
class LinkedToMenuItemBulkDeleteAction extends DeleteBulkAction
{
    public function setUp(): void
    {
        parent::setUp();

        $this->action(function (Collection $records, LinkedToMenuItemBulkDeleteAction $action) {
            $usedInMenu = false;
            foreach ($records as $record) {
                /** @var Model&HasMenuLabel $record */
                // Prevent deletion if the page is referenced by a menu item
                /** @var ?MenuItem $menuItem */
                $menuItem = $record->menuItem;

                if ($menuItem) {
                    $usedInMenu = true;

                    Notification::make()
                        ->title(flexiblePagesTrans('pages.notifications.used_in_menu_bulk', [
                            'page' => $record->getMenuLabel(),
                            'menu' => $menuItem->menu->name,
                            'menu_item' => $menuItem->getDisplayLabel(),
                        ]))
                        ->danger()
                        ->duration(12000)
                        ->send();
                }
            }

            if ($usedInMenu) {
                $action->failure();
            }
            else {
                $this->process(static fn (Collection $records) => $records->each(fn (Model&HasMenuLabel $record) => $record->delete()));
                $action->success();
            }
        });
    }
}
