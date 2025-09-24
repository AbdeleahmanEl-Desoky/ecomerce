<?php

declare(strict_types=1);

namespace Modules\Order\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Modules\Order\Events\OrderCancelled;
use Modules\Order\Jobs\UpdateInventoryJob;

class HandleOrderCancelled implements ShouldQueue
{
    use InteractsWithQueue;

    public $queue = 'events';

    /**
     * Handle the event
     */
    public function handle(OrderCancelled $event): void
    {
        Log::info("Processing OrderCancelled event for order: {$event->order->order_number}");

        // Dispatch inventory update job to release stock
        UpdateInventoryJob::dispatch($event->order, 'increase')
            ->delay(now()->addSeconds(10));

        Log::info("OrderCancelled event processed successfully for order: {$event->order->order_number}");
    }

    /**
     * Handle a job failure
     */
    public function failed(OrderCancelled $event, \Throwable $exception): void
    {
        Log::error("Failed to handle OrderCancelled event for order {$event->order->order_number}: " . $exception->getMessage());
    }
}
