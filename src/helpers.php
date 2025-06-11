<?php

function flexiblePagesTrans(string $translationKey, array $replace = [], ?string $locale = null): string
{
    return trans("filament-flexible-content-block-pages.$translationKey", $replace, $locale);
}
