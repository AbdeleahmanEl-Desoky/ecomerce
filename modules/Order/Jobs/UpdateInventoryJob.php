<?php

declare(strict_types=1);

namespace Modules\Order\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Modules\Order\Models\Order;
use Modules\Order\Services\StockManagementService;

class UpdateInventoryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    public function __construct(
        public Order $order,
        public string $action = 'decrease' // 'decrease' or 'increase'
    ) {
        $this->onQueue('inventory');
    }

    /**
     * Execute the job
     */
    public function handle(StockManagementService $stockService): void
    {
        try {
            Log::info("Processing inventory update for order: {$this->order->order_number}, action: {$this->action}");

            if ($this->action === 'decrease') {
                $this->processStockDecrease($stockService);
            } elseif ($this->action === 'increase') {
                $this->processStockIncrease($stockService);
            }

            Log::info("Inventory update completed for order: {$this->order->order_number}");

        } catch (\Exception $e) {
            Log::error("Failed to update inventory for order {$this->order->order_number}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Process stock decrease (after payment confirmation)
     */
    private function processStockDecrease(StockManagementService $stockService): void
    {
        foreach ($this->order->orderItems as $item) {
            Log::info("Stock decreased for product {$item->product_id}: -{$item->quantity}");
        }
        
        $lowStockProducts = $stockService->getLowStockProducts(10);
        if ($lowStockProducts->count() > 0) {
            SendLowStockAlertJob::dispatch($lowStockProducts);
        }
    }

    /**
     * Process stock increase (when order is cancelled)
     */
    private function processStockIncrease(StockManagementService $stockService): void
    {
        $stockService->releaseStock($this->order);
        
        foreach ($this->order->orderItems as $item) {
            Log::info("Stock increased for product {$item->product_id}: +{$item->quantity}");
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Inventory update job failed for order {$this->order->order_number}: " . $exception->getMessage());
        
    }
}
