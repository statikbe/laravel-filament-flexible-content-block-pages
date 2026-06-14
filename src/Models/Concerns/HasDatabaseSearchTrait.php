<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait HasDatabaseSearchTrait
{
    /**
     * The translatable attributes that scopeSearch() matches against.
     * Override this on the model to customise which fields are searched.
     *
     * @return list<string>
     */
    public function getSearchableAttributes(): array
    {
        return [
            'title',
            'intro',
            'content_blocks',
            'seo_title',
            'seo_description',
            'overview_title',
            'overview_description',
        ];
    }

    /**
     * Case-insensitive search across the translatable attributes for the current locale.
     *
     * Works on MySQL, PostgreSQL and SQLite: the query grammar builds the JSON column
     * expression for the active driver, which is then lowercased with the standard SQL
     * LOWER() function so matching is case-insensitive regardless of column collation.
     */
    public function scopeSearch(Builder $query, string $search): void
    {
        $search = trim($search);

        $query->when($search !== '', function (Builder $query) use ($search): void {
            $locale = app()->getLocale();
            $grammar = $query->getQuery()->getGrammar();

            // Escape LIKE wildcards so they are matched literally. "!" is used as the
            // escape character because, unlike "\", it has no special meaning inside a
            // string literal on any of the supported database drivers.
            $escaped = str_replace(['!', '%', '_'], ['!!', '!%', '!_'], mb_strtolower($search));
            $term = "%{$escaped}%";

            $query->where(function (Builder $query) use ($grammar, $locale, $term): void {
                foreach ($this->getSearchableAttributes() as $attribute) {
                    $column = $grammar->wrap("{$attribute}->{$locale}");
                    $query->orWhereRaw("LOWER({$column}) LIKE ? ESCAPE '!'", [$term]);
                }
            });
        });
    }
}
