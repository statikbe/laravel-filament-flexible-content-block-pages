<?php

namespace Statikbe\FilamentFlexibleContentBlockPages;

use Filament\Panel;
use Filament\PanelProvider;
use Filament\SpatieLaravelTranslatablePlugin;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;

class FlexibleContentBlockPagesPanel extends PanelProvider
{
    const ID = 'filament-flexible-content-block-pages';

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id(static::ID)
            ->path(FilamentFlexibleContentBlockPages::config()->getPanelPath())
            ->middleware(FilamentFlexibleContentBlockPages::config()->getPanelMiddleware())
            ->authMiddleware(FilamentFlexibleContentBlockPages::config()->getPanelAuthMiddleware())
            ->plugin(FlexibleContentBlockPagesPlugin::make())
            ->plugin(SpatieLaravelTranslatablePlugin::make()
                ->defaultLocales(FilamentFlexibleContentBlockPages::config()->getSupportedLocales()));
    }
}
