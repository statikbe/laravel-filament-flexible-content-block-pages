<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Actions;

use Filament\Actions\Action;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\Linkable;

class ViewAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'view';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(trans('filament-flexible-content-blocks::filament-flexible-content-blocks.table_action.view_page_lbl'));

        $this->color('gray');

        $this->icon('heroicon-s-eye');

        $this->url(function (Linkable $record): string {
            $livewire = $this->getLivewire();
            $locale = app()->getLocale();

            if (property_exists($livewire, 'activeLocale') && $livewire->activeLocale) {
                $locale = $livewire->activeLocale;
            }

            return $record->getPreviewUrl($locale);
        })
            ->openUrlInNewTab();
    }
}
