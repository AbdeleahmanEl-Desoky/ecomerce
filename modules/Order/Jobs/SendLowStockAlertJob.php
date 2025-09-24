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
use Illuminate\Database\Eloquent\Collection;
use Modules\Order\Mail\LowStockAlertMail;

class SendLowStockAlertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $timeout = 30;

    public function __construct(
        public Collection $lowStockProducts
    ) {
        $this->onQueue('alerts');
    }

    /**
     * Execute the job
     */
    public function handle(): void
    {
        try {
            Log::info("Sending low stock alert for {$this->lowStockProducts->count()} products");

            $adminEmail = config('mail.admin_email', 'admin@example.com');
            
            Mail::to($adminEmail)
                ->send(new LowStockAlertMail($this->lowStockProducts));

            Log::info("Low stock alert sent successfully");

        } catch (\Exception $e) {
            Log::error("Failed to send low stock alert: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Low stock alert job failed: " . $exception->getMessage());
    }
}
