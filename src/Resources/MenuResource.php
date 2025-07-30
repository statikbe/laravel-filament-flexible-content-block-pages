<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\MenuResource\Pages\CreateMenu;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\MenuResource\Pages\EditMenu;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\MenuResource\Pages\ListMenus;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\MenuResource\Pages\ManageMenuItems;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\MenuResource\Pages\OldManageMenuItems;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Form\Fields\CodeField;

class MenuResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-bars-3';

    protected static ?string $recordRouteKeyName = 'id';

    public static function getModel(): string
    {
        return FilamentFlexibleContentBlockPages::config()->getMenuModel()::class;
    }

    public static function getLabel(): ?string
    {
        return flexiblePagesTrans('menus.lbl');
    }

    public static function getPluralLabel(): ?string
    {
        return flexiblePagesTrans('menus.plural_lbl');
    }

    public static function getNavigationGroup(): string
    {
        return flexiblePagesTrans('menus.nav_group');
    }

    public static function form(Form $form): Form
    {
        $availableStyles = FilamentFlexibleContentBlockPages::config()->getMenuStyles();
        $showStyleField = count($availableStyles) > 1;

        $formFields = [
            TextInput::make('name')
                ->label(flexiblePagesTrans('menus.form.name_lbl'))
                ->required()
                ->maxLength(255)
                ->helperText(flexiblePagesTrans('menus.form.name_help')),

            CodeField::create()
                ->helperText(flexiblePagesTrans('menus.form.code_help')),
        ];

        // Only show style field if there are multiple styles available
        if ($showStyleField) {
            $formFields[] = Select::make('style')
                ->label(flexiblePagesTrans('menus.form.style_lbl'))
                ->options(FilamentFlexibleContentBlockPages::config()->getMenuStyleOptions())
                ->default(FilamentFlexibleContentBlockPages::config()->getDefaultMenuStyle())
                ->required()
                ->helperText(flexiblePagesTrans('menus.form.style_help'));
        }

        $formFields[] = Textarea::make('description')
            ->label(flexiblePagesTrans('menus.form.description_lbl'))
            ->rows(3)
            ->helperText(flexiblePagesTrans('menus.form.description_help'));

        return $form
            ->schema([
                Section::make(flexiblePagesTrans('menus.form.general_section'))
                    ->schema($formFields)
                    ->columns(1),
            ]);
    }

    public static function table(Table $table): Table
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

                TextColumn::make('menuItems_count')
                    ->label(flexiblePagesTrans('menus.table.items_count_col'))
                    ->counts('menuItems')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(flexiblePagesTrans('menus.table.created_at_col'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('manage_items')
                    ->label(flexiblePagesTrans('menus.actions.manage_items'))
                    ->icon('heroicon-o-bars-3')
                    ->color('primary')
                    ->url(fn ($record) => static::getUrl('items', ['record' => $record])),
                EditAction::make(),
            ])
            ->bulkActions([
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
            'index' => ListMenus::route('/'),
            'create' => CreateMenu::route('/create'),
            'edit' => EditMenu::route('/{record:id}/edit'),
            'items' => ManageMenuItems::route('/{record:id}/items'),
        ];
    }
}
