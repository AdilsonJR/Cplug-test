<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class CreateSaleDTO
{
    /** @var SaleItemDTO[] */
    public array $items;

    /**
     * @param SaleItemDTO[] $items
     */
    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public static function fromRequest(Request $request): self
    {
        $items = [];

        foreach ($request->input('items', []) as $item) {
            $items[] = new SaleItemDTO(
                $item['product_id'] ?? null,
                $item['sku'] ?? null,
                (int) $item['quantity'],
                (string) $item['unit_price']
            );
        }

        return new self($items);
    }

    public function itemsToArray(): array
    {
        return array_map(function (SaleItemDTO $item) {
            return $item->toArray();
        }, $this->items);
    }
}
