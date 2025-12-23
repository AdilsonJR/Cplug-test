<?php

namespace Tests\Unit;

use App\Events\SaleCreated;
use App\Jobs\ProcessSaleJob;
use App\Jobs\UpdateInventoryJob;
use App\Listeners\DispatchSaleCreated;
use App\Models\Sale;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SaleEventDispatchTest extends TestCase
{
    use RefreshDatabase;

    public function test_sale_observer_dispatches_sale_created_event(): void
    {
        Event::fake([SaleCreated::class]);

        $sale = Sale::create([
            'total_amount' => 0,
            'total_cost' => 0,
            'total_profit' => 0,
            'status' => 'pending',
        ]);

        Event::assertDispatched(SaleCreated::class, function ($event) use ($sale) {
            return $event->sale->id === $sale->id;
        });
    }

    public function test_dispatch_sale_created_listener_queues_jobs(): void
    {
        Event::fake();
        Queue::fake();

        $sale = Sale::create([
            'total_amount' => 0,
            'total_cost' => 0,
            'total_profit' => 0,
            'status' => 'pending',
        ]);

        $listener = new DispatchSaleCreated();
        $listener->handle(new SaleCreated($sale));

        Queue::assertPushedOn('sales', ProcessSaleJob::class);
        Queue::assertPushedOn('inventory', UpdateInventoryJob::class);
    }
}
