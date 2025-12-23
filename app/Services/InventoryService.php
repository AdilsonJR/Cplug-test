<?php

namespace App\Services;

use App\DTOs\InventoryEntryDTO;
use App\Repositories\InventoryRepository;
use App\Repositories\ProductRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use Illuminate\Support\Collection;

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

            $this->updateQuantity($product->id, $dto->quantity);

            DB::commit();
            $this->refreshProductCache($product->id);
            return $product->load('inventory');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function updateQuantity(int $productId, int $quantityChange): void
    {
        $this->inventory->createOrUpdate(
                [
                    'product_id' => $productId,
                ],
                [
                    'product_id' => $productId,
                    'quantity' => $quantityChange,
                    'last_updated' => now(),
                ]
            );
    }

    public function loadProductAndInventoryWithLockForUpdate(array $saleItemIds): Collection
    {
        return $this->inventory->loadProductAndInventoryWithLockForUpdate($saleItemIds);
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

    public function refreshProductCache(int $productId): void
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
            'total_cost' =>  number_format($totalCost, 2, '.', ''),
            'total_sale' => number_format($totalSale, 2, '.', ''),
            'projected_profit' =>  number_format($totalSale - $totalCost, 2, '.', ''),
            'last_updated' => $row->last_updated,
            'created_at' => $row->created_at,
            'updated_at' => $row->updated_at,
        ];
    }
}
