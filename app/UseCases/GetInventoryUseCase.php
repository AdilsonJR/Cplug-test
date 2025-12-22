<?php

namespace App\UseCases;

use App\Services\InventoryService;

class GetInventoryUseCase
{
    public function __construct(private InventoryService $service)
    {
    }

    public function execute(): array
    {
        return $this->service->getInventory();
    }
}
