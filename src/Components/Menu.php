<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Components;

use Illuminate\View\Component;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Contracts\HasMenuLabel;

class Menu extends Component
{
    public $menu;
    public $items;
    public $locale;
    public string $style;

    public function __construct(
        string $code,
        ?string $style = null,
        ?string $locale = null
    ) {
        $this->menu = $this->getMenuByCode($code);
        $this->locale = $locale ?: app()->getLocale();
        
        // Determine the style to use with proper fallback chain
        if ($style) {
            $this->style = $style;
        } elseif ($this->menu) {
            $this->style = $this->menu->getEffectiveStyle();
        } else {
            $this->style = FilamentFlexibleContentBlockPages::config()->getDefaultMenuStyle();
        }
        
        $this->items = $this->menu ? $this->getMenuItems($this->menu, $this->locale) : [];
    }

    public function render()
    {
        $theme = FilamentFlexibleContentBlockPages::config()->getMenuTheme();
        $template = "filament-flexible-content-block-pages::components.{$theme}.menu.{$this->style}";
        
        // Check if the themed style template exists, otherwise try default style in theme
        if (view()->exists($template)) {
            return view($template);
        }
        
        $defaultTemplate = "filament-flexible-content-block-pages::components.{$theme}.menu.default";
        if (view()->exists($defaultTemplate)) {
            return view($defaultTemplate);
        }
        
        // Final fallback to tailwind theme default
        return view('filament-flexible-content-block-pages::components.tailwind.menu.default');
    }

    protected function getMenuByCode(string $code)
    {
        $menuModel = FilamentFlexibleContentBlockPages::config()->getMenuModel();
        
        return $menuModel::getByCode($code);
    }

    protected function getMenuItems($menu, ?string $locale = null): array
    {
        if (!$menu) {
            return [];
        }

        $items = $menu->menuItems()
            ->with('linkable')
            ->orderBy('_lft')
            ->get();

        return $this->buildMenuTree($items->toArray(), $locale);
    }

    protected function buildMenuTree(array $items, ?string $locale = null, $parentId = null): array
    {
        $tree = [];
        
        foreach ($items as $item) {
            if ($item['parent_id'] == $parentId) {
                $processedItem = $this->processMenuItem($item, $locale);
                $children = $this->buildMenuTree($items, $locale, $item['id']);
                
                if (!empty($children)) {
                    $processedItem['children'] = $children;
                    $processedItem['has_children'] = true;
                } else {
                    $processedItem['has_children'] = false;
                }
                
                $tree[] = $processedItem;
            }
        }
        
        return $tree;
    }

    protected function processMenuItem(array $item, ?string $locale = null): array
    {
        $locale = $locale ?: app()->getLocale();
        
        // Get the display label
        $label = $item['label'][$locale] ?? $item['label'][config('app.fallback_locale', 'en')] ?? '';
        
        // If use_model_title is true and we have a linkable model, use its label
        if ($item['use_model_title'] && $item['linkable']) {
            $linkableModel = $this->getLinkableModel($item['linkable_type'], $item['linkable_id']);
            if ($linkableModel && $linkableModel instanceof HasMenuLabel) {
                $label = $linkableModel->getMenuLabel($locale);
            }
        }

        // Generate the URL
        $url = $this->generateMenuItemUrl($item);
        
        // Check if current page matches this menu item
        $isCurrent = $this->isCurrentMenuItem($item);
        
        return [
            'id' => $item['id'],
            'label' => $label,
            'url' => $url,
            'target' => $item['target'] ?? '_self',
            'link_type' => $item['link_type'],
            'is_current' => $isCurrent,
            'is_active' => $isCurrent,
            'css_classes' => $item['css_classes'] ?? '',
            'data_attributes' => $item['data_attributes'] ?? [],
        ];
    }

    protected function getLinkableModel(string $type, int $id)
    {
        try {
            $morphMap = FilamentFlexibleContentBlockPages::config()->getMorphMap();
            
            if (!isset($morphMap[$type])) {
                return null;
            }
            
            $modelClass = $morphMap[$type];
            return $modelClass::find($id);
            
        } catch (\Exception $e) {
            return null;
        }
    }

    protected function generateMenuItemUrl(array $item): string
    {
        switch ($item['link_type']) {
            case 'url':
                return $item['url'] ?? '#';
                
            case 'route':
                try {
                    $routeName = $item['route'] ?? '';
                    if (empty($routeName)) {
                        return '#';
                    }
                    
                    $parameters = $item['route_parameters'] ?? [];
                    return route($routeName, $parameters);
                    
                } catch (\Exception $e) {
                    return '#';
                }
                
            case 'model':
                $linkableModel = $this->getLinkableModel($item['linkable_type'], $item['linkable_id']);
                if ($linkableModel && method_exists($linkableModel, 'getUrl')) {
                    return $linkableModel->getUrl();
                }
                return '#';
                
            default:
                return '#';
        }
    }

    protected function isCurrentMenuItem(array $item): bool
    {
        $currentUrl = request()->url();
        $itemUrl = $this->generateMenuItemUrl($item);
        
        // Remove trailing slashes for comparison
        $currentUrl = rtrim($currentUrl, '/');
        $itemUrl = rtrim($itemUrl, '/');
        
        if ($itemUrl === '#' || empty($itemUrl)) {
            return false;
        }
        
        return $currentUrl === $itemUrl;
    }

    public function hasActiveChildren(array $item): bool
    {
        if (empty($item['children'])) {
            return false;
        }
        
        foreach ($item['children'] as $child) {
            if ($child['is_current'] || $this->hasActiveChildren($child)) {
                return true;
            }
        }
        
        return false;
    }
}