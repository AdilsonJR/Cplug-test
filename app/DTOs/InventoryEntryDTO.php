<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class InventoryEntryDTO
{
    public ?int $productId;
    public ?string $sku;
    public int $quantity;
    public string $costPrice;

    public function __construct(?int $productId, ?string $sku, int $quantity, string $costPrice)
    {
        $this->productId = $productId;
        $this->sku = $sku;
        $this->quantity = $quantity;
        $this->costPrice = $costPrice;
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            $request->input('product_id'),
            $request->input('sku'),
            (int) $request->input('quantity'),
            (string) $request->input('cost_price')
        );
    }
}
