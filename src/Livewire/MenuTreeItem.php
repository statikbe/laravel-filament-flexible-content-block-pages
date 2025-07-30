<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Livewire;

use Livewire\Component;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;

class MenuTreeItem extends Component
{

    public $item; // MenuItem model

    public int $depth = 0;

    public int $maxDepth = 2;

    public bool $showActions = true;

    public bool $isExpanded = true;

    public function mount($item, int $depth = 0, int $maxDepth = 2, bool $showActions = true): void
    {
        // Convert array to model if needed
        if (is_array($item)) {
            $menuItemModel = FilamentFlexibleContentBlockPages::config()->getMenuItemModel();
            $this->item = $menuItemModel::with('linkable')->find($item['id']);
        } else {
            $this->item = $item;
        }

        $this->depth = $depth;
        $this->maxDepth = $maxDepth;
        $this->showActions = $showActions;
        $this->isExpanded = true;
    }

    public function toggleExpanded(): void
    {
        $this->isExpanded = ! $this->isExpanded;
    }

    public function canHaveChildren(): bool
    {
        return $this->depth < $this->maxDepth;
    }

    public function hasChildren(): bool
    {
        return $this->item->children()->exists();
    }

    public function getItemDisplayLabel(): string
    {
        return $this->item->getDisplayLabel();
    }

    public function getItemTypeLabel(): string
    {
        if ($this->item->linkable_type && $this->item->linkable) {
            return flexiblePagesTrans('menu_items.tree.linked_to').' '.class_basename($this->item->linkable_type);
        }

        if ($this->item->url) {
            return flexiblePagesTrans('menu_items.tree.external_url').': '.$this->item->url;
        }

        if ($this->item->route) {
            return flexiblePagesTrans('menu_items.tree.route').': '.$this->item->route;
        }

        return flexiblePagesTrans('menu_items.tree.no_link');
    }

    public function addChild(): void
    {
        // Dispatch event to parent component with item ID for adding child
        $this->dispatch('show-add-child-modal', ['parent_id' => $this->item->id]);
    }

    public function edit(): void
    {
        // Dispatch event to parent component with item ID for editing
        $this->dispatch('show-edit-modal', ['itemId' => $this->item->id]);
    }

    public function delete(): void
    {
        // Dispatch event to parent component with item ID for deletion
        $this->dispatch('show-delete-modal', ['itemId' => $this->item->id]);
    }


    public function render()
    {
        return view('filament-flexible-content-block-pages::livewire.menu-tree-item');
    }
}
