<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;

/**
 * @property string $code
 * @property string $name
 * @property string $colour
 * @property string $icon
 * @property bool $is_default_type
 * @property bool $has_seo_pages
 */
class TagType extends Model
{
    use HasTranslations;

    const TYPE_DEFAULT = 'default';

    const TYPE_SEO = 'seo';

    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $casts = [
        'is_default_type' => 'boolean',
        'has_seo_pages' => 'boolean',
    ];

    protected $translatable = ['name'];

    protected static function booted(): void
    {
        static::created(function (self $status) {
            static::resetDefaultStatuses($status);
        });

        static::updated(function (self $status) {
            static::resetDefaultStatuses($status);
        });
    }

    protected static function resetDefaultStatuses(self $status): void
    {
        if ($status->is_default_type) {
            DB::table($status->getTable())
                ->whereNot('code', $status->code)
                ->update(['is_default_type' => false]);
        }
    }

    public function scopeDefaultStatus(Builder $query): void
    {
        $query->where('is_default_type', 1);
    }

    public function getTable()
    {
        return FilamentFlexibleContentBlockPages::config()->getTagTypesTable();
    }

    public function tags(): HasMany
    {
        return $this->hasMany(FilamentFlexibleContentBlockPages::config()->getTagModel()::class, 'code', 'type');
    }

    public function scopeCode(Builder $query, string $code): void
    {
        $query->where('code', $code);
    }

    public static function getByCode(string $code): ?self
    {
        return static::code($code)->first();
    }

    public function formatColour(): ?string
    {
        if (! $this->colour) {
            return null;
        }

        if (Str::startsWith($this->colour, ['#', 'rgb(', 'RGB('])) {
            return $this->colour;
        }

        if (strlen($this->colour) === 6) {
            return '#'.$this->colour;
        }

        return $this->colour;
    }

    public function hasSvgIcon(): bool
    {
        return $this->icon && Str::contains(trim($this->icon), ['<svg', '<SVG']);
    }
}
