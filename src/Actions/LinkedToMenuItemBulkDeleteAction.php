<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\DeleteBulkAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
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
            $referencedPages = [];
            $deletablePages = [];

            foreach ($records as $record) {
                /** @var Model&HasMenuLabel $record */
                // Prevent deletion if the page is referenced by a menu item
                /** @var ?MenuItem $menuItem */
                /** @phpstan-ignore-next-line */
                $menuItem = $record->menuItem;

                if ($menuItem) {
                    $referencedPages[] = $record;
                } else {
                    $deletablePages[] = $record;
                }
            }

            if (! empty($referencedPages)) {
                $pageNames = collect($referencedPages)->map(function (Model&HasMenuLabel $page) {
                    /** @var ?MenuItem $menuItem */
                    /** @phpstan-ignore-next-line */
                    $menuItem = $page->menuItem;
                    return '<li>'.
                        flexiblePagesTrans('pages.notifications.page_referenced_by_menu_item', [
                            'page' => $page->getMenuLabel(),
                            'menu' => $menuItem?->menu->name,
                            'menu_item' => $menuItem?->getDisplayLabel(),
                        ])
                        .'</li>';
                })->join('');

                Notification::make()
                    ->title(flexiblePagesTrans('pages.notifications.used_in_menu_bulk'))
                    ->body(new HtmlString("<ul>$pageNames</ul>"))
                    ->danger()
                    ->duration(12000)
                    ->send();
            }

            if (! empty($deletablePages)) {
                collect($deletablePages)->each(fn (Model&HasMenuLabel $record) => $record->delete());

                Notification::make()
                    ->title(flexiblePagesTrans('pages.notifications.bulk_delete_successful', ['count' => count($deletablePages)]))
                    ->success()
                    ->send();

                $action->success();
            }
        });
    }
}
