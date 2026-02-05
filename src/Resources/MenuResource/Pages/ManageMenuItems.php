<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\MenuResource\Pages;

use Exception;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Facades\Filament;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
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
use Statikbe\FilamentFlexibleContentBlocks\Filament\Actions\FlexibleLocaleSwitcher;

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

    protected function getTreeQuery(): Builder
    {
        return $this->getModel()::query()->where('menu_id', $this->menu->getKey());
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
        $breadcrumbs->put(MenuResource::getUrl('edit', ['record' => $this->menu->getKey()]), $this->menu->name ?? 'Menu');
        $breadcrumbs->push(static::getBreadcrumb());

        return $breadcrumbs->toArray();
    }

    protected function getActions(): array
    {
        return [
            FlexibleLocaleSwitcher::make(),
            Action::make('edit_menu')
                ->label(flexiblePagesTrans('menu_items.actions.edit_menu'))
                ->icon(Heroicon::PencilSquare)
                ->color('gray')
                ->url(fn () => MenuResource::getUrl('edit', ['record' => $this->menu])),
            CreateAction::make()
                ->label(flexiblePagesTrans('menu_items.tree.add_item'))
                ->schema($this->getFormSchema())
                ->icon(Heroicon::Plus)
                ->fillForm([
                    'menu_id' => $this->menu->getKey(),
                    'parent_id' => \SolutionForest\FilamentTree\Support\Utils::defaultParentId(),
                    'is_visible' => true,
                    'target' => '_self',
                ])
                ->action(function (array $data): void {
                    $data['menu_id'] = $this->menu->getKey();
                    static::getModel()::create($data);
                }),
        ];
    }

    protected function getTreeActions(): array
    {
        return [
            EditAction::make()
                ->fillForm(function (MenuItem $record): array {
                    $data = [
                        ...$record->toArray(),
                        'menu_id' => $this->menu->getKey(),
                    ];

                    // handle translatable fields:
                    $data['label'] = $record->getTranslation('label', $this->getActiveLocale());
                    $data['url'] = $record->getRawOriginal('url')
                        ? $record->getTranslation('url', $this->getActiveLocale())
                        : null;

                    return $data;
                })
                ->action(function (Model $record, array $data): void {
                    // Ensure translatable fields have null value if not provided
                    if (! array_key_exists('label', $data)) {
                        $data['label'] = null;
                    }

                    if (! array_key_exists('url', $data)) {
                        $data['url'] = null;
                    }

                    $record->update($data);
                }),
            DeleteAction::make(),
        ];
    }

    protected function getTreeRecords()
    {
        return static::getModel()::where('menu_id', $this->menu->getKey())
            ->with('linkable')
            ->orderBy('order')
            ->get();
    }

    public function getTreeRecordTitle(?Model $record = null): string
    {
        if (! $record) {
            return '';
        }

        /** @var MenuItem $record */
        $locale = $this->getActiveLocale();

        return $record->getDisplayLabel($locale);
    }

    public function getTreeRecordDescription(?Model $record = null): string|HtmlString|null
    {
        if (! $record) {
            return null;
        }

        /** @var MenuItem $record */
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
        $resourceClass = FilamentFlexibleContentBlockPages::getModelResource($modelClass);

        if ($resourceClass) {
            try {
                return $resourceClass::getModelLabel();
            } catch (Exception $e) {
                // Fallback to class basename if resource method fails
            }
        }

        return class_basename($modelClass);
    }

    protected function getModelIconFromResource(string $modelClass): ?string
    {
        $resourceClass = FilamentFlexibleContentBlockPages::getModelResource($modelClass);

        if ($resourceClass) {
            try {
                $icon = $resourceClass::getNavigationIcon();

                // Filament v4 returns Heroicon object, convert to string
                if (is_object($icon) && method_exists($icon, 'getName')) {
                    return $icon->getName();
                }

                return is_string($icon) ? $icon : null;
            } catch (Exception $e) {
                // Fallback to null if resource method fails
            }
        }

        return null;
    }

    protected function getRouteUrl(string $routeName): ?string
    {
        try {
            return route($routeName);
        } catch (Exception $e) {
            return null;
        }
    }

    public function getTreeRecordIcon(?Model $record = null): ?string
    {
        if (! $record) {
            return null;
        }

        /** @var MenuItem $record */

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
            static::$modelMaxDepth = FilamentFlexibleContentBlockPages::config()->getMenuMaxDepth();
        }

        return static::$modelMaxDepth;
    }
}
