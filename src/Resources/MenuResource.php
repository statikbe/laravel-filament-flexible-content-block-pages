<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources;

use Filament\Actions\Action;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\MenuResource\Pages\CreateMenu;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\MenuResource\Pages\EditMenu;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\MenuResource\Pages\ListMenus;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\MenuResource\Pages\ManageMenuItems;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Form\Fields\CodeField;
use Statikbe\FilamentFlexibleContentBlocks\Filament\Form\Fields\TitleField;
use Statikbe\FilamentFlexibleContentBlocks\FilamentFlexibleBlocksConfig;

class MenuResource extends Resource
{
    use Translatable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bars-3';

    protected static ?string $recordRouteKeyName = 'id';

    protected static ?string $recordTitleAttribute = 'name';

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

    public static function getNavigationSort(): ?int
    {
        return FilamentFlexibleContentBlockPages::config()->getMenuNavigationSort();
    }

    public static function form(Schema $schema): Schema
    {
        $availableStyles = FilamentFlexibleContentBlockPages::config()->getMenuStyles();
        $showStyleField = count($availableStyles) > 1;

        $formFields = [
            TextInput::make('name')
                ->label(flexiblePagesTrans('menus.form.name_lbl'))
                ->helperText(flexiblePagesTrans('menus.form.name_help'))
                ->required()
                ->maxLength(255),

            CodeField::create(true)
                ->helperText(flexiblePagesTrans('menus.form.code_help')),

            Textarea::make('description')
                ->label(flexiblePagesTrans('menus.form.description_lbl'))
                ->helperText(flexiblePagesTrans('menus.form.description_help'))
                ->rows(3)
                ->columnSpan(2),

            TitleField::create()
                ->label(flexiblePagesTrans('menus.form.title_lbl'))
                ->helperText(flexiblePagesTrans('menus.form.title_help')),
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

        $formFields[] = TextInput::make('max_depth')
            ->label(flexiblePagesTrans('menus.form.max_depth_lbl'))
            ->numeric()
            ->minValue(1)
            ->maxValue(10)
            ->placeholder((string) FilamentFlexibleContentBlockPages::config()->getMenuMaxDepth())
            ->helperText(flexiblePagesTrans('menus.form.max_depth_help'));

        return $schema
            ->components([
                Section::make(flexiblePagesTrans('menus.form.general_section'))
                    ->schema($formFields)
                    ->columns(2),
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
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('manage_items')
                    ->label(flexiblePagesTrans('menus.actions.manage_items'))
                    ->icon('heroicon-o-bars-3')
                    ->color('secondary')
                    ->url(fn ($record) => static::getUrl('items', ['record' => $record])),
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
            'index' => ListMenus::route('/'),
            'create' => CreateMenu::route('/create'),
            'edit' => EditMenu::route('/{record:id}/edit'),
            'items' => ManageMenuItems::route('/{record:id}/items'),
        ];
    }
}
