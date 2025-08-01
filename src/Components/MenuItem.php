<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Components;

use Illuminate\View\Component;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;

class MenuItem extends Component
{
    public array $item;

    public string $style;

    public function __construct(
        array $item,
        ?string $style = null
    ) {
        $this->item = $item;
        $this->style = $style ?: FilamentFlexibleContentBlockPages::config()->getDefaultMenuStyle();
    }

    public function render()
    {
        $theme = FilamentFlexibleContentBlockPages::config()->getTheme();
        $template = "filament-flexible-content-block-pages::{$theme}.components.menu.{$this->style}-item";

        // Check if the themed style item template exists, otherwise try default item in theme
        if (view()->exists($template)) {
            return view($template);
        }

        $defaultTemplate = "filament-flexible-content-block-pages::{$theme}.components.menu.default-item";
        if (view()->exists($defaultTemplate)) {
            return view($defaultTemplate);
        }

        // Final fallback to tailwind theme default
        return view('filament-flexible-content-block-pages::tailwind.components.menu.default-item');
    }

    public function getDataAttributes(): string
    {
        if (empty($this->item['data_attributes'])) {
            return '';
        }

        $attributes = '';
        foreach ($this->item['data_attributes'] as $key => $value) {
            $attributes .= ' data-'.e($key).'="'.e($value).'"';
        }

        return $attributes;
    }
}
