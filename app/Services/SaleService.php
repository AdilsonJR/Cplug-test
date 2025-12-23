<?php

namespace App\Services;

use App\DTOs\CreateSaleDTO;
use App\Models\Sale;
use App\Repositories\ProductRepository;
use App\Repositories\SaleItemRepository;
use App\Repositories\SaleRepository;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SaleService
{
    public function __construct(
        private SaleRepository $sales,
        private SaleItemRepository $saleItems,
        private ProductRepository $products
    ) {}

    public function registerSale(CreateSaleDTO $dto): Sale
    {
        return DB::transaction(function () use ($dto) {
            $sale = $this->sales->createPending();
            $items = $this->buildSaleItems($dto);

            $this->saleItems->insertForSale($sale->id, $items);

            return $sale;
        });
    }

    public function getLockedSaleById(int $saleId): Sale
    {
        return $this->sales->getLockedById($saleId);
    }

    public function getSaleDetails(int $saleId): Sale
    {
        return $this->sales->findWithItems($saleId);
    }

    private function buildSaleItems(CreateSaleDTO $dto): array
    {
        $productIds = [];
        $skus = [];

        foreach ($dto->items as $item) {
            if ($item->productId) {
                $productIds[] = $item->productId;
            }

            if ($item->sku) {
                $skus[] = $item->sku;
            }
        }

        $products = $this->products->getByIdsOrSkus($productIds, $skus);
        $productsById = $products->keyBy('id');
        $productsBySku = $products->keyBy('sku');
        $now = now();
        $itemsToInsert = [];

        foreach ($dto->items as $item) {
            $product = null;

            if ($item->productId) {
                $product = $productsById->get($item->productId);
            } elseif ($item->sku) {
                $product = $productsBySku->get($item->sku);
            }

            if (!$product) {
                throw new RuntimeException('Produto nao encontrado para a venda.');
            }

            $itemsToInsert[] = [
                'product_id' => $product->id,
                'quantity' => $item->quantity,
                'unit_price' => $item->unitPrice,
                'unit_cost' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        return $itemsToInsert;
    }

    public function getTotalAmount(Sale $sale): float
    {
        return $sale->items->sum(function ($item) {
            return $item->unit_price * $item->quantity;
        });
    }

    public function getTotalCost(Sale $sale): float
    {
        return $sale->items->sum(function ($item) {
            return $item->unit_cost * $item->quantity;
        });
    }

    public function getTotalProfit(Sale $sale): float
    {
        return $this->getTotalAmount($sale) - $this->getTotalCost($sale);
    }

    public function updateTotalsAndStatus(Sale $sale, array $totals, string $status): Sale
    {
        return $this->sales->updateTotalsAndStatus($sale, $totals, $status);
    }
}
