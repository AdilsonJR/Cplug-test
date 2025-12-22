<?php

namespace App\Repositories;

use App\Models\Product;

class ProductRepository
{
    public function findByIdOrSku(?int $productId, ?string $sku): Product
    {
        return Product::query()
            ->when($productId, function ($query, $id) {
                $query->where('id', $id);
            }, function ($query) use ($sku) {
                $query->where('sku', $sku);
            })
            ->firstOrFail();
    }

    public function updateCostPrice(Product $product, float $costPrice): Product
    {
        $product->update(['cost_price' => $costPrice]);
        return $product;
    }

    public function getAllproductsIdsAvailable(): array
    {
        return Product::query()
            ->select('id')
            ->pluck('id')
            ->toArray();
    }
}
