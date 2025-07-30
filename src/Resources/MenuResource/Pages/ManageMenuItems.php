<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\MenuResource\Pages;

use Filament\Actions\LocaleSwitcher;
use Filament\Infolists\Components\IconEntry;
use Filament\Panel;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Pages\PageRegistration;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;
use Kalnoy\Nestedset\QueryBuilder;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Filament\Form\Forms\MenuItemForm;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\MenuResource;
use Studio15\FilamentTree\Components\TreePage;

class ManageMenuItems extends TreePage
{
    use Translatable;

    protected static string $resource = MenuResource::class;

    public mixed $record;

    public function mount(): void
    {
        parent::mount();

        $menuModelClass = MenuResource::getModel();
        $recordId = request()->route('record');
        $this->record = app($menuModelClass)->findOrFail($recordId);
    }

    /**
     * Copied from Resource/Page to support routing in resources.
     * {@inheritDoc}
     */
    public static function route(string $path): PageRegistration
    {
        return new PageRegistration(
            page: static::class,
            route: fn (Panel $panel): Route => RouteFacade::get($path, static::class)
                ->middleware(static::getRouteMiddleware($panel))
                ->withoutMiddleware(static::getWithoutRouteMiddleware($panel)),
        );
    }

    public static function getModel(): string|QueryBuilder
    {
        return FilamentFlexibleContentBlockPages::config()->getMenuItemModel()::class;
    }

    protected function getViewData(): array
    {
        $query = static::getModel()::scoped(['menu_id' => $this->record->id])
            ->defaultOrder();

        return [
            'tree' => $query->get()->toTree(),
        ];
    }

    public function getTitle(): string
    {
        return flexiblePagesTrans('menu_items.manage.title', [
            'menu' => $this->record->name ?? 'Menu',
        ]);
    }

    public function getBreadcrumb(): string
    {
        // TODO fix
        return flexiblePagesTrans('menu_items.manage.breadcrumb');
    }

    public static function getCreateForm(): array
    {
        return MenuItemForm::getSchema();
    }

    public static function getEditForm(): array
    {
        return MenuItemForm::getSchema();
    }

    public static function getInfolistColumns(): array
    {
        return [
            IconEntry::make('is_visible')
                ->label('')
                ->icon(fn (bool $state): string => $state ? 'heroicon-o-eye' : 'heroicon-o-eye-slash')
                ->color(fn (bool $state): string => $state ? 'gray' : 'warning')
                ->tooltip(fn (bool $state): ?string => $state ? null : flexiblePagesTrans('menu_items.status.hidden'))
                ->hidden(fn (bool $state): bool => $state)
                ->size('sm'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            LocaleSwitcher::make(),
        ];
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['menu_id'] = $this->record->id;

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['menu_id'] = $this->record->id;

        return $data;
    }
}
