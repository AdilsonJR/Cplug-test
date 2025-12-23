<?php

namespace App\Observers;

use App\Models\Sale;
use App\Events\SaleCreated;

class SaleObserver
{
    public function created(Sale $sale): void
    {
        event(new SaleCreated($sale));
    }
}
