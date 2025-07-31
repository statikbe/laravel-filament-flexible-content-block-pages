<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\MenuResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions\LocaleSwitcher;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use SolutionForest\FilamentTree\Actions\DeleteAction;
use SolutionForest\FilamentTree\Actions\EditAction;
use SolutionForest\FilamentTree\Concern\TreeRecords\Translatable;
use SolutionForest\FilamentTree\Resources\Pages\TreePage;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Form\Forms\MenuItemForm;
use Statikbe\FilamentFlexibleContentBlockPages\Models\MenuItem;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\MenuResource;

class ManageMenuItems extends TreePage
{
    use Translatable;

    protected static string $resource = MenuResource::class;

    public mixed $menu;

    protected static int $maxDepth = 3;

    public function mount(): void
    {
        $menuModelClass = MenuResource::getModel();
        $recordId = request()->route('record');
        $this->menu = app($menuModelClass)
            ->query()
            ->with('menuItems.linkable')
            ->findOrFail($recordId);
    }

    public function getModel(): string
    {
        return FilamentFlexibleContentBlockPages::config()->getMenuItemModel()::class;
    }

    public function getTitle(): string
    {
        return flexiblePagesTrans('menu_items.manage.title', [
            'menu' => $this->menu->name ?? 'Menu',
        ]);
    }

    public function getBreadcrumb(): string
    {
        return flexiblePagesTrans('menu_items.manage.breadcrumb');
    }

    protected function getActions(): array
    {
        return [
            LocaleSwitcher::make(),
            CreateAction::make()
                ->label(flexiblePagesTrans('menu_items.tree.add_item'))
                ->mountUsing(
                    fn ($arguments, $form) => $form->fill([
                        'menu_id' => $this->menu->id,
                        'parent_id' => $arguments['parent_id'] ?? -1,
                        'is_visible' => true,
                        'target' => '_self',
                    ])
                )
                ->action(function (array $data): void {
                    $data['menu_id'] = $this->menu->id;
                    static::getModel()::create($data);
                }),
        ];
    }

    protected function getTreeActions(): array
    {
        return [
            EditAction::make()
                ->mountUsing(
                    function($arguments, $form, $model, MenuItem $record) {
                        $data = [
                            ...$record->toArray(),
                            'menu_id' => $this->record->id,
                        ];
                        $data['label'] = $record->getTranslation('label', $this->getActiveLocale());

                        $form->fill($data);
                    }
                ),
            DeleteAction::make(),
        ];
    }

    protected function getTreeRecords()
    {
        return static::getModel()::where('menu_id', $this->menu->id)
            ->with('linkable')
            ->orderBy('order')
            ->get();
    }

    public function getTreeRecordTitle(?Model $record = null): string
    {
        /** @var MenuItem $record */
        if (! $record) {
            return '';
        }

        $locale = $this->getActiveLocale();

        return $record->getDisplayLabel($locale);
    }

    public function getTreeRecordDescription(?Model $record = null): string|HtmlString|null
    {
        /** @var MenuItem $record */
        if (! $record) {
            return null;
        }

        $description = $this->getMenuItemTypeDescription($record);

        return new HtmlString($description);
    }

    protected function getMenuItemTypeDescription(MenuItem $record): string
    {
        if ($record->linkable_type && $record->linkable) {
            return flexiblePagesTrans('menu_items.tree.linked_to').' '.class_basename($record->linkable_type);
        }

        if ($record->url) {
            return flexiblePagesTrans('menu_items.tree.external_url').': '.$record->url;
        }

        if ($record->route) {
            return flexiblePagesTrans('menu_items.tree.route').': '.$record->route;
        }

        return flexiblePagesTrans('menu_items.tree.no_link');
    }

    public function getTreeRecordIcon(?\Illuminate\Database\Eloquent\Model $record = null): ?string
    {
        // TODO
        return parent::getTreeRecordIcon($record);
    }

    protected function getFormSchema(): array
    {
        return MenuItemForm::getSchema();
    }
}
