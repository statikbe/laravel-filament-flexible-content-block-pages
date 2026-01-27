<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources;

use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Http\Response;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\RedirectResource\Pages\CreateRedirect;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\RedirectResource\Pages\EditRedirect;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\RedirectResource\Pages\ListRedirects;
use Statikbe\FilamentFlexibleContentBlocks\FilamentFlexibleBlocksConfig;

class RedirectResource extends Resource
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bolt';

    public static function getModel(): string
    {
        return FilamentFlexibleContentBlockPages::config()->getRedirectModel()::class;
    }

    public static function getLabel(): ?string
    {
        return flexiblePagesTrans('redirects.redirects_lbl');
    }

    public static function getPluralLabel(): ?string
    {
        return flexiblePagesTrans('redirects.redirects_plural_lbl');
    }

    public static function getNavigationGroup(): string
    {
        return flexiblePagesTrans('redirects.nav_group');
    }

    public static function getNavigationSort(): ?int
    {
        return FilamentFlexibleContentBlockPages::config()->getRedirectNavigationSort();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('old_url')
                            ->label(flexiblePagesTrans('redirects.redirect_old_url'))
                            ->required(),
                        TextInput::make('new_url')
                            ->label(flexiblePagesTrans('redirects.redirect_new_url'))
                            ->required(),
                        Select::make('status_code')
                            ->label(flexiblePagesTrans('redirects.redirect_status_code'))
                            ->default(Response::HTTP_MOVED_PERMANENTLY)
                            ->options([
                                Response::HTTP_MOVED_PERMANENTLY => '301 - Moved permanently (most used)',
                                Response::HTTP_FOUND => '302 - Found (Google does not update old indexed url)',
                                Response::HTTP_TEMPORARY_REDIRECT => '307 - Temporary redirect (if you want to reuse the old url later)',
                                Response::HTTP_PERMANENTLY_REDIRECT => '308 - Permanently redirect',
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListRedirects::route('/'),
            'create' => CreateRedirect::route('/create'),
            'edit' => EditRedirect::route('/{record}/edit'),
        ];
    }
}
