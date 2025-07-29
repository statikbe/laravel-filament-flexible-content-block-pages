<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Livewire\Component;
use Statikbe\FilamentFlexibleContentBlockPages\Filament\Form\Forms\MenuItemForm;

class MenuTreeItem extends Component implements HasActions, HasForms
{
    use InteractsWithActions;
    use InteractsWithForms;

    public array $item;

    public int $depth = 0;

    public int $maxDepth = 2;

    public bool $showActions = true;

    public bool $isExpanded = true;

    public function mount(array $item, int $depth = 0, int $maxDepth = 2, bool $showActions = true): void
    {
        $this->item = $item;
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
        return ! empty($this->item['children']);
    }

    public function getItemDisplayLabel(): string
    {
        if (($this->item['use_model_title'] ?? false) && ! empty($this->item['linkable'])) {
            return $this->item['linkable']['title'] ?? $this->item['label'] ?? flexiblePagesTrans('menu_items.status.no_label');
        }

        return $this->item['label'] ?? flexiblePagesTrans('menu_items.status.no_label');
    }

    public function getItemTypeLabel(): string
    {
        if (! empty($this->item['linkable_type']) && ! empty($this->item['linkable'])) {
            return flexiblePagesTrans('menu_items.tree.linked_to').' '.class_basename($this->item['linkable_type']);
        }

        if (! empty($this->item['url'])) {
            return flexiblePagesTrans('menu_items.tree.external_url').': '.$this->item['url'];
        }

        if (! empty($this->item['route'])) {
            return flexiblePagesTrans('menu_items.tree.route').': '.$this->item['route'];
        }

        return flexiblePagesTrans('menu_items.tree.no_link');
    }

    public function addChildAction(): Action
    {
        return Action::make('addChild')
            ->form(MenuItemForm::getSchema())
            ->fillForm([
                'parent_id' => $this->item['id'],
                'is_visible' => true,
                'target' => '_self',
            ])
            ->action(function (array $data): void {
                $data['parent_id'] = $this->item['id'];
                $this->createMenuItem($data);
            })
            ->modalHeading(flexiblePagesTrans('menu_items.tree.add_child'))
            ->modalSubmitActionLabel(__('Create'))
            ->modalWidth('2xl')
            ->slideOver();
    }

    public function editAction(): Action
    {
        return Action::make('edit')
            ->form(MenuItemForm::getSchema())
            ->fillForm([
                'link_type' => $this->item['link_type'],
                'label' => $this->item['label'],
                'use_model_title' => $this->item['use_model_title'],
                'url' => $this->item['url'],
                'route' => $this->item['route'],
                'linkable_id' => $this->item['linkable_id'],
                'target' => $this->item['target'] ?? '_self',
                'icon' => $this->item['icon'],
                'is_visible' => $this->item['is_visible'],
            ])
            ->action(function (array $data): void {
                $this->updateMenuItem($this->item['id'], $data);
            })
            ->modalHeading(flexiblePagesTrans('menu_items.tree.edit'))
            ->modalSubmitActionLabel(__('Update'))
            ->modalWidth('2xl')
            ->slideOver();
    }

    public function deleteAction(): Action
    {
        return Action::make('delete')
            ->requiresConfirmation()
            ->modalHeading(flexiblePagesTrans('menu_items.tree.delete_confirm_title'))
            ->modalDescription(flexiblePagesTrans('menu_items.tree.delete_confirm_text'))
            ->modalSubmitActionLabel(flexiblePagesTrans('menu_items.tree.delete'))
            ->color('danger')
            ->action(function (): void {
                $this->deleteMenuItem($this->item['id']);
            });
    }

    protected function createMenuItem(array $data): void
    {
        // Dispatch event to parent component to handle creation
        $this->dispatch('menu-item-created', $data);
    }

    protected function updateMenuItem(int $itemId, array $data): void
    {
        // Dispatch event to parent component to handle update
        $this->dispatch('menu-item-updated', $itemId, $data);
    }

    protected function deleteMenuItem(int $itemId): void
    {
        // Dispatch event to parent component to handle deletion
        $this->dispatch('menu-item-deleted', $itemId);
    }

    public function render()
    {
        return view('filament-flexible-content-block-pages::livewire.menu-tree-item');
    }
}
