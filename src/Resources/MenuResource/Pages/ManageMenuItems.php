<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\MenuResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Actions\LocaleSwitcher;
use Illuminate\Database\Eloquent\Model;
use SolutionForest\FilamentTree\Actions\DeleteAction;
use SolutionForest\FilamentTree\Actions\EditAction;
use SolutionForest\FilamentTree\Concern\TreeRecords\Translatable;
use SolutionForest\FilamentTree\Resources\Pages\TreePage;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Filament\Form\Forms\MenuItemForm;
use Statikbe\FilamentFlexibleContentBlockPages\Models\MenuItem;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\MenuResource;

class ManageMenuItems extends TreePage
{
    use Translatable;

    protected static string $resource = MenuResource::class;

    public mixed $record;

    protected static int $maxDepth = 3;

    public function mount(): void
    {
        $menuModelClass = MenuResource::getModel();
        $recordId = request()->route('record');
        $this->record = app($menuModelClass)->findOrFail($recordId);
    }

    public function getModel(): string
    {
        return FilamentFlexibleContentBlockPages::config()->getMenuItemModel()::class;
    }

    public function getTranslatableLocales(): array
    {
        return static::getResource()::getTranslatableLocales();
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

    protected function getActions(): array
    {
        return [
            LocaleSwitcher::make(),
            CreateAction::make()
                ->label(flexiblePagesTrans('menu_items.tree.add_item'))
                ->mountUsing(
                    fn ($arguments, $form, $model) => $form->fill([
                        'menu_id' => $this->record->id,
                        'parent_id' => $arguments['parent_id'] ?? -1,
                        'is_visible' => true,
                        'target' => '_self',
                    ])
                )
                ->action(function (array $data): void {
                    $data['menu_id'] = $this->record->id;
                    static::getModel()::create($data);
                }),
        ];
    }

    protected function getTreeActions(): array
    {
        return [
            EditAction::make()
                ->mountUsing(
                    fn ($arguments, $form, $model) => $form->fill([
                        ...$model->toArray(),
                        'menu_id' => $this->record->id,
                    ])
                ),
            DeleteAction::make(),
        ];
    }

    protected function getTreeRecords()
    {
        return static::getModel()::where('menu_id', $this->record->id)
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
