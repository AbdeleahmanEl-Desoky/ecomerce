<?php

declare(strict_types=1);

namespace Modules\Order\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Collection;

class LowStockAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Collection $lowStockProducts
    ) {
    }

    /**
     * Build the message
     */
    public function build()
    {
        return $this->subject('Low Stock Alert - Immediate Action Required')
                    ->view('emails.low-stock-alert')
                    ->with([
                        'products' => $this->lowStockProducts,
                        'totalProducts' => $this->lowStockProducts->count(),
                        'criticalProducts' => $this->lowStockProducts->where('stock_quantity', '<=', 5),
                    ]);
    }
}
