<?php

namespace App\Filament\Resources\OutletResource\RelationManagers;

use App\Enums\CutoffBasis;
use App\Enums\MealMode;
use App\Enums\OperationDays;
use App\Models\QrConfig;
use App\Services\QrCodeService;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class QrConfigsRelationManager extends RelationManager
{
    protected static string $relationship = 'qrConfigs';
    protected static ?string $title = 'QR Configurations';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
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

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->copyable()->label('QR code'),
                TextColumn::make('operation_days')->badge(),
                TextColumn::make('meal_mode')->badge(),
                TextColumn::make('cutoff_basis')->badge(),
                IconColumn::make('is_active')->boolean(),
            ])
            ->toolbarActions([CreateAction::make(), DeleteBulkAction::make()])
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
                DeleteAction::make(),
            ]);
    }
}
