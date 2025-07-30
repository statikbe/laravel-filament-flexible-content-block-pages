<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\MenuResource\Pages;

use Exception;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\LocaleSwitcher;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Schema;
use Livewire\Attributes\On;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Filament\Form\Forms\MenuItemForm;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\MenuResource;

class ManageMenuItems extends Page implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;
    use Translatable;

    protected static string $resource = MenuResource::class;

    protected static string $view = 'filament-flexible-content-block-pages::filament.resources.menu-resource.pages.manage-menu-items';

    public mixed $record;

    public $refreshKey = 0;

    public function mount(int|string $record): void
    {
        $menuModel = static::getResource()::getModel();
        $this->record = $menuModel::findOrFail($record);
    }

    protected function refreshTree(): void
    {
        $this->refreshKey++;
        $this->dispatch('menu-items-updated');
    }

    public function getTitle(): string
    {
        return flexiblePagesTrans('menu_items.manage.title', [
            'menu' => $this->record->name,
        ]);
    }

    public function getBreadcrumb(): string
    {
        return flexiblePagesTrans('menu_items.manage.breadcrumb');
    }

    public function getMaxDepth(): int
    {
        return FilamentFlexibleContentBlockPages::config()
            ->getMenuMaxDepth();
    }

    public function getMenuItems(): array
    {
        $items = $this->record->allMenuItems()
            ->with('linkable')
            ->get();

        return $this->buildTree($items->toArray());
    }

    protected function buildTree(array $items, $parentId = null): array
    {
        $tree = [];

        foreach ($items as $item) {
            if ($item['parent_id'] == $parentId) {
                $children = $this->buildTree($items, $item['id']);
                if (! empty($children)) {
                    $item['children'] = $children;
                }
                $tree[] = $item;
            }
        }

        return $tree;
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('addMenuItem')
                ->label(flexiblePagesTrans('menu_items.tree.add_item'))
                ->icon('heroicon-o-plus')
                ->color('primary')
                ->form($this->getMenuItemFormSchema())
                ->fillForm(function (array $arguments): array {
                    $parentId = $arguments['parent_id'] ?? null;

                    return [
                        'parent_id' => $parentId,
                        'is_visible' => true,
                        'target' => '_self',
                    ];
                })
                ->action(function (array $data, array $arguments): void {
                    // Merge parent_id from arguments into data
                    $parentId = $arguments['parent_id'] ?? null;
                    if ($parentId) {
                        $data['parent_id'] = $parentId;
                    }
                    $this->createMenuItem($data);
                })
                ->modalHeading(function (array $arguments): string {
                    $parentId = $arguments['parent_id'] ?? null;

                    return $parentId
                        ? flexiblePagesTrans('menu_items.tree.add_child')
                        : flexiblePagesTrans('menu_items.tree.add_item');
                })
                ->modalSubmitActionLabel(__('Create'))
                ->modalWidth('2xl')
                ->slideOver()
                ->extraModalFooterActions([
                    LocaleSwitcher::make(),
                ]),
        ];
    }

    protected function getActions(): array
    {
        return [
            $this->editMenuItemAction(),
            $this->deleteMenuItemAction(),
        ];
    }

    public function editMenuItemAction(): Action
    {
        return Action::make('editMenuItem')
            ->form($this->getMenuItemFormSchema())
            ->fillForm(function (array $arguments): array {
                $itemId = $arguments['itemId'] ?? null;
                if (! $itemId) {
                    return [];
                }

                $item = $this->getMenuItemSecurely($itemId);
                if (! $item) {
                    return [];
                }

                return [
                    'link_type' => $item->link_type,
                    'label' => $item->label,
                    'use_model_title' => $item->use_model_title,
                    'url' => $item->url,
                    'route' => $item->route,
                    'linkable_id' => $item->linkable_id,
                    'target' => $item->target ?? '_self',
                    'icon' => $item->icon,
                    'is_visible' => $item->is_visible,
                ];
            })
            ->action(function (array $data, array $arguments): void {
                $itemId = $arguments['itemId'] ?? null;
                if ($itemId) {
                    $this->updateMenuItem($itemId, $data);
                }
            })
            ->modalHeading(flexiblePagesTrans('menu_items.tree.edit'))
            ->modalSubmitActionLabel(__('Update'))
            ->modalWidth('2xl')
            ->slideOver()
            ->extraModalFooterActions([
                LocaleSwitcher::make(),
            ]);
    }

    public function deleteMenuItemAction(): Action
    {
        return Action::make('deleteMenuItem')
            ->requiresConfirmation()
            ->modalHeading(flexiblePagesTrans('menu_items.tree.delete_confirm_title'))
            ->modalDescription(flexiblePagesTrans('menu_items.tree.delete_confirm_text'))
            ->modalSubmitActionLabel(flexiblePagesTrans('menu_items.tree.delete'))
            ->color('danger')
            ->action(function (array $arguments): void {
                $itemId = $arguments['itemId'] ?? null;
                if ($itemId) {
                    $this->deleteMenuItem($itemId);
                }
            });
    }

    protected function getMenuItemFormSchema(): array
    {
        return MenuItemForm::getSchema();
    }

    public function deleteMenuItem(int $itemId): void
    {
        try {
            $item = $this->getMenuItemSecurely($itemId);

            if (! $item) {
                Notification::make()
                    ->title(flexiblePagesTrans('menu_items.errors.item_not_found'))
                    ->danger()
                    ->send();

                return;
            }

            // Check if item has children and warn user
            if ($item->children()->count() > 0) {
                $this->dispatch('confirm-delete-with-children', [
                    'itemId' => $itemId,
                    'childCount' => $item->children()->count(),
                ]);

                return;
            }

            // Delete the item
            $item->delete();

            $this->refreshTree();

            Notification::make()
                ->title(flexiblePagesTrans('menu_items.messages.item_deleted'))
                ->success()
                ->send();

        } catch (Exception $e) {
            Notification::make()
                ->title(flexiblePagesTrans('menu_items.errors.delete_failed', [
                    'error' => $e->getMessage(),
                ]))
                ->danger()
                ->send();
        }
    }

    public function confirmDeleteWithChildren(int $itemId): void
    {
        try {
            $item = $this->getMenuItemSecurely($itemId);

            if ($item) {
                // Delete the item and all its descendants
                $item->delete();

                $this->refreshTree();

                Notification::make()
                    ->title(flexiblePagesTrans('menu_items.messages.item_and_children_deleted'))
                    ->success()
                    ->send();
            } else {
                Notification::make()
                    ->title(flexiblePagesTrans('menu_items.errors.item_not_found'))
                    ->danger()
                    ->send();
            }

        } catch (Exception $e) {
            Notification::make()
                ->title(flexiblePagesTrans('menu_items.errors.delete_failed', [
                    'error' => $e->getMessage(),
                ]))
                ->danger()
                ->send();
        }
    }

    public function reorderMenuItems(array $orderedItems): void
    {
        try {
            $menuItemModel = FilamentFlexibleContentBlockPages::config()
                ->getMenuItemModel();

            if (empty($orderedItems)) {
                Notification::make()
                    ->title(flexiblePagesTrans('menu_items.errors.no_items_to_reorder'))
                    ->danger()
                    ->send();

                return;
            }

            // Validate that all items belong to this menu
            $itemIds = array_column($orderedItems, 'id');
            
            // Filter out null IDs
            $itemIds = array_filter($itemIds, function($id) {
                return $id !== null && is_numeric($id);
            });
            
            if (empty($itemIds)) {
                Notification::make()
                    ->title(flexiblePagesTrans('menu_items.errors.no_items_to_reorder'))
                    ->danger()
                    ->send();
                    
                return;
            }
            
            $validItems = $menuItemModel::whereIn('id', $itemIds)
                ->where('menu_id', $this->record->id)
                ->count();

            if ($validItems !== count($itemIds)) {
                Notification::make()
                    ->title(flexiblePagesTrans('menu_items.errors.invalid_items_in_reorder'))
                    ->danger()
                    ->send();

                return;
            }

            // Process the reordering with proper nested set operations
            $this->processNestedSetReorder($orderedItems);

            $this->refreshTree();

            Notification::make()
                ->title(flexiblePagesTrans('menu_items.messages.items_reordered'))
                ->success()
                ->send();

        } catch (Exception $e) {
            Notification::make()
                ->title(flexiblePagesTrans('menu_items.errors.reorder_failed', [
                    'error' => $e->getMessage(),
                ]))
                ->danger()
                ->send();
        }
    }

    public function validateMenuDepth(?int $itemId, ?int $parentId = null): bool
    {
        if (! $parentId) {
            return true; // Root level is always valid
        }

        $menuItemModel = FilamentFlexibleContentBlockPages::config()
            ->getMenuItemModel();

        $parent = $menuItemModel::find($parentId);
        if (! $parent || $parent->menu_id !== $this->record->id) {
            return false;
        }

        $currentDepth = $parent->depth + 1;
        $maxDepth = $this->getMaxDepth();

        return $currentDepth <= $maxDepth;
    }

    protected function validateParentExists(int $parentId): bool
    {
        $menuItemModel = FilamentFlexibleContentBlockPages::config()
            ->getMenuItemModel();

        $parent = $menuItemModel::find($parentId);

        return $parent && $parent->menu_id === $this->record->id;
    }

    protected function validateMenuItemExists(int $itemId): bool
    {
        $menuItemModel = FilamentFlexibleContentBlockPages::config()
            ->getMenuItemModel();

        $item = $menuItemModel::find($itemId);

        return $item && $item->menu_id === $this->record->id;
    }

    protected function getMenuItemSecurely(int $itemId)
    {
        $menuItemModel = FilamentFlexibleContentBlockPages::config()
            ->getMenuItemModel();

        $item = $menuItemModel::find($itemId);

        if (! $item || $item->menu_id !== $this->record->id) {
            return null;
        }

        return $item;
    }

    protected function processNestedSetReorder(array $orderedItems): void
    {
        // Group items by their new parent
        $itemsByParent = [];
        foreach ($orderedItems as $position => $item) {
            $parentId = $item['parent_id'] ?? null;
            if (! isset($itemsByParent[$parentId])) {
                $itemsByParent[$parentId] = [];
            }
            $itemsByParent[$parentId][] = [
                'id' => $item['id'],
                'position' => $position,
            ];
        }

        // Process root level items first
        if (isset($itemsByParent[null])) {
            $this->reorderSiblings($itemsByParent[null]);
        }

        // Process items with parents
        foreach ($itemsByParent as $parentId => $children) {
            if ($parentId !== null) {
                $parent = $this->getMenuItemSecurely($parentId);
                if ($parent) {
                    // Move items to the correct parent first
                    foreach ($children as $child) {
                        $menuItem = $this->getMenuItemSecurely($child['id']);
                        if ($menuItem && $menuItem->parent_id !== $parentId) {
                            $menuItem->appendToNode($parent)->save();
                        }
                    }

                    // Then reorder within the parent
                    $this->reorderSiblings($children, $parent);
                }
            }
        }
    }

    protected function reorderSiblings(array $siblings, $parent = null): void
    {
        // Sort siblings by their intended position
        usort($siblings, function ($a, $b) {
            return $a['position'] <=> $b['position'];
        });

        $previousSibling = null;

        foreach ($siblings as $sibling) {
            $menuItem = $this->getMenuItemSecurely($sibling['id']);
            if (! $menuItem) {
                continue;
            }

            if ($parent) {
                // Moving within a parent node
                if ($previousSibling) {
                    $menuItem->afterNode($previousSibling)->save();
                } else {
                    $menuItem->prependToNode($parent)->save();
                }
            } else {
                // Moving at root level
                if ($previousSibling) {
                    $menuItem->afterNode($previousSibling)->save();
                } else {
                    $menuItem->makeRoot()->save();
                }
            }

            $previousSibling = $menuItem;
        }
    }

    public function moveMenuItem(int $itemId, ?int $newParentId = null, ?int $afterItemId = null): void
    {
        try {
            $item = $this->getMenuItemSecurely($itemId);
            if (! $item) {
                Notification::make()
                    ->title(flexiblePagesTrans('menu_items.errors.item_not_found'))
                    ->danger()
                    ->send();

                return;
            }

            // Validate depth constraints
            if (! $this->validateMenuDepth($itemId, $newParentId)) {
                Notification::make()
                    ->title(flexiblePagesTrans('menu_items.errors.max_depth_exceeded'))
                    ->danger()
                    ->send();

                return;
            }

            if ($newParentId) {
                $parent = $this->getMenuItemSecurely($newParentId);
                if (! $parent) {
                    Notification::make()
                        ->title(flexiblePagesTrans('menu_items.errors.parent_not_found'))
                        ->danger()
                        ->send();

                    return;
                }

                if ($afterItemId) {
                    $afterItem = $this->getMenuItemSecurely($afterItemId);
                    if ($afterItem) {
                        $item->afterNode($afterItem)->save();
                    } else {
                        $item->appendToNode($parent)->save();
                    }
                } else {
                    $item->prependToNode($parent)->save();
                }
            } else {
                // Moving to root level
                if ($afterItemId) {
                    $afterItem = $this->getMenuItemSecurely($afterItemId);
                    if ($afterItem) {
                        $item->afterNode($afterItem)->save();
                    } else {
                        $item->makeRoot()->save();
                    }
                } else {
                    $item->makeRoot()->save();
                }
            }

            $this->refreshTree();

            Notification::make()
                ->title(flexiblePagesTrans('menu_items.messages.item_moved'))
                ->success()
                ->send();

        } catch (Exception $e) {
            Notification::make()
                ->title(flexiblePagesTrans('menu_items.errors.move_failed', [
                    'error' => $e->getMessage(),
                ]))
                ->danger()
                ->send();
        }
    }

    public function getModalOptionsData(): array
    {
        return [
            'linkableTypes' => $this->getLinkableTypes(),
            'availableRoutes' => $this->getAvailableRoutes(),
        ];
    }

    public function getLinkableTypes(): array
    {
        $types = [];
        $configuredModels = FilamentFlexibleContentBlockPages::config()
            ->getMenuLinkableModels();

        foreach ($configuredModels as $modelClass) {
            if (is_string($modelClass) && class_exists($modelClass)) {
                $types[] = [
                    'alias' => $this->getModelAlias($modelClass),
                    'label' => flexiblePagesTrans('menu_items.form.types.model', [
                        'model' => class_basename($modelClass),
                    ]),
                    'model' => $modelClass,
                ];
            }
        }

        return $types;
    }

    public function getAvailableRoutes(): array
    {
        $routes = [];
        $routeCollection = app('router')->getRoutes();

        foreach ($routeCollection as $route) {
            $name = $route->getName();
            if ($name && ! str_contains($name, 'filament.') && ! str_contains($name, 'debugbar')) {
                $routes[] = [
                    'name' => $name,
                    'uri' => $route->uri(),
                ];
            }
        }

        return $routes;
    }

    public function getMenuItem(int $itemId): ?array
    {
        $item = $this->getMenuItemSecurely($itemId);

        if (! $item) {
            return null;
        }

        return [
            'id' => $item->id,
            'link_type' => $item->link_type,
            'label' => $item->label,
            'use_model_title' => (bool) $item->use_model_title,
            'url' => $item->url,
            'route' => $item->route,
            'linkable_id' => $item->linkable_id,
            'linkable_type' => $item->linkable_type,
            'target' => $item->target ?? '_self',
            'icon' => $item->icon,
            'is_visible' => (bool) $item->is_visible,
            'linkable' => $item->linkable ? [
                'id' => $item->linkable->getKey(),
                'title' => method_exists($item->linkable, 'getMenuLabel')
                    ? $item->linkable->getMenuLabel()
                    : ($item->linkable->title ?? $item->linkable->name ?? 'Unknown'),
            ] : null,
        ];
    }

    public function searchLinkableItems(array $params): array
    {
        $linkType = $params['linkType'] ?? '';
        $search = $params['search'] ?? '';

        if (strlen($search) < 2) {
            return [];
        }

        $types = $this->getLinkableTypes();
        $type = collect($types)->firstWhere('alias', $linkType);

        if (! $type || ! isset($type['model'])) {
            return [];
        }

        $modelClass = $type['model'];

        if (! class_exists($modelClass)) {
            return [];
        }

        try {
            $query = $modelClass::query();

            // Use searchForMenuItems if available, otherwise basic search
            if (method_exists($modelClass, 'searchForMenuItems')) {
                $results = $modelClass::searchForMenuItems($search)->limit(50)->get();
            } else {
                // Basic search on common fields
                $searchableFields = ['title', 'name', 'label'];
                $query->where(function ($q) use ($searchableFields, $search) {
                    foreach ($searchableFields as $field) {
                        if (Schema::hasColumn((new $modelClass)->getTable(), $field)) {
                            $q->orWhere($field, 'LIKE', "%{$search}%");
                        }
                    }
                });
                $results = $query->limit(50)->get();
            }

            return $results->map(function ($item) {
                return [
                    'id' => $item->getKey(),
                    'label' => method_exists($item, 'getMenuLabel')
                        ? $item->getMenuLabel()
                        : ($item->title ?? $item->name ?? 'Item #'.$item->getKey()),
                ];
            })->toArray();

        } catch (Exception $e) {
            return [];
        }
    }

    public function createMenuItem(array $data): void
    {
        try {
            $menuItemModel = FilamentFlexibleContentBlockPages::config()
                ->getMenuItemModel();

            // Validate required fields based on link type
            $this->validateMenuItemData($data);

            // Create the menu item
            $menuItem = new $menuItemModel;
            $menuItem->fill([
                'menu_id' => $this->record->id,
                'parent_id' => $data['parent_id'] ?? null,
                'link_type' => $data['link_type'],
                'use_model_title' => $data['use_model_title'] ?? false,
                'url' => $data['url'] ?? null,
                'route' => $data['route'] ?? null,
                'linkable_id' => $data['linkable_id'] ?? null,
                'linkable_type' => $data['linkable_type'] ?? null,
                'target' => $data['target'] ?? '_self',
                'icon' => $data['icon'] ?? null,
                'is_visible' => $data['is_visible'] ?? true,
            ]);

            // Handle translatable label field separately
            if (isset($data['label'])) {
                $menuItem->label = $data['label'];
            }

            // Handle nested set positioning
            if ($data['parent_id']) {
                $parent = $this->getMenuItemSecurely($data['parent_id']);
                if ($parent) {
                    $menuItem->appendToNode($parent)->save();
                } else {
                    throw new Exception('Parent item not found');
                }
            } else {
                $menuItem->makeRoot()->save();
            }

            Notification::make()
                ->title(flexiblePagesTrans('menu_items.messages.item_created'))
                ->success()
                ->send();

            // Refresh the menu items tree view
            $this->refreshTree();

        } catch (Exception $e) {
            Notification::make()
                ->title(flexiblePagesTrans('menu_items.errors.create_failed', [
                    'error' => $e->getMessage(),
                ]))
                ->danger()
                ->send();
        }
    }

    public function updateMenuItem(int $itemId, array $data): void
    {
        try {
            $item = $this->getMenuItemSecurely($itemId);

            if (! $item) {
                Notification::make()
                    ->title(flexiblePagesTrans('menu_items.errors.item_not_found'))
                    ->danger()
                    ->send();

                return;
            }

            // Validate required fields based on link type
            $this->validateMenuItemData($data);

            // Update the menu item
            $item->update([
                'link_type' => $data['link_type'],
                'use_model_title' => $data['use_model_title'] ?? false,
                'url' => $data['url'] ?? null,
                'route' => $data['route'] ?? null,
                'linkable_id' => $data['linkable_id'] ?? null,
                'linkable_type' => $data['linkable_type'] ?? null,
                'target' => $data['target'] ?? '_self',
                'icon' => $data['icon'] ?? null,
                'is_visible' => $data['is_visible'] ?? true,
            ]);

            // Handle translatable label field separately
            if (isset($data['label'])) {
                $item->label = $data['label'];
                $item->save();
            }

            Notification::make()
                ->title(flexiblePagesTrans('menu_items.messages.item_updated'))
                ->success()
                ->send();

            // Refresh the menu items tree view
            $this->refreshTree();

        } catch (Exception $e) {
            Notification::make()
                ->title(flexiblePagesTrans('menu_items.errors.update_failed', [
                    'error' => $e->getMessage(),
                ]))
                ->danger()
                ->send();
        }
    }

    protected function validateMenuItemData(array $data): void
    {
        // Label is only required if use_model_title is false
        if (empty($data['label']) && ! ($data['use_model_title'] ?? false)) {
            throw new Exception(flexiblePagesTrans('menu_items.form.label_lbl').' is required');
        }

        switch ($data['link_type']) {
            case 'url':
                if (empty($data['url'])) {
                    throw new Exception(flexiblePagesTrans('menu_items.form.url_lbl').' is required for URL links');
                }
                break;

            case 'route':
                if (empty($data['route'])) {
                    throw new Exception(flexiblePagesTrans('menu_items.form.route_lbl').' is required for route links');
                }
                break;

            default:
                // Model type - check if linkable_id is provided
                if (empty($data['linkable_id'])) {
                    throw new Exception(flexiblePagesTrans('menu_items.form.linkable_item_lbl').' is required for model links');
                }
                break;
        }
    }

    protected function getModelAlias(string $modelClass): string
    {
        // Get the morph alias for the model
        $morphMap = FilamentFlexibleContentBlockPages::config()->getMorphMap();

        foreach ($morphMap as $alias => $class) {
            if ($class === $modelClass) {
                return $alias;
            }
        }

        // Fallback to class basename if no morph alias found
        return strtolower(class_basename($modelClass));
    }

    public function getItemDisplayLabel(array $item): string
    {
        if (($item['use_model_title'] ?? false) && ! empty($item['linkable'])) {
            return $item['linkable']['title'] ?? $item['label'] ?? flexiblePagesTrans('menu_items.status.no_label');
        }

        return $item['label'] ?? flexiblePagesTrans('menu_items.status.no_label');
    }

    public function getItemTypeLabel(array $item): string
    {
        if (! empty($item['linkable_type']) && ! empty($item['linkable'])) {
            return flexiblePagesTrans('menu_items.tree.linked_to').' '.class_basename($item['linkable_type']);
        }

        if (! empty($item['url'])) {
            return flexiblePagesTrans('menu_items.tree.external_url').': '.$item['url'];
        }

        if (! empty($item['route'])) {
            return flexiblePagesTrans('menu_items.tree.route').': '.$item['route'];
        }

        return flexiblePagesTrans('menu_items.tree.no_link');
    }

    #[On('show-add-child-modal')]
    public function handleShowAddChildModal(array $data): void
    {
        $parentId = $data['parent_id'] ?? null;
        $this->mountAction('addMenuItem', ['parent_id' => $parentId]);
    }

    #[On('show-edit-modal')]
    public function handleShowEditModal(array $data): void
    {
        $itemId = $data['itemId'] ?? null;
        if ($itemId) {
            $this->mountAction('editMenuItem', ['itemId' => $itemId]);
        }
    }

    #[On('show-delete-modal')]
    public function handleShowDeleteModal(array $data): void
    {
        $itemId = $data['itemId'] ?? null;
        if ($itemId) {
            $this->mountAction('deleteMenuItem', ['itemId' => $itemId]);
        }
    }
}
