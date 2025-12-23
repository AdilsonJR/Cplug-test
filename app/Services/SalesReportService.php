<?php

namespace App\Services;

use App\Repositories\SalesReportRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class SalesReportService
{
    public function __construct(private SalesReportRepository $reports)
    {
    }

    public function getSalesReport(?string $startDate, ?string $endDate, ?string $productSku): array
    {
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : null;
        $end = $endDate ? Carbon::parse($endDate)->endOfDay() : null;

        $sales = $this->reports->fetchReportRows(
            $start?->toDateTimeString(),
            $end?->toDateTimeString(),
            $productSku
        );

        return [
            'filters' => [
                'start_date' => $start?->toDateString(),
                'end_date' => $end?->toDateString(),
                'product_sku' => $productSku,
            ],
            'totals' => [
                'total_amount' => $this->sumTotalSalesAmount($sales),
                'total_cost' => $this->sumTotalSalesCost($sales),
                'total_profit' => $this->calculateTotalSalesProfit($sales),
            ],
            'sales' => $sales,
        ];
    }

    public function sumTotalSalesAmount(Collection $sales): float
    {
        return $sales->reduce(function ($carry, $sale) {
            return $carry + $sale->total_amount;
        }, 0);
    }

    public function sumTotalSalesCost(Collection $sales): float
    {
        return $sales->reduce(function ($carry, $sale) {
            return $carry + $sale->total_cost;
        }, 0);
    }

    public function calculateTotalSalesProfit(Collection $sales): float
    {
        return $this->sumTotalSalesAmount($sales) - $this->sumTotalSalesCost($sales);
    }
}
