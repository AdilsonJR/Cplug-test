<?php

namespace App\UseCases;

use App\DTOs\InventoryEntryDTO;
use App\Models\Product;
use App\Services\InventoryService;

class StoreInventoryUseCase
{
    public function __construct(private InventoryService $service)
    {
    }

    public function execute(InventoryEntryDTO $dto): Product
    {
        return $this->service->registerEntry($dto);
    }
}
