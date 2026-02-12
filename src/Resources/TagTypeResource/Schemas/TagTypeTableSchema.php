<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\TagTypeResource\Schemas;

use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Guava\IconPicker\Tables\Columns\IconColumn;
use Statikbe\FilamentFlexibleContentBlocks\FilamentFlexibleBlocksConfig;

class TagTypeTableSchema
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(flexiblePagesTrans('form_component.name_lbl')),
                ColorColumn::make('colour')
                    ->label(flexiblePagesTrans('form_component.colour_lbl')),
                ToggleColumn::make('is_default_type')
                    ->label(flexiblePagesTrans('tag_types.tag_type_is_default_type_lbl')),
                IconColumn::make('icon')
                    ->label(flexiblePagesTrans('form_component.icon_lbl')),
                TextColumn::make('created_at')
                    ->label(flexiblePagesTrans('menu_items.table.created_at_col'))
                    ->dateTime(FilamentFlexibleBlocksConfig::getPublishingDateFormatting())
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(flexiblePagesTrans('menu_items.table.updated_at_col'))
                    ->dateTime(FilamentFlexibleBlocksConfig::getPublishingDateFormatting())
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
