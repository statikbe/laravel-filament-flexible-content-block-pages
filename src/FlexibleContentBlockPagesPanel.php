<?php

namespace Statikbe\FilamentFlexibleContentBlockPages;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\SpatieLaravelTranslatablePlugin;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlocks\Facades\FilamentFlexibleContentBlocks;

class FlexibleContentBlockPagesPanel extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('filament-flexible-content-block-pages')
            ->path(FilamentFlexibleContentBlockPages::config()->getPanelPath())
            ->plugin(FlexibleContentBlockPagesPlugin::make())
            ->plugin(SpatieLaravelTranslatablePlugin::make()
                ->defaultLocales(FilamentFlexibleContentBlockPages::config()->getSupportedLocales()));
    }
}
