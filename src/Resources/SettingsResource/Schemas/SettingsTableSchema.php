<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\SettingsResource\Schemas;

use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Statikbe\FilamentFlexibleContentBlockPages\Models\Settings;

/**
 * This settings table is nowhere shown, since the db table only contains 1 record, we redirect immediately to the edit
 * page of this record.
 */
class SettingsTableSchema
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make(Settings::SETTING_SITE_TITLE),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }
}
