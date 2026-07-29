<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\PageResource\Schemas;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Statikbe\FilamentFlexibleContentBlockPages\Actions\LinkedToMenuItemBulkDeleteAction;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Table\Actions\PublishAction;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Table\Actions\ReplicateAction;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Table\Actions\ViewAction;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Table\Columns\PublishedColumn;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Table\Columns\TitleColumn;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Table\Filters\CodeFilter;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Table\Filters\PublishedFilter;
use Statikbe\FilamentFlexibleContentBlocks\FilamentFlexibleBlocksConfig;

class PageTableSchema
{
    public static function configure(Table $table): Table
    {
        $config = FilamentFlexibleContentBlockPages::config();
        $pageResource = $config->getPageResource();

        return $table
            ->columns([
                TitleColumn::create()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label(flexiblePagesTrans('pages.table.created_at_col'))
                    ->dateTime(FilamentFlexibleBlocksConfig::getPublishingDateFormatting())
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label(flexiblePagesTrans('pages.table.updated_at_col'))
                    ->dateTime(FilamentFlexibleBlocksConfig::getPublishingDateFormatting())
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('code')
                    ->label(flexiblePagesTrans('pages.table.code_col'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                PublishedColumn::create()
                    ->toggleable(),
            ])
            ->filters([
                PublishedFilter::create(),
                CodeFilter::create(),
            ])
            ->recordActions([
                EditAction::make(),
                PublishAction::make(),
                ViewAction::make(),
                ReplicateAction::make()
                    ->visible($config->isReplicateActionOnTableEnabled($config->getPageModel()::class))
                    ->successRedirectUrl(fn (ReplicateAction $action) => $pageResource::getUrl('edit', ['record' => $action->getReplica()])),
            ])
            ->toolbarActions([
                LinkedToMenuItemBulkDeleteAction::make(),
            ])
            ->recordUrl(
                fn ($record): string => $pageResource::getUrl('edit', ['record' => $record])
            )
            ->modifyQueryUsing(function (Builder $query) {
                $query->with(['menuItem', 'parent.parent']);
            });
    }
}
