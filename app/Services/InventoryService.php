<?php

namespace App\Services;

use App\DTOs\InventoryEntryDTO;
use App\Models\Inventory;
use App\Repositories\InventoryRepository;
use App\Repositories\ProductRepository;
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
            return $product->load('inventory');
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
