<?php

namespace Tests\Unit;

use App\Jobs\UpdateInventoryJob;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class UpdateInventoryJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_inventory_job_decrements_stock_and_refreshes_cache(): void
    {
        Event::fake();
        config(['cache.default' => 'array']);

        $product = Product::factory()->create([
            'cost_price' => 5.00,
            'sale_price' => 10.00,
        ]);

        $inventory = Inventory::create([
            'product_id' => $product->id,
            'quantity' => 10,
            'last_updated' => now(),
        ]);

        $sale = Sale::create([
            'total_amount' => 0,
            'total_cost' => 0,
            'total_profit' => 0,
            'status' => 'finalized',
        ]);

        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 3,
            'unit_price' => 10.00,
            'unit_cost' => 5.00,
        ]);

        $job = new UpdateInventoryJob($sale);
        $job->handle(app(InventoryService::class));

        $inventory->refresh();

        $this->assertSame(7, $inventory->quantity);

        $cached = Cache::get("inventory.product.{$product->id}");

        $this->assertIsArray($cached);
        $this->assertSame(7, $cached['quantity']);
    }
}
