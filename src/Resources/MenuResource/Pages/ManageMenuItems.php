<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\MenuResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions\LocaleSwitcher;
use Filament\Facades\Filament;
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

    protected static int $modelMaxDepth;

    public mixed $menu;

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

    /**
     * Add an extra breadcrumb to the edit page of the menu.
     * {@inheritDoc}
     */
    public function getBreadcrumbs(): array
    {
        $breadcrumbs = collect(parent::getBreadcrumbs());

        $breadcrumbs->pop();
        $breadcrumbs->put(MenuResource::getUrl('edit', ['record' => $this->menu->id]), $this->menu->name ?? 'Menu');
        $breadcrumbs->push(static::getBreadcrumb());

        return $breadcrumbs->toArray();
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
                    function ($arguments, $form, $model, MenuItem $record) {
                        $data = [
                            ...$record->toArray(),
                            'menu_id' => $this->menu->id,
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

        return new HtmlString(' &rarr; '.$description);
    }

    protected function getMenuItemTypeDescription(MenuItem $record): string
    {
        if ($record->linkable_type && $record->linkable) {
            // Get model label from Filament resource if available
            $modelLabel = $this->getModelLabelFromResource($record->linkable::class);

            return flexiblePagesTrans('menu_items.tree.linked_to').' '.$modelLabel;
        } elseif ($record->url) {
            return flexiblePagesTrans('menu_items.tree.external_url').': '.$record->url;
        } elseif ($record->route) {
            // Show route URL instead of route name
            $routeUrl = $this->getRouteUrl($record->route);

            return flexiblePagesTrans('menu_items.tree.route').': '.($routeUrl ?: $record->route);
        } else {
            return flexiblePagesTrans('menu_items.tree.no_link');
        }
    }

    protected function getModelLabelFromResource(string $modelClass): string
    {
        $resourceClass = Filament::getModelResource($modelClass);

        if ($resourceClass) {
            try {
                return $resourceClass::getModelLabel();
            } catch (\Exception $e) {
                // Fallback to class basename if resource method fails
            }
        }

        return class_basename($modelClass);
    }

    protected function getModelIconFromResource(string $modelClass): ?string
    {
        $resourceClass = Filament::getModelResource($modelClass);

        if ($resourceClass) {
            try {
                return $resourceClass::getNavigationIcon();
            } catch (\Exception $e) {
                // Fallback to null if resource method fails
            }
        }

        return null;
    }

    protected function getRouteUrl(string $routeName): ?string
    {
        try {
            return route($routeName);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getTreeRecordIcon(?\Illuminate\Database\Eloquent\Model $record = null): ?string
    {
        /** @var MenuItem $record */
        if (! $record) {
            return null;
        }

        // If not visible, show eye-slash icon
        if (! $record->is_visible) {
            return 'heroicon-o-eye-slash';
        }

        // Return appropriate icon based on type
        if ($record->linkable_type && $record->linkable) {
            return $this->getModelIconFromResource($record->linkable::class) ?: 'heroicon-o-link';
        }

        if ($record->url) {
            return 'heroicon-o-link';
        }

        if ($record->route) {
            return 'heroicon-o-command-line';
        }

        return 'heroicon-o-bars-3';
    }

    protected function getFormSchema(): array
    {
        return MenuItemForm::getSchema();
    }

    public static function getMaxDepth(): int
    {
        if (! isset(static::$modelMaxDepth)) {
            // Since this is static, we need to get the menu from the current request
            $recordId = request()->route('record');
            if ($recordId) {
                $menuModelClass = MenuResource::getModel();
                $menu = app($menuModelClass)->find($recordId);
                if ($menu) {
                    return $menu->getEffectiveMaxDepth();
                }
            }

            // Fallback to global config if we can't determine the menu
            static::$modelMaxDepth = FilamentFlexibleContentBlockPages::config()->getMenuMaxDepth() ?? 3;
        }

        return static::$modelMaxDepth;
    }
}
