<?php

namespace App\Jobs;

use App\Models\Sale;
use App\Services\InventoryService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Exception;

class UpdateInventoryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(private Sale $sale)
    {
    }

    public function handle(InventoryService $inventoryService): void
    {
        try {
            DB::beginTransaction();

            $lockedItems = $inventoryService->loadProductAndInventoryWithLockForUpdate(
                $this->sale->items->pluck('id')->toArray()
            );

            $lockedItems->each(function ($item) use ($inventoryService) {
                $quantitySold = $item->quantity;
                $quantityAvailable = $item->product->getAvailableInventoryQuantity();
                $newQuantity = $quantityAvailable - $quantitySold;

                $inventoryService->updateQuantity((int) $item->product_id, $newQuantity);
                $inventoryService->refreshProductCache((int) $item->product_id);
            });

            DB::commit();
        }catch (Exception $e) {
            DB::rollBack();
            if ($this->attempts() <= config('queue.update_inventory_max_attempt')) {
                $this->release(FibonacciSequence::calculate($this->attempts()));
                return;
            }
            throw $e;
        }
    }
}
