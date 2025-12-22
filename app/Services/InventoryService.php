<?php

namespace App\Services;

use App\DTOs\InventoryEntryDTO;
use App\Repositories\InventoryRepository;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Product;

class InventoryService
{
    public function __construct(
        private ProductRepository $products,
        private InventoryRepository $inventory
    ) {}

    public function registerEntry(InventoryEntryDTO $dto): Product
    {
        try {
            DB::beginTransaction();

            $product = $this->products->findByIdOrSku($dto->productId, $dto->sku);

            if ($product->cost_price != $dto->costPrice) {
                $this->products->updateCostPrice($product, $dto->costPrice);
            }

            $this->inventory->createOrUpdate(
                [
                    'product_id' => $product->id,
                ],
                [
                    'product_id' => $product->id,
                    'quantity' => $dto->quantity,
                    'last_updated' => now(),
                ]
            );

            DB::commit();
            $this->refreshProductCache($product->id);
            return $product->load('inventory');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function getInventory(): array
    {
        $productIds = $this->products->getAllproductsIdsAvailable();

        $items = [];

        foreach ($productIds as $productId) {
            $cacheKey = $this->productCacheKey($productId);
            $cached = Cache::get($cacheKey);

            if ($cached) {
                $items[] = $cached;
                continue;
            }

            $row = $this->inventory->getInventoryWithProductByProductId($productId);
            $item = $this->buildInventoryItem($row);
            Cache::put($cacheKey, $item, config('cache.product_cache_ttl'));
            $items[] = $item;
        }

        return $items;
    }

    private function refreshProductCache(int $productId): void
    {
        $row = $this->inventory->getInventoryWithProductByProductId($productId);

        if (!$row) {
            return;
        }

        Cache::put(
            $this->productCacheKey($productId),
            $this->buildInventoryItem($row),
            config('cache.product_cache_ttl')
        );
    }

    private function productCacheKey(int $productId): string
    {
        return "inventory.product.{$productId}";
    }

    private function buildInventoryItem(object $row): array
    {
        $totalCost = $row->quantity * $row->cost_price;
        $totalSale = $row->quantity * $row->sale_price;

        return [
            'inventory_id' => $row->id,
            'product_id' => $row->product_id,
            'sku' => $row->sku,
            'name' => $row->name,
            'quantity' => $row->quantity,
            'cost_price' => $row->cost_price,
            'sale_price' => $row->sale_price,
            'total_cost' => $totalCost,
            'total_sale' => $totalSale,
            'projected_profit' => $totalSale - $totalCost,
            'last_updated' => $row->last_updated,
            'created_at' => $row->created_at,
            'updated_at' => $row->updated_at,
        ];
    }
}
