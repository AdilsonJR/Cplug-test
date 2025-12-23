<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Sale;

class SaleCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public Sale $sale)
    {
    }
}
