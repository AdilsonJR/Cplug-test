<?php

namespace App\Repositories;

use App\Models\SaleItem;

class SaleItemRepository
{
    public function insertForSale(int $saleId, array $items): void
    {
        foreach ($items as &$item) {
            $item['sale_id'] = $saleId;
        }

        SaleItem::insert($items);
    }

    public function updateUnitCost(SaleItem $saleItem, float $unitCost): void
    {
        $saleItem->update([
            'unit_cost' => $unitCost,
        ]);
    }
}
