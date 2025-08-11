<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Actions;

use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Contracts\HasMenuLabel;
use Statikbe\FilamentFlexibleContentBlockPages\Models\MenuItem;

/**
 * Delete action with extra check to see if the model is linked to a menu item.
 */
class LinkedToMenuItemDeleteAction extends DeleteAction
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->action(function (Model&HasMenuLabel $record, $action) {
            // Prevent deletion if the page is referenced by a menu item
            /** @var ?MenuItem $menuItem */
            /** @phpstan-ignore-next-line */
            $menuItem = $record->menuItem;

            if ($menuItem) {
                Notification::make()
                    ->title(flexiblePagesTrans('pages.notifications.used_in_menu', [
                        'menu' => $menuItem->menu->name,
                        'menu_item' => $menuItem->getDisplayLabel(),
                    ]))
                    ->danger()
                    ->duration(12000)
                    ->send();
            } else {
                $record->delete();
                $action->success();
            }
        });
    }
}
