<?php

namespace App\Services;

use App\Models\Sale;
use App\Repositories\SaleItemRepository;

class SaleItemsService
{
    public function __construct(private SaleItemRepository $saleItemRepository) {}

    public function updateUnitCost(Sale $sale): void
    {
        $sale->items->each(function ($item) {
            $unitCost = (float) $item->product->cost_price;

            if ($item->unit_cost != $unitCost) {
                $this->saleItemRepository->updateUnitCost($item, $unitCost);
            }
        });
    }
}
