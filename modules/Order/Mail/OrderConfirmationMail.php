<?php

declare(strict_types=1);

namespace Modules\Order\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Modules\Order\Models\Order;

class OrderConfirmationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Order $order,
        public bool $isAdminNotification = false
    ) {
    }

    /**
     * Build the message
     */
    public function build()
    {
        $subject = $this->isAdminNotification 
            ? "New Order Received: {$this->order->order_number}"
            : "Order Confirmation: {$this->order->order_number}";

        return $this->subject($subject)
                    ->view('emails.order-confirmation')
                    ->with([
                        'order' => $this->order,
                        'isAdmin' => $this->isAdminNotification,
                        'customer' => $this->order->user,
                        'items' => $this->order->orderItems->load('product'),
                        'subtotal' => $this->order->subtotal_amount,
                        'discount' => $this->order->discount_amount,
                        'total' => $this->order->total_amount,
                    ]);
    }
}
