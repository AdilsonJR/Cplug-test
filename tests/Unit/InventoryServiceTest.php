<?php

namespace Tests\Unit;

use App\DTOs\InventoryEntryDTO;
use App\Models\Product;
use App\Services\InventoryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class InventoryServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_entry_updates_inventory_and_cache(): void
    {
        config(['cache.default' => 'array']);

        $product = Product::factory()->create([
            'cost_price' => 10.00,
            'sale_price' => 20.00,
        ]);

        $service = app(InventoryService::class);
        $dto = new InventoryEntryDTO($product->id, null, 5, '12.50');

        $result = $service->registerEntry($dto);

        $product->refresh();

        $this->assertSame(12.5, (float) $product->cost_price);
        $this->assertSame(5, $result->inventory->quantity);
        $this->assertDatabaseHas('inventory', [
            'product_id' => $product->id,
            'quantity' => 5,
        ]);

        $cached = Cache::get("inventory.product.{$product->id}");

        $this->assertIsArray($cached);
        $this->assertSame(5, $cached['quantity']);
    }
}
