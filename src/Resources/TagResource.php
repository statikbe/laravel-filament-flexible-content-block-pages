<?php

namespace Statikbe\FilamentFlexibleContentBlockPages\Resources;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Concerns\Translatable;
use Filament\Resources\Resource;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Statikbe\FilamentFlexibleContentBlockPages\Facades\FilamentFlexibleContentBlockPages;
use Statikbe\FilamentFlexibleContentBlockPages\Form\Components\DescriptionField;
use Statikbe\FilamentFlexibleContentBlockPages\Form\Components\NameField;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\TagResource\Pages\CreateTag;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\TagResource\Pages\EditTag;
use Statikbe\FilamentFlexibleContentBlockPages\Resources\TagResource\Pages\ListTags;

class TagResource extends Resource
{
    use Translatable;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

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
        return 1;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                                ->where('is_default_type', true)->first()->id ?? null;
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
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
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
