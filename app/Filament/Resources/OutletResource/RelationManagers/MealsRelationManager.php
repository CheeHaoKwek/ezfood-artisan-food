<?php

namespace App\Filament\Resources\OutletResource\RelationManagers;

use App\Enums\MealSlotType;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class MealsRelationManager extends RelationManager
{
    protected static string $relationship = 'meals';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(255)->columnSpanFull(),
            Textarea::make('description')->maxLength(1000)->columnSpanFull(),

            TextInput::make('price')
                ->required()->numeric()->prefix('RM')->minValue(0),

            CheckboxList::make('available_slots')
                ->options(MealSlotType::class)
                ->label('Available slots (leave empty = all slots)')
                ->columns(3)
                ->columnSpanFull(),

            Toggle::make('is_active')->default(true),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('price')->money('MYR')->sortable(),
                TextColumn::make('available_slots')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state ? implode(', ', (array) $state) : 'All slots'),
                IconColumn::make('is_active')->boolean(),
            ])
            ->toolbarActions([CreateAction::make(), DeleteBulkAction::make()])
            ->recordActions([EditAction::make(), DeleteAction::make()]);
    }
}
