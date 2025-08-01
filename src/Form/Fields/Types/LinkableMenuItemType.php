<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Form\Fields\Types;

use Closure;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Contracts\HasMenuLabel;

class LinkableMenuItemType extends AbstractMenuItemType
{
    protected string $model;

    protected ?Closure $getOptionLabelFromRecordUsing = null;

    protected int $searchResultLimit = 50;

    final public function __construct(string $model)
    {
        $this->model = $model;
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

    public function getSearchResults(string $search): array
    {
        $modelClass = $this->model;

        // Verify the model implements HasMenuLabel
        if (! is_subclass_of($modelClass, HasMenuLabel::class)) {
            return [];
        }

        /** @var class-string<HasMenuLabel> $modelClass */
        /** @phpstan-ignore-next-line */
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

    public function isModelType(): bool
    {
        return true;
    }
}
