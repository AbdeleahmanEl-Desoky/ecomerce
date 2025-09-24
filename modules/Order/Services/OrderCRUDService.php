<?php

declare(strict_types=1);

namespace Modules\Order\Services;

use Illuminate\Support\Collection;
use Modules\Order\DTO\CreateOrderDTO;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderItem;
use Modules\Order\Repositories\OrderRepository;
use Modules\Order\Services\StockManagementService;
use Modules\Order\Services\OrderCalculationService;
use Modules\Order\Services\OrderStatusService;
use Modules\Order\Exceptions\InsufficientStockException;
use Modules\Order\Jobs\SendOrderConfirmationJob;
use Modules\Order\Jobs\UpdateInventoryJob;
use Ramsey\Uuid\UuidInterface;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderCRUDService
{
    public function __construct(
        private OrderRepository $repository,
        private StockManagementService $stockService,
        private OrderCalculationService $calculationService,
        private OrderStatusService $statusService,
    ) {
    }

    public function create(CreateOrderDTO $createOrderDTO): Order
    {
        $itemsArray = array_map(function ($itemDTO) {
            return [
                'product_id' => $itemDTO->product_id->toString(),
                'quantity' => $itemDTO->quantity,
                'price_at_time' => $itemDTO->price_at_time,
            ];
        }, $createOrderDTO->items);
        
        $stockIssues = $this->stockService->checkStockAvailability($itemsArray);
        if (!empty($stockIssues)) {
            throw new InsufficientStockException($stockIssues);
        }
        
        return DB::transaction(function () use ($createOrderDTO, $itemsArray) {
            $this->stockService->reserveStock($itemsArray);
            
            $amounts = $this->calculationService->calculateAmountsFromArray($itemsArray);
            
            $order = $this->repository->createOrder([
                'user_id' => $createOrderDTO->user_id->toString(),
                'subtotal_amount' => $amounts['subtotal_amount'],
                'discount_amount' => $amounts['discount_amount'],
                'total_amount' => $amounts['total_amount'],
                'notes' => $createOrderDTO->notes,
                'status' => 'pending',
            ]);
            
            foreach ($createOrderDTO->items as $itemDTO) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $itemDTO->product_id->toString(),
                    'quantity' => $itemDTO->quantity,
                    'price_at_time' => $itemDTO->price_at_time,
                ]);
            }
            
            // Load the order with its items
            $order->load(['orderItems.product', 'user']);
            
            // Dispatch async jobs
            $this->dispatchOrderJobs($order);
            
            return $order;
        });
    }

    public function list(int $page = 1, int $perPage = 10): array
    {
        return $this->repository->paginated(
            page: $page,
            perPage: $perPage,
        );
    }

    public function get(UuidInterface $id): Order
    {
        return $this->repository->getOrder(
            id: $id,
        );
    }
    
    /**
     * Cancel order and release stock
     */
    public function cancelOrder(Order $order): bool
    {
        if (!$this->statusService->canBeCancelled($order)) {
            return false;
        }
        
        return DB::transaction(function () use ($order) {
            // Release stock back to products
            $this->stockService->releaseStock($order);
            
            // Cancel the order using status service
            $cancelled = $this->statusService->cancelOrder($order);
            
            if ($cancelled) {
                // Dispatch inventory update job for stock release
                UpdateInventoryJob::dispatch($order, 'increase')
                    ->delay(now()->addSeconds(5));
            }
            
            return $cancelled;
        });
    }
    
    /**
     * Update order status
     */
    public function updateOrderStatus(Order $order, string $newStatus): bool
    {
        return $this->statusService->updateStatus($order, $newStatus);
    }
    
    /**
     * Recalculate order totals
     */
    public function recalculateOrderTotals(Order $order): Order
    {
        return $this->calculationService->recalculateOrderTotals($order);
    }
    
    /**
     * Get stock status for dashboard
     */
    public function getStockStatus(): array
    {
        return [
            'low_stock_products' => $this->stockService->getLowStockProducts(),
            'out_of_stock_products' => $this->stockService->getOutOfStockProducts(),
            'low_stock_count' => $this->stockService->getLowStockProducts()->count(),
            'out_of_stock_count' => $this->stockService->getOutOfStockProducts()->count(),
        ];
    }
    
    /**
     * Dispatch async jobs for order processing
     */
    private function dispatchOrderJobs(Order $order): void
    {
        // Send order confirmation email (immediate)
        SendOrderConfirmationJob::dispatch($order);
        
        // Update inventory after a short delay (to ensure order is fully processed)
        UpdateInventoryJob::dispatch($order, 'decrease')
            ->delay(now()->addMinutes(2));
    }
}
