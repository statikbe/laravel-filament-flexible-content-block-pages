<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Filament\Form\Fields\Types;

use Closure;
use Filament\Forms\Components\Select;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Contracts\HasMenuLabel;

class LinkableMenuItemType extends AbstractMenuItemType
{
    protected string $model;

    protected ?Closure $getOptionLabelFromRecordUsing = null;

    protected int $searchResultLimit = 50;

    public function __construct(string $model)
    {
        $this->model = $model;
        $this->icon = 'heroicon-o-link';

        // Set icon based on model if it has a getMorphClass method
        $modelInstance = app($model);
        if (method_exists($modelInstance, 'getMorphClass')) {
            $morphClass = $modelInstance->getMorphClass();
            $this->icon = $this->getIconForMorphClass($morphClass);
        }
    }

    public static function make(string $model): static
    {
        return new static($model);
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getAlias(): string
    {
        return app($this->model)->getMorphClass();
    }

    public function getOptionLabelFromRecordUsing(?Closure $closure): static
    {
        $this->getOptionLabelFromRecordUsing = $closure;

        return $this;
    }

    public function searchResultLimit(int $limit): static
    {
        $this->searchResultLimit = $limit;

        return $this;
    }

    public function getSelectField(): Select
    {
        return Select::make('linkable_id')
            ->label(flexiblePagesTrans('menu_items.form.linkable_item_lbl'))
            ->searchable()
            ->getSearchResultsUsing(fn (string $search): array => $this->getSearchResults($search))
            ->getOptionLabelUsing(fn ($value): ?string => $this->getOptionLabel($value))
            ->required()
            ->helperText($this->getHelperText());
    }

    public function getSearchResults(string $search): array
    {
        $modelClass = $this->model;

        // Verify the model implements HasMenuLabel
        if (! is_subclass_of($modelClass, HasMenuLabel::class)) {
            return [];
        }

        $results = $modelClass::searchForMenuItems($search)
            ->limit($this->searchResultLimit)
            ->get();

        return $results->mapWithKeys(function ($record) {
            return [$record->getKey() => $record->getMenuLabel()];
        })->toArray();
    }

    public function getOptionLabel($value): ?string
    {
        if (! $value) {
            return null;
        }

        $record = app($this->model)::find($value);
        if (! $record || ! ($record instanceof HasMenuLabel)) {
            return null;
        }

        return $this->getRecordLabel($record);
    }

    protected function getRecordLabel($record): string
    {
        if ($this->getOptionLabelFromRecordUsing) {
            return call_user_func($this->getOptionLabelFromRecordUsing, $record);
        }

        // Use HasMenuLabel interface if available
        if ($record instanceof HasMenuLabel) {
            return $record->getMenuLabel();
        }

        // Fallback to record key (ID)
        return (string) $record->getKey();
    }

    protected function getHelperText(): string
    {
        return flexiblePagesTrans('menu_items.form.linkable_help', [
            'model' => class_basename($this->model),
        ]);
    }

    protected function getIconForMorphClass(string $morphClass): string
    {
        // Check config for exact morph class match
        $configuredIcons = FilamentFlexibleContentBlockPages::config()
            ->getMenuModelIcons();

        if (isset($configuredIcons[$morphClass])) {
            return $configuredIcons[$morphClass];
        }

        // Fallback to pattern matching for backwards compatibility
        foreach ($configuredIcons as $pattern => $icon) {
            if (str_contains($morphClass, $pattern)) {
                return $icon;
            }
        }

        // Final fallback to default icon
        return 'heroicon-o-link';
    }

    public function isModelType(): bool
    {
        return true;
    }
}
