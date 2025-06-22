<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Http\Response;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\RedirectResource\Pages;

class RedirectResource extends Resource
{
    public static function getModel(): string
    {
        return FilamentFlexibleContentBlockPages::config()->getRedirectModel()::class;
    }

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?int $navigationSort = 10;

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
        return flexiblePagesTrans('pages.nav_group');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                Tables\Columns\TextColumn::make('old_url')
                    ->label(flexiblePagesTrans('redirects.redirect_old_url'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('new_url')
                    ->label(flexiblePagesTrans('redirects.redirect_new_url'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('status_code')
                    ->label(flexiblePagesTrans('redirects.redirect_status_code')),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListRedirects::route('/'),
            'create' => Pages\CreateRedirect::route('/create'),
            'edit' => Pages\EditRedirect::route('/{record}/edit'),
        ];
    }
}
