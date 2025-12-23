<?php

namespace Tests\Unit;

use App\Jobs\ProcessSaleJob;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Services\SaleItemsService;
use App\Services\SaleService;
use App\Services\SalesReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class SalesReportServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_report_filters_by_sku_and_returns_all_sale_items(): void
    {
        Event::fake();

        $productA = Product::factory()->create([
            'sku' => 'SKU-A',
            'cost_price' => 4.00,
            'sale_price' => 10.00,
        ]);

        $productB = Product::factory()->create([
            'sku' => 'SKU-B',
            'cost_price' => 2.00,
            'sale_price' => 5.00,
        ]);

        $saleOne = Sale::create([
            'total_amount' => 0,
            'total_cost' => 0,
            'total_profit' => 0,
            'status' => 'pending',
        ]);

        $saleTwo = Sale::create([
            'total_amount' => 0,
            'total_cost' => 0,
            'total_profit' => 0,
            'status' => 'pending',
        ]);

        SaleItem::create([
            'sale_id' => $saleOne->id,
            'product_id' => $productA->id,
            'quantity' => 1,
            'unit_price' => 10.00,
            'unit_cost' => 4.00,
        ]);

        SaleItem::create([
            'sale_id' => $saleOne->id,
            'product_id' => $productB->id,
            'quantity' => 2,
            'unit_price' => 5.00,
            'unit_cost' => 2.00,
        ]);

        SaleItem::create([
            'sale_id' => $saleTwo->id,
            'product_id' => $productB->id,
            'quantity' => 1,
            'unit_price' => 5.00,
            'unit_cost' => 2.00,
        ]);

        (new ProcessSaleJob($saleOne->id))
            ->handle(app(SaleItemsService::class), app(SaleService::class));

        (new ProcessSaleJob($saleTwo->id))
            ->handle(app(SaleItemsService::class), app(SaleService::class));

        $service = app(SalesReportService::class);
        $report = $service->getSalesReport(null, null, 'SKU-A');

        $this->assertCount(1, $report['sales']);
        $this->assertSame($saleOne->id, $report['sales'][0]['id']);
        $this->assertCount(2, $report['sales'][0]['items']);
        $this->assertEquals(20.00, $report['totals']['total_amount']);
        $this->assertEquals(8.00, $report['totals']['total_cost']);
        $this->assertEquals(12.00, $report['totals']['total_profit']);
    }
}
