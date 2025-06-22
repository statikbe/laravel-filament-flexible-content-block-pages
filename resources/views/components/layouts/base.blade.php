<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ \Artesaos\SEOTools\SEOTools::getTitle() ?? flexiblePagesSetting(\Statikbe\FilamentFlexibleContentBlockPages\Models\Settings::SETTING_SITE_TITLE, app()->getLocale(), config('app.name') }}</title>

    {!! \Artesaos\SEOTools\SEOTools::generate() !!}

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body class="font-sans antialiased">
    <div class="min-h-screen">
        <!-- Page Content -->
        {{ $slot }}
    </div>
</body>
</html>
