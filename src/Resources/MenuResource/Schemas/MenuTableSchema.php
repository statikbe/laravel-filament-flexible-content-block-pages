<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\MenuResource\Schemas;

use Filament\Actions\Action;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\MenuResource;
use Statikbe\FilamentFlexibleContentBlocks\FilamentFlexibleBlocksConfig;

class MenuTableSchema
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(flexiblePagesTrans('menus.table.name_col'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('code')
                    ->label(flexiblePagesTrans('menus.table.code_col'))
                    ->searchable()
                    ->sortable()
                    ->badge(),

                TextColumn::make('style')
                    ->label(flexiblePagesTrans('menus.table.style_col'))
                    ->formatStateUsing(function (string $state): string {
                        return flexiblePagesTrans("menu.styles.{$state}");
                    })
                    ->badge()
                    ->color('gray')
                    ->visible(fn () => count(FilamentFlexibleContentBlockPages::config()->getMenuStyles()) > 1),

                TextColumn::make('created_at')
                    ->label(flexiblePagesTrans('menus.table.created_at_col'))
                    ->dateTime(FilamentFlexibleBlocksConfig::getPublishingDateFormatting())
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(flexiblePagesTrans('menus.table.updated_at_col'))
                    ->dateTime(FilamentFlexibleBlocksConfig::getPublishingDateFormatting())
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                Action::make('manage_items')
                    ->label(flexiblePagesTrans('menus.actions.manage_items'))
                    ->icon('heroicon-o-bars-3')
                    ->color('secondary')
                    ->url(fn ($record) => MenuResource::getUrl('items', ['record' => $record])),
                EditAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}