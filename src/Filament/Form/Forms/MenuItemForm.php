<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Filament\Form\Forms;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Statikbe\FilamentFlexibleContentBlockPages\Filament\Form\Fields\Types\AbstractMenuItemType;
use Statikbe\FilamentFlexibleContentBlockPages\Filament\Form\Fields\Types\LinkableMenuItemType;
use Statikbe\FilamentFlexibleContentBlockPages\Filament\Form\Fields\Types\RouteMenuItemType;
use Statikbe\FilamentFlexibleContentBlockPages\Filament\Form\Fields\Types\UrlMenuItemType;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Contracts\HasMenuLabel;
use Statikbe\FilamentFlexibleContentBlocks\FilamentFlexibleBlocksConfig;

class MenuItemForm
{
    const FIELD_LINK_TYPE = 'link_type';

    const FIELD_LINKABLE_ID = 'linkable_id';

    const FIELD_URL = 'url';

    const FIELD_ROUTE = 'route';

    const FIELD_LABEL = 'label';

    const FIELD_USE_MODEL_TITLE = 'use_model_title';

    const FIELD_TARGET = 'target';

    const FIELD_ICON = 'icon';

    const FIELD_IS_VISIBLE = 'is_visible';

    protected static ?array $types = null;

    public static function getSchema(): array
    {
        return [
            Grid::make(2)->schema([
                // Basic Information Section
                Grid::make(1)->schema([
                    static::getLinkTypeField(),
                    static::getLabelField(),
                    static::getUseModelTitleField(),
                ])->columnSpan(1),

                // Link Configuration Section
                Grid::make(1)->schema([
                    static::getLinkableField(),
                    static::getUrlField(),
                    static::getRouteField(),
                ])->columnSpan(1),

                // Additional Options Section
                Grid::make(2)->schema([
                    static::getTargetField(),
                    static::getIconField(),
                    static::getVisibilityField(),
                ])->columnSpan(2),

                // Hidden fields for nested set
                Hidden::make('menu_id'),
                Hidden::make('parent_id'),
                Hidden::make('_lft'),
                Hidden::make('_rgt'),
            ]),
        ];
    }

    protected static function getLinkTypeField(): Select
    {
        return Select::make(static::FIELD_LINK_TYPE)
            ->label(flexiblePagesTrans('menu_items.form.link_type_lbl'))
            ->options(static::getLinkTypeOptions())
            ->required()
            ->live()
            ->helperText(flexiblePagesTrans('menu_items.form.link_type_help'));
    }

    protected static function getLabelField(): TextInput
    {
        return TextInput::make(static::FIELD_LABEL)
            ->label(flexiblePagesTrans('menu_items.form.label_lbl'))
            ->required(fn (Get $get): bool => ! $get(static::FIELD_USE_MODEL_TITLE))
            ->visible(fn (Get $get): bool => ! $get(static::FIELD_USE_MODEL_TITLE))
            ->maxLength(255)
            ->helperText(flexiblePagesTrans('menu_items.form.label_help'))
            ->live();
    }

    protected static function getUseModelTitleField(): Toggle
    {
        return Toggle::make(static::FIELD_USE_MODEL_TITLE)
            ->label(flexiblePagesTrans('menu_items.form.use_model_title_lbl'))
            ->helperText(flexiblePagesTrans('menu_items.form.use_model_title_help'))
            ->visible(fn (Get $get): bool => static::isModelType($get(static::FIELD_LINK_TYPE)))
            ->live();
    }

    protected static function getLinkableField(): Select
    {
        return Select::make(static::FIELD_LINKABLE_ID)
            ->label(flexiblePagesTrans('menu_items.form.linkable_item_lbl'))
            ->searchable()
            ->getSearchResultsUsing(function (string $search, Get $get): array {
                $linkType = $get(static::FIELD_LINK_TYPE);
                $type = static::getTypeByAlias($linkType);

                if ($type && $type->isModelType()) {
                    $modelClass = $type->getModel();

                    // Use the model's searchForMenuItems scope if it implements HasMenuLabel
                    if (is_subclass_of($modelClass, HasMenuLabel::class)) {
                        $results = $modelClass::searchForMenuItems($search)
                            ->limit(50)
                            ->get();

                        return $results->mapWithKeys(function ($record) {
                            return [$record->getKey() => $record->getMenuLabel()];
                        })->toArray();
                    }
                }

                return [];
            })
            ->getOptionLabelUsing(function ($value, Get $get): ?string {
                $linkType = $get(static::FIELD_LINK_TYPE);
                $type = static::getTypeByAlias($linkType);

                if ($type && $type->isModelType() && $value) {
                    $record = app($type->getModel())::find($value);
                    if ($record && $record instanceof HasMenuLabel) {
                        return $record->getMenuLabel();
                    }
                }

                return null;
            })
            ->required()
            ->visible(fn (Get $get): bool => static::isModelType($get(static::FIELD_LINK_TYPE)))
            ->helperText(function (Get $get): string {
                $linkType = $get(static::FIELD_LINK_TYPE);
                $type = static::getTypeByAlias($linkType);

                if ($type && $type->isModelType()) {
                    return flexiblePagesTrans('menu_items.form.linkable_help', [
                        'model' => class_basename($type->getModel()),
                    ]);
                }

                return '';
            });
    }

    protected static function getUrlField(): TextInput
    {
        return TextInput::make(static::FIELD_URL)
            ->label(flexiblePagesTrans('menu_items.form.url_lbl'))
            ->url()
            ->required()
            ->visible(fn (Get $get): bool => static::isUrlType($get(static::FIELD_LINK_TYPE)))
            ->helperText(flexiblePagesTrans('menu_items.form.url_help'));
    }

    protected static function getRouteField(): Select
    {
        return Select::make(static::FIELD_ROUTE)
            ->label(flexiblePagesTrans('menu_items.form.route_lbl'))
            ->options(FilamentFlexibleBlocksConfig::getLinkRoutes())
            ->searchable()
            ->required()
            ->visible(fn (Get $get): bool => static::isRouteType($get(static::FIELD_LINK_TYPE)))
            ->helperText(flexiblePagesTrans('menu_items.form.route_help'));
    }

    protected static function getTargetField(): Select
    {
        return Select::make(static::FIELD_TARGET)
            ->label(flexiblePagesTrans('menu_items.form.target_lbl'))
            ->options([
                '_self' => flexiblePagesTrans('menu_items.form.target_self'),
                '_blank' => flexiblePagesTrans('menu_items.form.target_blank'),
            ])
            ->default('_self')
            ->helperText(flexiblePagesTrans('menu_items.form.target_help'));
    }

    protected static function getIconField(): TextInput
    {
        return TextInput::make(static::FIELD_ICON)
            ->label(flexiblePagesTrans('menu_items.form.icon_lbl'))
            ->helperText(flexiblePagesTrans('menu_items.form.icon_help'));
    }

    protected static function getVisibilityField(): Toggle
    {
        return Toggle::make(static::FIELD_IS_VISIBLE)
            ->label(flexiblePagesTrans('menu_items.form.is_visible_lbl'))
            ->default(true)
            ->helperText(flexiblePagesTrans('menu_items.form.is_visible_help'));
    }

    protected static function getLinkTypeOptions(): array
    {
        $options = [];

        foreach (static::getTypes() as $type) {
            $options[$type->getAlias()] = static::getTypeLabel($type);
        }

        return $options;
    }

    protected static function getTypeLabel(AbstractMenuItemType $type): string
    {
        if ($type->isUrlType()) {
            return flexiblePagesTrans('menu_items.form.types.url');
        }

        if ($type->isRouteType()) {
            return flexiblePagesTrans('menu_items.form.types.route');
        }

        return flexiblePagesTrans('menu_items.form.types.model', [
            'model' => class_basename($type->getModel()),
        ]);
    }

    protected static function getTypes(): array
    {
        if (static::$types === null) {
            static::$types = [
                new UrlMenuItemType,
                new RouteMenuItemType,
            ];

            // Add configured linkable models from config
            $configuredModels = config('filament-flexible-content-block-pages.menu.linkable_models', []);

            foreach ($configuredModels as $modelClass) {
                if (is_string($modelClass) && is_subclass_of($modelClass, HasMenuLabel::class)) {
                    static::$types[] = new LinkableMenuItemType($modelClass);
                }
            }
        }

        return static::$types;
    }

    protected static function getTypeByAlias(?string $alias): ?AbstractMenuItemType
    {
        if ($alias === null) {
            return null;
        }

        foreach (static::getTypes() as $type) {
            if ($type->getAlias() === $alias) {
                return $type;
            }
        }

        return null;
    }

    protected static function isModelType(?string $linkType): bool
    {
        $type = static::getTypeByAlias($linkType);

        return $type ? $type->isModelType() : false;
    }

    protected static function isUrlType(?string $linkType): bool
    {
        $type = static::getTypeByAlias($linkType);

        return $type ? $type->isUrlType() : false;
    }

    protected static function isRouteType(?string $linkType): bool
    {
        $type = static::getTypeByAlias($linkType);

        return $type ? $type->isRouteType() : false;
    }
}
