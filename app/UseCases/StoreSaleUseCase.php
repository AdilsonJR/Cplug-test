<?php

namespace App\UseCases;

use App\DTOs\CreateSaleDTO;
use App\Models\Sale;
use App\Services\SaleService;

class StoreSaleUseCase
{
    public function __construct(private SaleService $service)
    {
    }

    public function execute(CreateSaleDTO $dto): Sale
    {
        return $this->service->registerSale($dto);
    }
}
