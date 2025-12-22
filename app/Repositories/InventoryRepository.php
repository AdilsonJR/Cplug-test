<?php

namespace App\Repositories;

use App\Models\Inventory;

class InventoryRepository
{
    public function createOrUpdate(array $conditions, array $data): Inventory
    {
        return Inventory::updateOrCreate(
            $conditions,
            $data
        );
    }
}
