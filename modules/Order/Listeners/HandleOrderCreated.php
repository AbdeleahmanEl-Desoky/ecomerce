<?php

declare(strict_types=1);

namespace Modules\Order\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Modules\Order\Events\OrderCreated;
use Modules\Order\Jobs\SendOrderConfirmationJob;
use Modules\Order\Jobs\UpdateInventoryJob;

class HandleOrderCreated implements ShouldQueue
{
    use InteractsWithQueue;

    public $queue = 'events';

    /**
     * Handle the event
     */
    public function handle(OrderCreated $event): void
    {
        Log::info("Processing OrderCreated event for order: {$event->order->order_number}");

        // Dispatch confirmation email job
        SendOrderConfirmationJob::dispatch($event->order);

        // Dispatch inventory update job with delay
        UpdateInventoryJob::dispatch($event->order, 'decrease')
            ->delay(now()->addMinutes(1));

        Log::info("OrderCreated event processed successfully for order: {$event->order->order_number}");
    }

    /**
     * Handle a job failure
     */
    public function failed(OrderCreated $event, \Throwable $exception): void
    {
        Log::error("Failed to handle OrderCreated event for order {$event->order->order_number}: " . $exception->getMessage());
    }
}
