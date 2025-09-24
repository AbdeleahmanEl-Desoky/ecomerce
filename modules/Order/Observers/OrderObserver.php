<?php

declare(strict_types=1);

namespace Modules\Order\Observers;

use Modules\Order\Models\Order;
use Modules\Order\Services\OrderNumberService;
use Modules\Order\Events\OrderCreated;
use Modules\Order\Events\OrderCancelled;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    public function __construct(
        private OrderNumberService $orderNumberService
    ) {
    }

    /**
     * Handle the Order "creating" event.
     */
    public function creating(Order $order): void
    {
        // Generate order number if not provided
        if (empty($order->order_number)) {
            $order->order_number = $this->orderNumberService->generateOrderNumber();
        }
    }

    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        // Log order creation
        Log::info("Order created: {$order->order_number}");
        
        // Dispatch OrderCreated event
        OrderCreated::dispatch($order);
    }

    /**
     * Handle the Order "updating" event.
     */
    public function updating(Order $order): void
    {
        // Prevent order number changes
        if ($order->isDirty('order_number') && $order->getOriginal('order_number')) {
            $order->order_number = $order->getOriginal('order_number');
        }
        
        // Check if order is being cancelled
        if ($order->isDirty('status') && $order->status === 'cancelled') {
            Log::info("Order being cancelled: {$order->order_number}");
        }
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        // Check if order was cancelled and dispatch event
        if ($order->wasChanged('status') && $order->status === 'cancelled') {
            OrderCancelled::dispatch($order);
        }
    }

    /**
     * Handle the Order "deleting" event.
     */
    public function deleting(Order $order): void
    {
        // Log order deletion
        Log::warning("Order being deleted: {$order->order_number}");
    }
}
