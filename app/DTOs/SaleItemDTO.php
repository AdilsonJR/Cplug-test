<?php

namespace App\DTOs;

class SaleItemDTO
{
    public ?int $productId;
    public ?string $sku;
    public int $quantity;
    public string $unitPrice;

    public function __construct(?int $productId, ?string $sku, int $quantity, string $unitPrice)
    {
        $this->productId = $productId;
        $this->sku = $sku;
        $this->quantity = $quantity;
        $this->unitPrice = $unitPrice;
    }

    public function toArray(): array
    {
        return [
            'product_id' => $this->productId,
            'sku' => $this->sku,
            'quantity' => $this->quantity,
            'unit_price' => $this->unitPrice,
        ];
    }
}
