<?php

namespace App\Filament\Resources;

use App\Enums\CutoffBasis;
use App\Enums\MealMode;
use App\Enums\OperationDays;
use App\Filament\Resources\QrConfigResource\Pages;
use App\Filament\Resources\QrConfigResource\RelationManagers;
use App\Models\QrConfig;
use App\Services\QrCodeService;
use Filament\Actions\Action;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class QrConfigResource extends Resource
{
    protected static ?string $model = QrConfig::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-qr-code';
    protected static ?string $navigationLabel = 'QR Configurations';
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Outlet')->schema([
                Select::make('outlet_id')
                    ->relationship('outlet', 'name')
                    ->searchable()
                    ->required(),
                TextInput::make('code')
                    ->label('QR code identifier')
                    ->disabled()
                    ->helperText('Auto-generated on creation.'),
            ])->columns(2),

            Section::make('Operation schedule')->schema([
                Select::make('operation_days')
                    ->options(OperationDays::class)
                    ->required(),

                Toggle::make('is_24_hours')
                    ->label('24-hour operation')
                    ->live()
                    ->default(false),

                TimePicker::make('opens_at')
                    ->seconds(false)
                    ->hidden(fn (Get $get) => $get('is_24_hours')),

                TimePicker::make('closes_at')
                    ->seconds(false)
                    ->hidden(fn (Get $get) => $get('is_24_hours')),
            ])->columns(2),

            Section::make('Meal settings')->schema([
                Select::make('meal_mode')
                    ->options(MealMode::class)
                    ->required(),

                Select::make('cutoff_basis')
                    ->options(CutoffBasis::class)
                    ->required()
                    ->live(),

                TimePicker::make('daily_cutoff_time')
                    ->seconds(false)
                    ->label('Daily cut-off time')
                    ->visible(fn (Get $get) => $get('cutoff_basis') === CutoffBasis::PerDay->value)
                    ->required(fn (Get $get) => $get('cutoff_basis') === CutoffBasis::PerDay->value),
            ])->columns(2),

            Section::make('Logistics')->schema([
                Textarea::make('delivery_location')->required()->maxLength(500)->columnSpanFull(),
                Toggle::make('is_active')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('outlet.name')->searchable()->sortable(),
                TextColumn::make('code')->copyable()->label('QR code'),
                TextColumn::make('operation_days')->badge(),
                TextColumn::make('meal_mode')->badge(),
                TextColumn::make('cutoff_basis')->badge(),
                IconColumn::make('is_active')->boolean()->sortable(),
            ])
            ->filters([
                SelectFilter::make('outlet')->relationship('outlet', 'name'),
                TernaryFilter::make('is_active'),
            ])
            ->recordActions([
                Action::make('downloadQr')
                    ->label('Download QR')
                    ->icon('heroicon-o-qr-code')
                    ->action(fn (QrConfig $record) => response()->streamDownload(
                        fn () => print app(QrCodeService::class)->svg($record),
                        'ezfood-qr-'.$record->code.'.svg',
                        ['Content-Type' => 'image/svg+xml'],
                    )),
                EditAction::make(),
            ])
            ->toolbarActions([DeleteBulkAction::make()]);
    }

    public static function getRelationManagers(): array
    {
        return [
            RelationManagers\MealSlotsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQrConfigs::route('/'),
            'create' => Pages\CreateQrConfig::route('/create'),
            'edit' => Pages\EditQrConfig::route('/{record}/edit'),
        ];
    }
}
