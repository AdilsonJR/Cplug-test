<?php

namespace App\UseCases;

use App\Services\SalesReportService;

class GetSalesReportUseCase
{
    public function __construct(private SalesReportService $service)
    {
    }

    public function execute(?string $startDate, ?string $endDate, ?string $productSku): array
    {
        return $this->service->getSalesReport($startDate, $endDate, $productSku);
    }
}
