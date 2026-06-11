<?php

namespace App\Filament\Resources\QrConfigResource\RelationManagers;

use App\Enums\MealSlotType;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MealSlotsRelationManager extends RelationManager
{
    protected static string $relationship = 'mealSlots';
    protected static ?string $title = 'Meal Slots';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('slot')
                ->options(MealSlotType::class)
                ->required(),

            TimePicker::make('starts_at')->seconds(false)->required(),
            TimePicker::make('ends_at')->seconds(false)->required(),
            TimePicker::make('cutoff_time')
                ->seconds(false)
                ->required()
                ->label('Cut-off time'),

            Toggle::make('is_active')->default(true),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('slot')->badge()->sortable(),
                TextColumn::make('starts_at')->label('Starts'),
                TextColumn::make('ends_at')->label('Ends'),
                TextColumn::make('cutoff_time')->label('Cut-off'),
                IconColumn::make('is_active')->boolean(),
            ])
            ->toolbarActions([CreateAction::make(), DeleteBulkAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
