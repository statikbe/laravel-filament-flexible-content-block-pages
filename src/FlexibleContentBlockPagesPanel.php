<?php

namespace Statikbe\FilamentFlexibleContentBlockPages;

use Filament\Panel;
use Filament\PanelProvider;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;

class FlexibleContentBlockPagesPanel extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('filament-flexible-content-block-pages')
            ->path(FilamentFlexibleContentBlockPages::config()->getPanelPath())
            ->plugin(FlexibleContentBlockPagesPlugin::make());
    }
}
