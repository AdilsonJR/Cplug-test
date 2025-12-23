<?php

namespace App\UseCases;

use App\Models\Sale;
use App\Services\SaleService;

class GetSaleUseCase
{
    public function __construct(private SaleService $service)
    {
    }

    public function execute(int $saleId): Sale
    {
        return $this->service->getSaleDetails($saleId);
    }
}
