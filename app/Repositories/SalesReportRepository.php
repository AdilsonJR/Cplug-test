<?php

namespace App\Repositories;

use App\Models\Sale;
use Illuminate\Support\Collection;

class SalesReportRepository
{
    public function fetchReportRows(?string $startDate, ?string $endDate, ?string $productSku): Collection
    {
        return Sale::select([
                'id',
                'status',
                'total_amount',
                'total_cost',
                'total_profit',
                'created_at',
                'updated_at',
            ])
            ->with([
                'items' => function ($query) {
                    $query->select([
                        'id',
                        'sale_id',
                        'product_id',
                        'quantity',
                        'unit_price',
                        'unit_cost',
                        'created_at',
                        'updated_at',
                    ])->with([
                        'product' => function ($query) {
                            $query->select([
                                'id',
                                'sku',
                                'name',
                            ]);
                        },
                    ]);
                },
            ])
            ->when($startDate, function ($query, $startDate) {
                $query->where('created_at', '>=', $startDate);
            })
            ->when($endDate, function ($query, $endDate) {
                $query->where('created_at', '<=', $endDate);
            })
            ->when($productSku, function ($query, $productSku) {
                $query->whereHas('items.product', function ($query) use ($productSku) {
                    $query->where('sku', $productSku);
                });
            })
            ->orderByDesc('created_at')
            ->get();
    }
}
