<?php

namespace App\Jobs;

use App\Models\Sale;
use FFI\Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use App\Services\SaleItemsService;
use App\Services\SaleService;

class ProcessSaleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private int $saleId
    ) {}

    public function handle(
        SaleItemsService $saleItemsService,
        SaleService $saleService
    ): void {
        try {
            DB::beginTransaction();

            $lockedSale = $saleService->getLockedSaleById($this->saleId);

            if (!$lockedSale->isPending()) {
                $this->delete();
                return;
            }

            $saleItemsService->updateUnitCost($lockedSale);

            $saleService->updateTotalsAndStatus($lockedSale, [
                'total_amount' => $saleService->getTotalAmount($lockedSale),
                'total_cost' => $saleService->getTotalCost($lockedSale),
                'total_profit' => $saleService->getTotalProfit($lockedSale),
            ], 'processed');
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            if ($this->attempts() <= config('queue.sales_max_attempt')) {
                $this->release(FibonacciSequence::calculate($this->attempts()));
                return;
            }
            throw $e;
        }
    }
}
