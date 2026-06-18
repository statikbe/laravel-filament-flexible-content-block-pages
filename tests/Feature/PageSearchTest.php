<?php

use Statikbe\FilamentFlexibleContentBlockPages\Models\Page;

beforeEach(function () {
    app()->setLocale('en');
});

/** @return list<string> */
function searchTitles(string $term): array
{
    return Page::search($term)->get()->map(fn (Page $page) => $page->title)->all();
}

it('finds pages by title case-insensitively', function () {
    Page::factory()->create(['title' => ['en' => 'The Quick Brown Fox', 'es' => 'El Zorro']]);
    Page::factory()->create(['title' => ['en' => 'Something Else', 'es' => 'Otra Cosa']]);

    expect(searchTitles('quick brown'))->toBe(['The Quick Brown Fox'])
        ->and(searchTitles('QUICK BROWN'))->toBe(['The Quick Brown Fox']);
});

it('searches across multiple translatable attributes', function () {
    Page::factory()->create([
        'title' => ['en' => 'Homepage', 'es' => 'Inicio'],
        'intro' => ['en' => 'A unique introduction paragraph', 'es' => 'Intro'],
    ]);

    expect(Page::search('unique introduction')->count())->toBe(1);
});

it('only matches the current locale', function () {
    Page::factory()->create(['title' => ['en' => 'Contact us', 'es' => 'Contacto especial']]);

    app()->setLocale('en');
    expect(Page::search('especial')->count())->toBe(0);

    app()->setLocale('es');
    expect(Page::search('especial')->count())->toBe(1);
});

it('treats LIKE wildcards in the search term as literal characters', function () {
    Page::factory()->create(['title' => ['en' => 'Save 50% today', 'es' => 'Ahorra']]);
    Page::factory()->create(['title' => ['en' => 'Order 5000 units', 'es' => 'Pedido']]);

    // Without escaping, "50%" would match both via the % wildcard.
    expect(searchTitles('50%'))->toBe(['Save 50% today']);
});

it('treats the underscore wildcard as a literal character', function () {
    Page::factory()->create(['title' => ['en' => 'report_2024 final', 'es' => 'Informe']]);
    Page::factory()->create(['title' => ['en' => 'reportX2024 draft', 'es' => 'Borrador']]);

    expect(searchTitles('report_2024'))->toBe(['report_2024 final']);
});

it('returns all records for an empty or whitespace-only search', function () {
    Page::factory()->count(3)->create();

    expect(Page::search('')->count())->toBe(3)
        ->and(Page::search('   ')->count())->toBe(3);
});
