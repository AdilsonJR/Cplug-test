<?php

namespace App\Repositories;

use App\Models\Sale;

class SaleRepository
{
    public function createPending(): Sale
    {
        return Sale::create([
            'total_amount' => 0,
            'total_cost' => 0,
            'total_profit' => 0,
            'status' => 'pending',
        ]);
    }

    public function updateTotalsAndStatus(Sale $sale, array $totals, string $status): Sale
    {
        $sale->update([
            'total_amount' => $totals['total_amount'],
            'total_cost' => $totals['total_cost'],
            'total_profit' => $totals['total_profit'],
            'status' => $status,
        ]);

        return $sale;
    }

    public function updateStatus(Sale $sale, string $status): Sale
    {
        $sale->update([
            'status' => $status,
        ]);

        return $sale;
    }

    public function getLockedById(int $saleId): Sale
    {
        return Sale::where('id', $saleId)
            ->lockForUpdate()
            ->firstOrFail();
    }
}
