<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Models\Concerns;

trait HasTitleMenuLabelTrait
{
    /**
     * Get the display label for menu items.
     * This implementation uses the 'title' field with locale support.
     * Assumes the model uses Spatie Laravel Translatable trait.
     */
    public function getMenuLabel(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();

        // Get translated title (assumes HasTranslations trait is used)
        $title = $this->getTranslation('title', $locale);
        if (empty($title)) {
            // Fallback to the configured fallback locale
            $title = $this->getTranslation('title', config('app.fallback_locale', 'en'));
        }

        return $title ?: 'Untitled';
    }

    /**
     * Scope to search for models that can be used in menu items.
     * This implementation searches in common searchable fields.
     * Assumes the model uses Spatie Laravel Translatable trait.
     */
    public function scopeSearchForMenuItems($query, string $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->where('title', 'like', "%{$search}%");

            // Add additional searchable fields if they exist
            $searchableFields = ['name', 'intro', 'description', 'overview_title'];
            foreach ($searchableFields as $field) {
                if (in_array($field, $this->getFillable()) ||
                    in_array($field, $this->getTranslatableAttributes())) {
                    $query->orWhere($field, 'like', "%{$search}%");
                }
            }
        });
    }
}
