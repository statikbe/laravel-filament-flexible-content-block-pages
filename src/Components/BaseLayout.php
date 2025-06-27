<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Components;

use Illuminate\View\Component;

class BaseLayout extends Component
{
    public function __construct(public string $title){}

    public function render()
    {
        return view('filament-flexible-content-block-pages::components.layouts.base');
    }
}
