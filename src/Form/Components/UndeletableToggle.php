<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Form\Components;

use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\Page;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;

class UndeletableToggle extends Toggle
{
    const FIELD = 'is_undeletable';

    public static function create(?string $label = null): static
    {
        $field = static::getFieldName();

        return parent::make($field)
            ->label($label ?? flexiblePagesTrans('form_component.is_undeletable_lbl'))
            ->hint(flexiblePagesTrans('form_component.is_undeletable_helper'))
            ->hintIcon('heroicon-o-question-mark-circle')
            ->visible(function (Page $livewire, ?Model $record = null) {
                // Get the model class from record or livewire context
                $modelClass = $record ? $record::class : $livewire->getModel();

                if (! FilamentFlexibleContentBlockPages::config()->isUndeletableEnabled($modelClass)) {
                    return false;
                }

                // Always visible on create pages (when $record is null)
                if ($record === null) {
                    return true;
                }

                // On edit pages, check if the user has the gate permission
                $gate = FilamentFlexibleContentBlockPages::config()->getUndeletableGate($modelClass);

                // If no gate set, no authorisation is wanted
                return (!$gate) || Gate::allows($gate, $record);
            });
    }

    public static function getFieldName(): string
    {
        return self::FIELD;
    }
}
