<?php

use Statikbe\FilamentFlexibleContentBlockPages\Models\Menu;
use Statikbe\FilamentFlexibleContentBlockPages\Models\MenuItem;

it('can create a menu with code', function () {
    $menu = Menu::factory()->create([
        'name' => 'Main Navigation',
        'code' => 'main-nav',
        'style' => 'horizontal',
    ]);

    expect($menu)->toBeInstanceOf(Menu::class)
        ->and($menu->name)->toBe('Main Navigation')
        ->and($menu->code)->toBe('main-nav')
        ->and($menu->style)->toBe('horizontal');
});

it('retrieves menu by code', function () {
    $menu = Menu::factory()->main()->create();

    $foundMenu = Menu::code('main')->first();

    expect($foundMenu)->not->toBeNull()
        ->and($foundMenu->id)->toBe($menu->id);
});

it('returns top-level menu items only via menuItems relationship', function () {
    $menu = Menu::factory()->create();
    $topLevelItem = MenuItem::factory()->forMenu($menu)->create(['parent_id' => config('filament-tree.default_parent_id', -1)]);
    $childItem = MenuItem::factory()->childOf($topLevelItem)->create();

    $menuItems = $menu->menuItems;

    expect($menuItems)->toHaveCount(1)
        ->and($menuItems->first()->id)->toBe($topLevelItem->id);
});

it('returns all menu items via allMenuItems relationship', function () {
    $menu = Menu::factory()->create();
    $topLevelItem = MenuItem::factory()->forMenu($menu)->create(['parent_id' => config('filament-tree.default_parent_id', -1)]);
    $childItem = MenuItem::factory()->childOf($topLevelItem)->create();

    $allMenuItems = $menu->allMenuItems;

    expect($allMenuItems)->toHaveCount(2);
});

it('returns effective style when set', function () {
    $menu = Menu::factory()->withStyle('horizontal')->create();

    expect($menu->getEffectiveStyle())->toBe('horizontal');
});

it('returns default style when not set', function () {
    $menu = Menu::factory()->create(['style' => '']);

    expect($menu->getEffectiveStyle())->toBe('default');
});

it('returns effective max depth when set', function () {
    $menu = Menu::factory()->withMaxDepth(3)->create();

    expect($menu->getEffectiveMaxDepth())->toBe(3);
});

it('returns default max depth when not set', function () {
    $menu = Menu::factory()->create(['max_depth' => null]);

    // Default from config is 2
    expect($menu->getEffectiveMaxDepth())->toBe(2);
});

it('returns display title in correct locale', function () {
    $menu = Menu::factory()->create([
        'title' => ['en' => 'Navigation', 'es' => 'Navegacion'],
    ]);

    app()->setLocale('en');
    expect($menu->getDisplayTitle())->toBe('Navigation');

    app()->setLocale('es');
    expect($menu->getDisplayTitle('es'))->toBe('Navegacion');
});

it('returns null for display title when not set', function () {
    $menu = Menu::factory()->create(['title' => null]);

    expect($menu->getDisplayTitle())->toBeNull();
});

it('returns correct morph class', function () {
    $menu = Menu::factory()->create();

    expect($menu->getMorphClass())->toBe('filament-flexible-content-block-pages::menu');
});
