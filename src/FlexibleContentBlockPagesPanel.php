<?php

namespace Statikbe\FilamentFlexibleContentBlockPages;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\SpatieLaravelTranslatablePlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
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
