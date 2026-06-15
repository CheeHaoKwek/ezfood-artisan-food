<?php

namespace App\Filament\Widgets;

use App\Enums\ConsolidatedOrderStatus;
use App\Models\ConsolidatedOrder;
use App\Models\Outlet;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EzFoodStatsOverview extends StatsOverviewWidget
{
    // Stats change a few times a day — Filament's default 5s polling would
    // re-run all three COUNTs continuously while the dashboard is open.
    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        return [
            Stat::make('Active outlets', Outlet::where('is_active', true)->count())
                ->icon('heroicon-o-building-office-2'),

            Stat::make('New subscribers (7 days)', User::where('is_admin', false)
                ->where('created_at', '>=', now()->subDays(7))
                ->count())
                ->icon('heroicon-o-user-plus'),

            Stat::make('Orders pending logistics', ConsolidatedOrder::where('status', ConsolidatedOrderStatus::Pending)->count())
                ->description('Consolidated, not yet sent to logistics')
                ->icon('heroicon-o-clipboard-document-list'),
        ];
    }
}
