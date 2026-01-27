<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources\RedirectResource\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Http\Response;

class RedirectFormSchema
{
    public static function configure(Schema $schema): Schema
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
}