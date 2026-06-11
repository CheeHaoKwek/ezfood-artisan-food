<?php

namespace App\Console\Commands;

use App\Services\OrderConsolidationService;
use Illuminate\Console\Command;

class ConsolidateOrders extends Command
{
    protected $signature = 'orders:consolidate';

    protected $description = 'Consolidate meal selections for every outlet slot past its cut-off time';

    public function handle(OrderConsolidationService $service): int
    {
        $orders = $service->consolidateDue();

        $this->info("Consolidated {$orders->count()} order(s).");

        return self::SUCCESS;
    }
}
