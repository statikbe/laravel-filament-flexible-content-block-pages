<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use LaraZeus\SpatieTranslatable\Resources\Concerns\Translatable;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Form\Components\DescriptionField;
use Statikbe\FilamentFlexibleContentBlockPages\Form\Components\NameField;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\TagResource\Pages\CreateTag;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\TagResource\Pages\EditTag;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\TagResource\Pages\ListTags;
use Statikbe\FilamentFlexibleContentBlocks\FilamentFlexibleBlocksConfig;

class TagResource extends Resource
{
    use Translatable;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-tag';

    protected static ?string $recordRouteKeyName = 'id';

    protected static ?string $recordTitleAttribute = 'name';

    /**
     * @return class-string
     */
    public static function getModel(): string
    {
        return FilamentFlexibleContentBlockPages::config()->getTagModel()::class;
    }

    public static function getNavigationGroup(): ?string
    {
        return flexiblePagesTrans('tags.navigation_group');
    }

    public static function getLabel(): ?string
    {
        return flexiblePagesTrans('tags.tag_lbl');
    }

    public static function getModelLabel(): string
    {
        return flexiblePagesTrans('tags.tag_lbl');
    }

    public static function getPluralModelLabel(): string
    {
        return flexiblePagesTrans('tags.tag_plural_lbl');
    }

    public static function getNavigationSort(): ?int
    {
        return FilamentFlexibleContentBlockPages::config()->getTagNavigationSort();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make([
                    NameField::create(true),
                    DescriptionField::create('seo_description', false),
                    Select::make('type')
                        ->label(flexiblePagesTrans('tags.tag_type_lbl'))
                        ->hint(flexiblePagesTrans('tags.tag_type_hint'))
                        ->relationship('tagType', 'name')
                        ->preload()
                        ->default(function (Select $component) {
                            $relationship = $component->getRelationship();
                            if (! $relationship) {
                                return null;
                            }

                            /** @phpstan-ignore-next-line */
                            return $relationship->getModel()->query()
                                ->where('is_default_type', true)->first()?->getKey() ?? null;
                        }),
                ]),
            ]);
    }

    public static function table(Table $table): Table
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
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTags::route('/'),
            'create' => CreateTag::route('/create'),
            'edit' => EditTag::route('/{record:id}/edit'),
        ];
    }
}
