<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait HasDatabaseSearchTrait
{
    /**
     * Searches in database on different fields with LIKE queries.
     * @param $query
     * @param string $search
     * @return void
     */
    public function scopeSearch(Builder $query, string $search): void
    {
        $search = strtolower($search);
        $query->when($search, function ($query, $search) {
            $locale = app()->getLocale();
            $query->where(function ($query) use ($search, $locale) {
                $query->whereRaw("LOWER(title->>'$.{$locale}') LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("LOWER(intro->>'$.{$locale}') LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("LOWER(content_blocks->>'$.{$locale}') LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("LOWER(seo_title->>'$.{$locale}') LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("LOWER(seo_description->>'$.{$locale}') LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("LOWER(overview_title->>'$.{$locale}') LIKE ?", ["%{$search}%"])
                    ->orWhereRaw("LOWER(overview_description->>'$.{$locale}') LIKE ?", ["%{$search}%"]);
            });
        });
    }
}
