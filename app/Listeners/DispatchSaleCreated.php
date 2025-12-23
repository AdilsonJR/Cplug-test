<?php

namespace App\Listeners;

use App\Events\SaleCreated;
use App\Jobs\UpdateInventoryJob;
use App\Jobs\ProcessSaleJob;

class DispatchSaleCreated
{
    public function handle(SaleCreated $event): void
    {
        ProcessSaleJob::dispatch($event->sale->id)->onQueue('sales')->afterCommit();
        UpdateInventoryJob::dispatch($event->sale)->onQueue('inventory')->afterCommit();
    }
}
