@php
    use \Statikbe\FilamentFlexibleContentBlockPages\Models\Settings;
    use \Statikbe\FilamentFlexibleContentBlockPages\Models\Page;

    /* @var Page $page */
@endphp

<x-flexible-pages-base-layout title="{{ $page->title }}" wide="true">
    <header>
        <x-flexible-pages-language-switch/>
    </header>

    <main class="prose-headings:font-base">

        <x-flexible-hero :page="$page"/>

        <x-flexible-content-blocks :page="$page"/>

    </main>

    <footer>
        <div>{{flexiblePagesSetting(Settings::SETTING_FOOTER_COPYRIGHT)}}</div>
    </footer>
</x-flexible-pages-base-layout>
