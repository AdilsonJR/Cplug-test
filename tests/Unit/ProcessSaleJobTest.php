<?php

namespace Tests\Unit;

use App\Jobs\ProcessSaleJob;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Services\SaleItemsService;
use App\Services\SaleService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ProcessSaleJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_process_sale_job_updates_totals_and_status(): void
    {
        Event::fake();

        $product = Product::factory()->create([
            'cost_price' => 4.00,
            'sale_price' => 10.00,
        ]);

        $sale = Sale::create([
            'total_amount' => 0,
            'total_cost' => 0,
            'total_profit' => 0,
            'status' => 'pending',
        ]);

        $saleItem = SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'unit_price' => 10.00,
            'unit_cost' => 0,
        ]);

        $productTwo = Product::factory()->create([
            'cost_price' => 3.00,
            'sale_price' => 8.00,
        ]);

        $saleItemTwo = SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $productTwo->id,
            'quantity' => 1,
            'unit_price' => 8.00,
            'unit_cost' => 0,
        ]);

        $job = new ProcessSaleJob($sale->id);
        $job->handle(app(SaleItemsService::class), app(SaleService::class));

        $sale->refresh();
        $saleItem->refresh();
        $saleItemTwo->refresh();

        $this->assertSame('processed', $sale->status);
        $this->assertEquals(28.00, (float) $sale->total_amount);
        $this->assertEquals(11.00, (float) $sale->total_cost);
        $this->assertEquals(17.00, (float) $sale->total_profit);
        $this->assertEquals(4.00, (float) $saleItem->unit_cost);
        $this->assertEquals(3.00, (float) $saleItemTwo->unit_cost);
    }
}
