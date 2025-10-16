<?php

namespace Statikbe\FilamentFlexibleContentBlockPages;

use Filament\Facades\Filament;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\SpatieLaravelTranslatablePlugin;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;

class FlexibleContentBlockPagesPanel extends PanelProvider
{
    const ID = 'filament-flexible-content-block-pages';

    public function panel(Panel $panel): Panel
    {
        return static::configurePanel($panel, static::ID);
    }

    /**
     * This function can be used to create a custom panel. Just create a new PanelProvider sub class and implement the
     * `panel` function. You can then call `configurePanel` statically.
     *
     * Another option if you are not already extending from another class, is to extend this class and overwrite the functions.
     */
    public static function configurePanel(Panel $panel, ?string $id = null): Panel
    {
        return $panel
            ->id($id ?? static::ID)
            ->path(FilamentFlexibleContentBlockPages::config()->getPanelPath())
            ->middleware(FilamentFlexibleContentBlockPages::config()->getPanelMiddleware())
            ->authMiddleware(FilamentFlexibleContentBlockPages::config()->getPanelAuthMiddleware())
            ->resources(FilamentFlexibleContentBlockPages::config()->getResources())
            ->plugin(FlexibleContentBlockPagesPlugin::make())
            ->plugin(SpatieLaravelTranslatablePlugin::make()
                ->defaultLocales(FilamentFlexibleContentBlockPages::config()->getSupportedLocales()))
            ->navigationItems(static::getExtraNavigationItems())
            ->login(fn () => static::getLoginAction());
    }

    /**
     * Implement this function in a sub class, to add nav items.
     */
    public static function getExtraNavigationItems(): array
    {
        return [];
    }

    /**
     * Implements the login screen action that is used by this panel.
     * You can overwrite this in a subclass.
     * We assume in this default implementation that your app used multiple panels and
     * the default panel is used for authentication.
     */
    public static function getLoginAction()
    {
        return redirect()->to(Filament::getDefaultPanel()->getLoginUrl());
    }
}
