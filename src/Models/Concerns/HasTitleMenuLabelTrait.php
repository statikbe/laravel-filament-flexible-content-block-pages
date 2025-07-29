<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Models\Concerns;

trait HasTitleMenuLabelTrait
{
    /**
     * Get the display label for menu items.
     * This implementation uses the 'title' field with locale support.
     */
    public function getMenuLabel(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();

        // Check if the model uses translations (like Spatie Laravel Translatable)
        if (method_exists($this, 'getTranslation')) {
            $title = $this->getTranslation('title', $locale);
            if (empty($title)) {
                // Fallback to the configured fallback locale
                $title = $this->getTranslation('title', config('app.fallback_locale', 'en'));
            }

            return $title ?: 'Untitled';
        }

        return $this->title ?: 'Untitled';
    }

    /**
     * Scope to search for models that can be used in menu items.
     * This implementation searches in common searchable fields.
     */
    public function scopeSearchForMenuItems($query, string $search)
    {
        return $query->where(function ($query) use ($search) {
            $query->where('title', 'like', "%{$search}%");

            // Add additional searchable fields if they exist
            $searchableFields = ['name', 'intro', 'description', 'overview_title'];
            foreach ($searchableFields as $field) {
                if (in_array($field, $this->getFillable()) ||
                    (method_exists($this, 'getTranslatableAttributes') &&
                     in_array($field, $this->getTranslatableAttributes()))) {
                    $query->orWhere($field, 'like', "%{$search}%");
                }
            }
        });
    }
}
