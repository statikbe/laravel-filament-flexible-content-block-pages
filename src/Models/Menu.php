<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlocks\Models\Concerns\HasCodeTrait;
use Statikbe\FilamentFlexibleContentBlocks\Models\Contracts\HasCode;

class Menu extends Model implements HasCode
{
    use HasFactory;
    use HasCodeTrait;

    protected $fillable = [
        'name',
        'code',
        'description',
        'style',
    ];

    public function getTable()
    {
        return FilamentFlexibleContentBlockPages::config()->getMenusTable();
    }

    public function menuItems(): HasMany
    {
        return $this->hasMany(FilamentFlexibleContentBlockPages::config()->getMenuItemModel()::class)
            ->whereNull('parent_id')
            ->orderBy('_lft');
    }

    public function allMenuItems(): HasMany
    {
        return $this->hasMany(FilamentFlexibleContentBlockPages::config()->getMenuItemModel()::class)
            ->orderBy('_lft');
    }

    public function getMorphClass()
    {
        return 'filament-flexible-content-block-pages::menu';
    }

    public function getEffectiveStyle(): string
    {
        // Return the menu's style if set, otherwise fall back to config default
        if (!empty($this->style)) {
            $availableStyles = FilamentFlexibleContentBlockPages::config()->getMenuStyles();
            if (in_array($this->style, $availableStyles)) {
                return $this->style;
            }
        }
        
        return FilamentFlexibleContentBlockPages::config()->getDefaultMenuStyle();
    }

}