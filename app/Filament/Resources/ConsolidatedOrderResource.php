<?php

namespace App\Filament\Resources;

use App\Enums\ConsolidatedOrderStatus;
use App\Filament\Resources\ConsolidatedOrderResource\Pages;
use App\Models\ConsolidatedOrder;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ConsolidatedOrderResource extends Resource
{
    protected static ?string $model = ConsolidatedOrder::class;
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Consolidated Orders';
    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('outlet_id')
                ->relationship('outlet', 'name')
                ->disabled(),

            TextInput::make('serve_date')->disabled(),
            TextInput::make('slot')->disabled(),
            TextInput::make('total_meals')->disabled(),

            Select::make('status')
                ->options(ConsolidatedOrderStatus::class)
                ->required(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('serve_date', 'desc')
            ->columns([
                TextColumn::make('outlet.name')->searchable()->sortable(),
                TextColumn::make('serve_date')->date('D, d M Y')->sortable(),
                TextColumn::make('slot')->badge()->sortable(),
                TextColumn::make('total_meals')->numeric()->sortable(),
                TextColumn::make('status')->badge()
                    ->color(fn (ConsolidatedOrderStatus $state) => match ($state) {
                        ConsolidatedOrderStatus::Pending => 'warning',
                        ConsolidatedOrderStatus::SentToLogistics => 'info',
                        ConsolidatedOrderStatus::Delivered => 'success',
                    }),
                TextColumn::make('consolidated_at')->dateTime()->sortable()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('outlet')->relationship('outlet', 'name'),
                SelectFilter::make('status')->options(ConsolidatedOrderStatus::class),
                Filter::make('serve_date')
                    ->schema([DatePicker::make('serve_date')->label('Serve date')])
                    ->query(fn ($query, array $data) => $query->when(
                        $data['serve_date'],
                        fn ($q) => $q->whereDate('serve_date', $data['serve_date'])
                    )),
            ])
            ->recordActions([ViewAction::make(), EditAction::make()])
            ->toolbarActions([])
            ->emptyStateHeading('No consolidated orders yet')
            ->emptyStateDescription('Orders appear here automatically after each meal slot\'s cut-off — the consolidation job runs every 5 minutes.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConsolidatedOrders::route('/'),
            'view' => Pages\ViewConsolidatedOrder::route('/{record}'),
            'edit' => Pages\EditConsolidatedOrder::route('/{record}/edit'),
        ];
    }
}
