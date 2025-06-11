<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\PageResource\Pages;

use Statikbe\FilamentFlexibleContentBlockPages\Resources\PageResource;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Pages\CreateRecord\Concerns\TranslatableWithMedia;

class CreatePage extends CreateRecord
{
    use TranslatableWithMedia;

    protected static string $resource = PageResource::class;
}
