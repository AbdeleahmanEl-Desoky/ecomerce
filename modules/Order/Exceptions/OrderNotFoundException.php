<?php

declare(strict_types=1);

namespace Modules\Order\Exceptions;

use App\Exceptions\BaseModuleException;

class OrderNotFoundException extends BaseModuleException
{
    protected string $errorType = 'ORDER_NOT_FOUND';
    protected string $errorCode = 'E404_ORDER_NOT_FOUND';
    protected int $statusCode = 404;

    public function __construct(string $orderId = '')
    {
        $message = $orderId 
            ? "Order with ID '{$orderId}' not found"
            : 'Order not found';

        $details = [
            'order_id' => $orderId,
            'suggestion' => 'Please check the order ID and try again'
        ];

        parent::__construct($message, $details);
    }
}
