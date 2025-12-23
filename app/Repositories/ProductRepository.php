<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Support\Collection;

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

    public function getByIdsOrSkus(array $productIds, array $skus): Collection
    {
        return Product::query()
            ->where(function ($query) use ($productIds, $skus) {
                if ($productIds) {
                    $query->whereIn('id', array_unique($productIds));
                }

                if ($skus) {
                    if ($productIds) {
                        $query->orWhereIn('sku', array_unique($skus));
                    } else {
                        $query->whereIn('sku', array_unique($skus));
                    }
                }
            })
            ->get();
    }
}
