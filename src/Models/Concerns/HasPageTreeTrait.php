<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use SolutionForest\FilamentTree\Concern\ModelTree;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\HasParent;

/**
 * @mixin HasParent
 * @mixin Model
 *
 * @property int|null $parent_id
 */
trait HasPageTreeTrait
{
    use ModelTree;

    public function initializeHasPageTreeTrait(): void
    {
        $this->mergeFillable(['parent_id', 'order']);
        $this->mergeCasts(['parent_id' => 'integer', 'order' => 'integer']);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    public function hasParent(): bool
    {
        return ! $this->isRoot();
    }

    public function isParentOf(HasParent $child): bool
    {
        return property_exists($child, 'parent_id') && $this->id === $child->parent_id;
    }
}
