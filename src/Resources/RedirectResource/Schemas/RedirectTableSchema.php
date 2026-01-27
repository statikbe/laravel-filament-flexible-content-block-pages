<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\RedirectResource\Schemas;

use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Statikbe\FilamentFlexibleContentBlocks\FilamentFlexibleBlocksConfig;

class RedirectTableSchema
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('old_url')
                    ->label(flexiblePagesTrans('redirects.redirect_old_url'))
                    ->searchable(),
                TextColumn::make('new_url')
                    ->label(flexiblePagesTrans('redirects.redirect_new_url'))
                    ->searchable(),
                TextColumn::make('status_code')
                    ->label(flexiblePagesTrans('redirects.redirect_status_code')),
                TextColumn::make('created_at')
                    ->label(flexiblePagesTrans('redirects.table.created_at_col'))
                    ->dateTime(FilamentFlexibleBlocksConfig::getPublishingDateFormatting())
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(flexiblePagesTrans('redirects.table.updated_at_col'))
                    ->dateTime(FilamentFlexibleBlocksConfig::getPublishingDateFormatting())
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
