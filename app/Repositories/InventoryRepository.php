<?php

namespace App\Repositories;

use App\Models\Inventory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Models\SaleItem;

class InventoryRepository
{
    public function createOrUpdate(array $conditions, array $data): Inventory
    {
        return Inventory::updateOrCreate(
            $conditions,
            $data
        );
    }

    public function getInventoryWithProductByProductId(int $productId): ?object
    {
        return Inventory::query()
            ->rightJoin('products', 'inventory.product_id', '=', 'products.id')
            ->select(
                [
                    'inventory.id',
                    'products.id as product_id',
                    DB::raw('COALESCE(inventory.quantity, 0) as quantity'),
                    'inventory.last_updated',
                    'inventory.created_at',
                    'inventory.updated_at',
                    'products.sku',
                    'products.name',
                    'products.cost_price',
                    'products.sale_price'
                ]
            )
            ->where('products.id', $productId)
            ->first();
    }

    public function loadProductAndInventoryWithLockForUpdate(array $saleItemIds): Collection
    {
        return SaleItem::whereIn('id', $saleItemIds)
            ->with(['product.inventory' => fn($q) => $q->lockForUpdate()])
            ->get();
    }
}
