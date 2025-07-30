<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\MenuResource\Pages;

use Filament\Actions\LocaleSwitcher;
use Filament\Infolists\Components\TextEntry;
use Filament\Panel;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Pages\PageRegistration;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;
use Illuminate\Support\Str;
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
        return FilamentFlexibleContentBlockPages::config()->getMenuItemModel();
    }

    public function getTreeQuery()
    {
        return static::getModel()::where('menu_id', $this->record->id);
    }

    public function getTitle(): string
    {
        return flexiblePagesTrans('menu_items.manage.title', [
            'menu' => $this->record->name ?? 'Menu',
        ]);
    }

    public function getBreadcrumb(): string
    {
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
            TextEntry::make('label')
                ->label(flexiblePagesTrans('menu_items.form.label_lbl')),
            TextEntry::make('link_type')
                ->label(flexiblePagesTrans('menu_items.form.link_type_lbl'))
                ->formatStateUsing(function (string $state): string {
                    return match ($state) {
                        'url' => flexiblePagesTrans('menu_items.form.types.url'),
                        'route' => flexiblePagesTrans('menu_items.form.types.route'),
                        default => flexiblePagesTrans('menu_items.form.types.model', ['model' => Str::title($state)])
                    };
                }),
            TextEntry::make('is_visible')
                ->label(flexiblePagesTrans('menu_items.form.is_visible_lbl'))
                ->formatStateUsing(fn (bool $state): string => $state ? flexiblePagesTrans('menu_items.status.visible') : flexiblePagesTrans('menu_items.status.hidden')
                )
                ->badge()
                ->color(fn (bool $state): string => $state ? 'success' : 'gray'),
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
