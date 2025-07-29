<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\MenuResource\Pages;

use Exception;
use Filament\Resources\Pages\Page;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\MenuResource;

class ManageMenuItems extends Page
{
    protected static string $resource = MenuResource::class;

    protected static string $view = 'filament-flexible-content-block-pages::filament.resources.menu-resource.pages.manage-menu-items';

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
                if (!empty($children)) {
                    $item['children'] = $children;
                }
                $tree[] = $item;
            }
        }

        return $tree;
    }

    public function addMenuItem(?int $parentId = null): void
    {
        try {
            // Validate parent if provided
            if ($parentId && !$this->validateMenuDepth(null, $parentId)) {
                $this->dispatch('show-error', [
                    'message' => flexiblePagesTrans('menu_items.errors.max_depth_exceeded')
                ]);
                return;
            }

            if ($parentId && !$this->validateParentExists($parentId)) {
                $this->dispatch('show-error', [
                    'message' => flexiblePagesTrans('menu_items.errors.parent_not_found')
                ]);
                return;
            }

            // This method will be called from Alpine.js
            $this->dispatch('open-menu-item-modal', [
                'menuId' => $this->record->id,
                'parentId' => $parentId,
                'action' => 'create',
            ]);

        } catch (Exception $e) {
            $this->dispatch('show-error', [
                'message' => flexiblePagesTrans('menu_items.errors.general_error', [
                    'error' => $e->getMessage()
                ])
            ]);
        }
    }

    public function editMenuItem(int $itemId): void
    {
        try {
            // Validate that the item exists and belongs to this menu
            if (!$this->validateMenuItemExists($itemId)) {
                $this->dispatch('show-error', [
                    'message' => flexiblePagesTrans('menu_items.errors.item_not_found')
                ]);
                return;
            }

            // This method will be called from Alpine.js
            $this->dispatch('open-menu-item-modal', [
                'menuId' => $this->record->id,
                'itemId' => $itemId,
                'action' => 'edit',
            ]);

        } catch (Exception $e) {
            $this->dispatch('show-error', [
                'message' => flexiblePagesTrans('menu_items.errors.general_error', [
                    'error' => $e->getMessage()
                ])
            ]);
        }
    }

    public function deleteMenuItem(int $itemId): void
    {
        try {
            $item = $this->getMenuItemSecurely($itemId);

            if (!$item) {
                $this->dispatch('show-error', [
                    'message' => flexiblePagesTrans('menu_items.errors.item_not_found')
                ]);
                return;
            }

            // Check if item has children and warn user
            if ($item->children()->count() > 0) {
                $this->dispatch('confirm-delete-with-children', [
                    'itemId' => $itemId,
                    'childCount' => $item->children()->count()
                ]);
                return;
            }

            // Delete the item
            $item->delete();

            $this->dispatch('menu-items-updated');
            $this->dispatch('show-success', [
                'message' => flexiblePagesTrans('menu_items.messages.item_deleted')
            ]);

        } catch (Exception $e) {
            $this->dispatch('show-error', [
                'message' => flexiblePagesTrans('menu_items.errors.delete_failed', [
                    'error' => $e->getMessage()
                ])
            ]);
        }
    }

    public function confirmDeleteWithChildren(int $itemId): void
    {
        try {
            $item = $this->getMenuItemSecurely($itemId);

            if ($item) {
                // Delete the item and all its descendants
                $item->delete();

                $this->dispatch('menu-items-updated');
                $this->dispatch('show-success', [
                    'message' => flexiblePagesTrans('menu_items.messages.item_and_children_deleted')
                ]);
            } else {
                $this->dispatch('show-error', [
                    'message' => flexiblePagesTrans('menu_items.errors.item_not_found')
                ]);
            }

        } catch (Exception $e) {
            $this->dispatch('show-error', [
                'message' => flexiblePagesTrans('menu_items.errors.delete_failed', [
                    'error' => $e->getMessage()
                ])
            ]);
        }
    }

    public function reorderMenuItems(array $orderedItems): void
    {
        try {
            $menuItemModel = FilamentFlexibleContentBlockPages::config()
                ->getMenuItemModel();

            if (empty($orderedItems)) {
                $this->dispatch('show-error', [
                    'message' => flexiblePagesTrans('menu_items.errors.no_items_to_reorder')
                ]);
                return;
            }

            // Validate that all items belong to this menu
            $itemIds = array_column($orderedItems, 'id');
            $validItems = $menuItemModel::whereIn('id', $itemIds)
                ->where('menu_id', $this->record->id)
                ->count();

            if ($validItems !== count($itemIds)) {
                $this->dispatch('show-error', [
                    'message' => flexiblePagesTrans('menu_items.errors.invalid_items_in_reorder')
                ]);
                return;
            }

            // Process the reordering with proper nested set operations
            $this->processNestedSetReorder($orderedItems);

            $this->dispatch('menu-items-updated');
            $this->dispatch('show-success', [
                'message' => flexiblePagesTrans('menu_items.messages.items_reordered')
            ]);

        } catch (Exception $e) {
            $this->dispatch('show-error', [
                'message' => flexiblePagesTrans('menu_items.errors.reorder_failed', [
                    'error' => $e->getMessage()
                ])
            ]);
        }
    }

    public function validateMenuDepth(?int $itemId, ?int $parentId = null): bool
    {
        if (!$parentId) {
            return true; // Root level is always valid
        }

        $menuItemModel = FilamentFlexibleContentBlockPages::config()
            ->getMenuItemModel();
        
        $parent = $menuItemModel::find($parentId);
        if (!$parent || $parent->menu_id !== $this->record->id) {
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
        
        if (!$item || $item->menu_id !== $this->record->id) {
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
            if (!isset($itemsByParent[$parentId])) {
                $itemsByParent[$parentId] = [];
            }
            $itemsByParent[$parentId][] = [
                'id' => $item['id'],
                'position' => $position
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
        usort($siblings, function($a, $b) {
            return $a['position'] <=> $b['position'];
        });

        $previousSibling = null;
        
        foreach ($siblings as $sibling) {
            $menuItem = $this->getMenuItemSecurely($sibling['id']);
            if (!$menuItem) continue;

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
            if (!$item) {
                $this->dispatch('show-error', [
                    'message' => flexiblePagesTrans('menu_items.errors.item_not_found')
                ]);
                return;
            }

            // Validate depth constraints
            if (!$this->validateMenuDepth($itemId, $newParentId)) {
                $this->dispatch('show-error', [
                    'message' => flexiblePagesTrans('menu_items.errors.max_depth_exceeded')
                ]);
                return;
            }

            if ($newParentId) {
                $parent = $this->getMenuItemSecurely($newParentId);
                if (!$parent) {
                    $this->dispatch('show-error', [
                        'message' => flexiblePagesTrans('menu_items.errors.parent_not_found')
                    ]);
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

            $this->dispatch('menu-items-updated');
            $this->dispatch('show-success', [
                'message' => flexiblePagesTrans('menu_items.messages.item_moved')
            ]);

        } catch (Exception $e) {
            $this->dispatch('show-error', [
                'message' => flexiblePagesTrans('menu_items.errors.move_failed', [
                    'error' => $e->getMessage()
                ])
            ]);
        }
    }
}
