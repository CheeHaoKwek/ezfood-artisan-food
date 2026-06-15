<?php

namespace App\Filament\Resources;

use App\Enums\MealSlotType;
use App\Filament\Resources\MealResource\Pages;
use App\Models\Meal;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class MealResource extends Resource
{
    protected static ?string $model = Meal::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cake';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('outlet_id')
                ->relationship('outlet', 'name')
                ->searchable()
                ->required(),

            TextInput::make('name')->required()->maxLength(255),
            Textarea::make('description')->maxLength(1000)->columnSpanFull(),

            TextInput::make('price')
                ->required()->numeric()->prefix('RM')->minValue(0),

            CheckboxList::make('available_slots')
                ->options(MealSlotType::class)
                ->label('Available slots (leave empty = all slots)')
                ->columns(3)
                ->columnSpanFull(),

            Toggle::make('is_vegetarian')->label('Vegetarian'),
            Toggle::make('is_active')->default(true),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('outlet.name')->searchable()->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('price')->money('MYR')->sortable(),
                TextColumn::make('available_slots')
                    ->badge()
                    // Badge columns format each array item individually; null never reaches
                    // formatStateUsing, so the "all slots" case needs placeholder().
                    ->formatStateUsing(fn (string $state) => MealSlotType::from($state)->getLabel())
                    ->placeholder('All slots'),
                IconColumn::make('is_vegetarian')->label('Veg')->boolean()
                    // Non-vegetarian is not a fault state — neutral dash instead of red cross.
                    ->falseIcon('heroicon-o-minus')
                    ->falseColor('gray')
                    ->sortable(),
                IconColumn::make('is_active')->boolean()->sortable(),
            ])
            ->filters([
                SelectFilter::make('outlet')->relationship('outlet', 'name'),
                TernaryFilter::make('is_vegetarian'),
                TernaryFilter::make('is_active'),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMeals::route('/'),
            'create' => Pages\CreateMeal::route('/create'),
            'edit' => Pages\EditMeal::route('/{record}/edit'),
        ];
    }
}
