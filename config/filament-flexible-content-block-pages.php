<?php

// config for Statikbe/FilamentFlexibleContentBlockPages
return [
    'models' => [
        'page' => \Statikbe\FilamentFlexibleContentBlockPages\Models\Page::class,
    ],

    'table_names' => [
        'pages' => 'pages',
        'authors' => 'users',
    ],

    'resources' => [
        'pages' => \Statikbe\FilamentFlexibleContentBlockPages\Resources\PageResource::class,
    ],

    'panel' => [
        'path' => 'content',
    ],
];
