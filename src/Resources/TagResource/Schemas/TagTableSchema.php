<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\TagResource\Schemas;

use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Statikbe\FilamentFlexibleContentBlocks\FilamentFlexibleBlocksConfig;

class TagTableSchema
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(flexiblePagesTrans('form_component.name_lbl')),
                TextColumn::make('tagType.name')
                    ->label(flexiblePagesTrans('tags.tag_type_lbl'))
                    ->badge(),
                TextColumn::make('created_at')
                    ->label(flexiblePagesTrans('tags.table.created_at_col'))
                    ->dateTime(FilamentFlexibleBlocksConfig::getPublishingDateFormatting())
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(flexiblePagesTrans('tags.table.updated_at_col'))
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