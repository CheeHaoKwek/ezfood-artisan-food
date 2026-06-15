<?php

namespace App\Filament\Resources;

use App\Enums\DietaryPreference;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';
    protected static ?int $navigationSort = 5;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Profile')->schema([
                TextInput::make('name')->required()->maxLength(255),
                TextInput::make('nickname')->maxLength(255),
                TextInput::make('mobile_number')->tel()->maxLength(20),
                TextInput::make('email')->email()->required()->maxLength(255)->unique(ignoreRecord: true),
                TextInput::make('company_name')->maxLength(255),
                Select::make('dietary_preference')->options(DietaryPreference::class),
            ])->columns(2),

            Section::make('Tenant')->schema([
                Select::make('outlet_id')
                    ->label('Outlet')
                    ->relationship('outlet', 'name')
                    ->searchable()
                    ->helperText('Changing this moves the user to another tenant.'),
                Toggle::make('is_admin')
                    ->label('CMS admin')
                    ->helperText('Grants full CMS access.')
                    // Guard against locking yourself out by removing your own admin flag.
                    ->disabled(fn (?User $record) => $record?->id === Auth::id()),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('nickname')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('mobile_number')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('company_name')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('dietary_preference')
                    ->badge()
                    ->color(fn (?DietaryPreference $state) => match ($state) {
                        DietaryPreference::Vegetarian => 'success',
                        default => 'gray',
                    })
                    ->placeholder('—'),
                TextColumn::make('outlet.name')->label('Outlet')->sortable()->placeholder('—'),
                IconColumn::make('is_admin')->label('Admin')->boolean()
                    // Non-admin is the normal state — neutral dash instead of red cross.
                    ->falseIcon('heroicon-o-minus')
                    ->falseColor('gray'),
            ])
            ->filters([
                SelectFilter::make('outlet_id')->label('Outlet')->relationship('outlet', 'name'),
                SelectFilter::make('dietary_preference')->options(DietaryPreference::class),
                TernaryFilter::make('is_admin'),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
