<?php

namespace App\Filament\Resources;

use App\Enums\OutletType;
use App\Filament\Resources\OutletResource\Pages;
use App\Filament\Resources\OutletResource\RelationManagers;
use App\Models\Outlet;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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

class OutletResource extends Resource
{
    protected static ?string $model = Outlet::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Outlet details')->schema([
                TextInput::make('name')
                    ->required()->maxLength(255)->columnSpanFull(),

                Select::make('type')
                    ->options(OutletType::class)
                    ->required(),

                Textarea::make('address')
                    ->required()->maxLength(500)->columnSpanFull(),

                TextInput::make('contact_person')->maxLength(255),
                TextInput::make('contact_phone')->maxLength(50)->tel(),

                Select::make('timezone')
                    ->options(collect(timezone_identifiers_list())->mapWithKeys(fn ($tz) => [$tz => $tz]))
                    ->default('Asia/Kuala_Lumpur')
                    ->searchable()
                    ->required(),

                Toggle::make('is_active')->default(true)->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('type')->badge()->sortable(),
                TextColumn::make('address')->limit(40)->toggleable(),
                // withCount('qrConfigs') aliases the value as snake_case qr_configs_count.
                TextColumn::make('qr_configs_count')
                    ->counts('qrConfigs')
                    ->label('QR configs'),
                IconColumn::make('is_active')->boolean()->sortable(),
                TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')->options(OutletType::class),
                TernaryFilter::make('is_active'),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([DeleteBulkAction::make()]);
    }

    public static function getRelationManagers(): array
    {
        return [
            RelationManagers\QrConfigsRelationManager::class,
            RelationManagers\MealsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOutlets::route('/'),
            'create' => Pages\CreateOutlet::route('/create'),
            'edit' => Pages\EditOutlet::route('/{record}/edit'),
        ];
    }
}
