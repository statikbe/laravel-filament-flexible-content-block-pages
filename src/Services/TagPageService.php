<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Services;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Tag;

class TagPageService
{
    /**
     * Get paginated content with actual model instances for a given tag.
     */
    public function getTaggedContent(Tag $tag, ?int $perPage = null): LengthAwarePaginator
    {
        $perPage = $perPage ?? FilamentFlexibleContentBlockPages::config()->getTagPagePaginationItemCount();

        // Step 1: Get IDs and types efficiently with union query
        $unionResults = $this->getUnionQueryResults($tag, $perPage);

        if ($unionResults->isEmpty()) {
            return $unionResults;
        }

        // Step 2: Group by model type for efficient loading
        $groupedIds = collect($unionResults->items())->groupBy('model_class');

        // Step 3: Load actual models in batches
        $models = $this->hydrateModels($groupedIds, $tag);

        // Return as paginator with actual models
        return new LengthAwarePaginator(
            $models,
            $unionResults->total(),
            $unionResults->perPage(),
            $unionResults->currentPage(),
            [
                'path' => $unionResults->path(),
                'pageName' => $unionResults->getPageName(),
            ]
        );
    }

    /**
     * Get the union query results with IDs and model info.
     */
    private function getUnionQueryResults(Tag $tag, int $perPage): LengthAwarePaginator
    {
        $queries = [];
        $enabledModels = $this->getEnabledModels();

        foreach ($enabledModels as $modelClass) {
            /** @var class-string $modelClass */
            $query = $modelClass::withAnyTagsOfAnyType([$tag->name])
                ->select([
                    'id',
                    'publishing_begins_at',
                    'created_at', // fallback for sorting
                    DB::raw("'{$modelClass}' as model_class"),
                ]);

            // Add published scope if available
            if (method_exists($modelClass, 'scopePublished')) {
                $query->published();
            }

            $queries[] = $query;
        }

        if (empty($queries)) {
            return new LengthAwarePaginator([], 0, $perPage);
        }

        // Union all queries
        $unionQuery = $queries[0];
        for ($i = 1; $i < count($queries); $i++) {
            $unionQuery->union($queries[$i]);
        }

        // Order and paginate the unified result
        return DB::table(DB::raw("({$unionQuery->toSql()}) as tagged_content"))
            ->mergeBindings($unionQuery->getQuery())
            ->orderByRaw('COALESCE(publishing_begins_at, created_at) DESC')
            ->paginate($perPage);
    }

    /**
     * Hydrate actual model instances from the union query results.
     */
    private function hydrateModels(Collection $groupedIds, Tag $tag): Collection
    {
        $models = collect();

        foreach ($groupedIds as $modelClass => $items) {
            $ids = $items->pluck('id');

            // Load actual models with eager loading
            /** @var class-string $modelClass */
            $modelInstances = $modelClass::query()
                ->whereIn('id', $ids)
                ->when(method_exists($modelClass, 'scopePublished'), function ($query) {
                    return $query->published();
                })
                ->with('tags') // Avoid N+1 on tag relationships
                ->get()
                ->keyBy('id');

            // Maintain original order from union query
            foreach ($items as $item) {
                if ($modelInstances->has($item->id)) {
                    $model = $modelInstances->get($item->id);
                    $models->push($model);
                }
            }
        }

        return $models;
    }

    /**
     * Get the configured models that can be included in tag pages.
     */
    private function getEnabledModels(): array
    {
        $enabledModels = FilamentFlexibleContentBlockPages::config()->getTagPageEnabledModels();
        $validModels = [];

        foreach ($enabledModels as $modelClass) {
            if (class_exists($modelClass) && $this->isTaggable($modelClass)) {
                $validModels[] = $modelClass;
            }
        }

        return $validModels;
    }

    /**
     * Check if a model class is taggable.
     */
    private function isTaggable(string $modelClass): bool
    {
        return in_array(\Spatie\Tags\HasTags::class, class_uses_recursive($modelClass));
    }

    /**
     * Get content counts by model type for a tag.
     */
    public function getContentCounts(Tag $tag): array
    {
        $counts = [];
        $enabledModels = $this->getEnabledModels();

        foreach ($enabledModels as $modelClass) {
            /** @var class-string $modelClass */
            $count = $modelClass::withAnyTags([$tag->name])
                ->when(method_exists($modelClass, 'scopePublished'), function ($query) {
                    return $query->published();
                })
                ->count();

            if ($count > 0) {
                /** @var class-string<\Filament\Resources\Resource>|null $resourceClass */
                $resourceClass = Filament::getModelResource($modelClass);
                $label = $resourceClass ? ($count === 1 ? $resourceClass::getModelLabel() : $resourceClass::getPluralLabel()) : class_basename($modelClass);
                $counts[$label] = $count;
            }
        }

        return $counts;
    }
}
