<?php

declare(strict_types=1);

namespace Modules\Order\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Modules\Order\Models\Order;
use Modules\Order\Mail\OrderConfirmationMail;

class SendOrderConfirmationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 60;
    public $backoff = [10, 30, 60];

    public function __construct(
        public Order $order
    ) {
        $this->onQueue('emails');
    }

    /**
     * Execute the job
     */
    public function handle(): void
    {
        try {
            Log::info("Sending order confirmation email for order: {$this->order->order_number}");

            // Send confirmation email to customer
            if ($this->order->user && $this->order->user->email) {
                Mail::to($this->order->user->email)
                    ->send(new OrderConfirmationMail($this->order));
                
                Log::info("Order confirmation email sent successfully for order: {$this->order->order_number}");
            }

            // Send notification to admin
            $adminEmail = config('mail.admin_email', 'admin@example.com');
            Mail::to($adminEmail)
                ->send(new OrderConfirmationMail($this->order, true));

        } catch (\Exception $e) {
            Log::error("Failed to send order confirmation email for order {$this->order->order_number}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Order confirmation job failed for order {$this->order->order_number}: " . $exception->getMessage());
    }
}
