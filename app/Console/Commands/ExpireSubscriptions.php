<?php

namespace App\Console\Commands;

use App\Services\SubscriptionService;
use Illuminate\Console\Command;

class ExpireSubscriptions extends Command
{
    protected $signature = 'subscriptions:expire';

    protected $description = 'Mark active subscriptions whose weekly plan has ended as expired';

    public function handle(SubscriptionService $service): int
    {
        $count = $service->expireLapsed();

        $this->info("Expired {$count} subscription(s).");

        return self::SUCCESS;
    }
}
